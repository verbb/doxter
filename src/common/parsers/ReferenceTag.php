<?php
namespace verbb\doxter\common\parsers;

use Craft;
use craft\base\ElementInterface;
use craft\errors\SiteNotFoundException;
use craft\helpers\StringHelper;
use craft\services\Elements;

use yii\base\Exception;

use Throwable;

class ReferenceTag extends BaseParser
{
    // Constants
    // =========================================================================

    private const MAX_REFERENCE_DEPTH = 32;
    private const MAX_REFERENCE_EXPANSIONS = 256;

    // Craft exposed its reference-tag pattern publicly in 5.10; retain the same syntax for Doxter's 5.0 floor.
    private const LEGACY_REFERENCE_TAG_PATTERN = '/\{([\w\\\\]+)\:([^@\:\}]+)(?:@([^\:\}]+))?(?:\:([^\}\| ]+))?(?: *\|\| *([^\}]+))?\}/';


    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;

    private array $_activeReferences = [];
    private bool $_didWarn = false;
    private int $_parseLevel = 0;
    private int $_referenceExpansions = 0;


    // Public Methods
    // =========================================================================

    /**
     * Parses reference tags recursively
     *
     * @param string $source
     * @param array $options
     *
     * @return mixed
     */
    public function parse(string $source, array $options = []): mixed
    {
        $isRootParse = $this->_parseLevel === 0;

        if ($isRootParse) {
            $this->_activeReferences = [];
            $this->_didWarn = false;
            $this->_referenceExpansions = 0;
        }

        $this->_parseLevel++;

        try {
            return $this->_parseRefs($source);
        } finally {
            $this->_parseLevel--;

            if ($isRootParse) {
                $this->_activeReferences = [];
                $this->_referenceExpansions = 0;
            }
        }
    }


    // Private Methods
    // =========================================================================

    /**
     * Mirrors Craft's reference resolver while retaining control of recursive expansion.
     */
    private function _parseRefs(string $source): string
    {
        if (!StringHelper::contains($source, '{')) {
            return $source;
        }

        $elementsService = Craft::$app->getElements();
        $sitesService = Craft::$app->getSites();
        $allRefTagTokens = [];
        $referenceTagPattern = defined(Elements::class . '::REF_TAG_PATTERN')
            ? constant(Elements::class . '::REF_TAG_PATTERN')
            : self::LEGACY_REFERENCE_TAG_PATTERN;
        $source = preg_replace_callback(
            $referenceTagPattern,
            function(array $matches) use ($elementsService, $sitesService, &$allRefTagTokens) {
                if (isset($matches['elementType'])) {
                    $fullMatch = $matches[0];
                    $elementType = $matches['elementType'];
                    $ref = $matches['ref'];
                    $siteId = $matches['site'] ?? null;
                    $attribute = $matches['attr'] ?? null;
                    $fallback = $matches['fallback'] ?? $fullMatch;
                } else {
                    $matches = array_pad($matches, 6, null);
                    [$fullMatch, $elementType, $ref, $siteId, $attribute, $fallback] = $matches;
                    $fallback ??= $fullMatch;
                }

                $elementType = $elementsService->getElementTypeByRefHandle($elementType);

                if ($elementType === null) {
                    return $fallback;
                }

                if (!empty($siteId)) {
                    if (is_numeric($siteId)) {
                        $siteId = (int)$siteId;
                    } else {
                        try {
                            if (StringHelper::isUUID($siteId)) {
                                $site = $sitesService->getSiteByUid($siteId);
                            } else {
                                $site = $sitesService->getSiteByHandle($siteId);
                            }
                        } catch (SiteNotFoundException) {
                            $site = null;
                        }

                        if (!$site) {
                            return $fallback;
                        }

                        $siteId = $site->id;
                    }
                }

                $refType = is_numeric($ref) ? 'id' : 'ref';
                $token = '{' . StringHelper::randomString(9) . '}';
                $allRefTagTokens[$siteId][$elementType][$refType][$ref][] = [$token, $attribute, $fallback];

                return $token;
            },
            $source,
            -1,
            $count,
        );

        if ($count === 0) {
            return $source;
        }

        $search = [];
        $replace = [];

        foreach ($allRefTagTokens as $siteId => $siteTokens) {
            foreach ($siteTokens as $elementType => $tokensByType) {
                foreach ($tokensByType as $refType => $tokensByName) {
                    $refNames = array_keys($tokensByName);
                    $elementQuery = $elementsService->createElementQuery($elementType)
                        ->siteId($siteId)
                        ->status(null);

                    if ($refType === 'id') {
                        $elementQuery->id($refNames);
                    } else {
                        $elementQuery->ref($refNames);
                    }

                    $elements = [];

                    foreach ($elementQuery->all() as $element) {
                        $ref = $refType === 'id' ? $element->id : $element->getRef();
                        $elements[$ref] = $element;

                        if ($refType === 'ref' && ($slash = strrpos($ref, '/')) !== false) {
                            $elements[substr($ref, $slash + 1)] ??= $element;
                        }
                    }

                    foreach ($tokensByName as $refName => $tokens) {
                        $element = $elements[$refName] ?? null;

                        foreach ($tokens as [$token, $attribute, $fallback]) {
                            $search[] = $token;
                            $replace[] = $this->_referenceReplacement($element, $attribute, $fallback);
                        }
                    }
                }
            }
        }

        return str_replace($search, $replace, $source);
    }

    private function _referenceReplacement(?ElementInterface $element, ?string $attribute, string $fallback): string
    {
        if ($element === null) {
            return $fallback;
        }

        if (empty($attribute)) {
            return (string)$element->getUrl();
        }

        $referenceKey = $this->_referenceKey($element, $attribute);

        if (
            isset($this->_activeReferences[$referenceKey]) ||
            count($this->_activeReferences) >= self::MAX_REFERENCE_DEPTH ||
            $this->_referenceExpansions >= self::MAX_REFERENCE_EXPANSIONS
        ) {
            $this->_warnAboutReferenceLimit();

            return $fallback;
        }

        // Mark the reference active before property access, because Craft fields can normalize from __isset().
        $this->_activeReferences[$referenceKey] = true;
        $this->_referenceExpansions++;

        try {
            if (!isset($element->$attribute)) {
                return (string)$element->getUrl();
            }

            $value = $element->$attribute;

            if (is_object($value) && !method_exists($value, '__toString')) {
                throw new Exception('Object of class ' . get_class($value) . ' could not be converted to string');
            }

            return $this->_parseRefs((string)$value);
        } catch (Throwable $e) {
            Craft::error('An exception was thrown when parsing a Doxter reference tag: ' . $e->getMessage(), __METHOD__);

            return $fallback;
        } finally {
            unset($this->_activeReferences[$referenceKey]);
        }
    }

    private function _referenceKey(ElementInterface $element, string $attribute): string
    {
        $elementId = $element->getCanonicalId();
        $elementKey = $elementId === null ? 'object-' . spl_object_id($element) : 'id-' . $elementId;

        return implode(':', [
            get_class($element),
            $elementKey,
            (string)($element->siteId ?? ''),
            $attribute,
        ]);
    }

    private function _warnAboutReferenceLimit(): void
    {
        if ($this->_didWarn) {
            return;
        }

        $this->_didWarn = true;
        Craft::warning('Doxter stopped a circular or excessive reference expansion.', __METHOD__);
    }
}
