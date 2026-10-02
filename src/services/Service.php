<?php
namespace verbb\doxter\services;

use verbb\doxter\Doxter;
use verbb\doxter\common\parsers\Toc;
use verbb\doxter\common\parsers\Header;
use verbb\doxter\common\parsers\Markdown;
use verbb\doxter\common\parsers\CodeBlock;
use verbb\doxter\common\parsers\Shortcode;
use verbb\doxter\common\parsers\Typography;
use verbb\doxter\common\parsers\ReferenceTag;
use verbb\doxter\events\DoxterEvent;
use verbb\doxter\models\Settings;

use Craft;
use craft\base\Component;
use craft\helpers\ArrayHelper;
use craft\helpers\FileHelper;
use craft\helpers\HtmlPurifier;
use craft\helpers\Template;
use craft\web\View;

use yii\base\Exception;

use Spatie\YamlFrontMatter\YamlFrontMatter;

use Twig\Markup;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;
use Twig\Error\LoaderError;

class Service extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_TYPOGRAPHY = 'beforeTypography';
    public const EVENT_BEFORE_HEADER_PARSE = 'beforeHeaderParsing';
    public const EVENT_BEFORE_MARKDOWN_PARSE = 'beforeMarkdownParsing';
    public const EVENT_BEFORE_SHORTCODE_PARSE = 'beforeShortcodeParsing';
    public const EVENT_BEFORE_CODEBLOCK_PARSE = 'beforeCodeBlockParsing';
    public const EVENT_BEFORE_REFERENCETAG_PARSE = 'beforeReferenceTagParsing';
    public const EVENT_AFTER_PARSE = 'afterParsing';


    // Public Methods
    // =========================================================================

    /**
     * Parses source markdown into valid html using various rules and parsers
     *
     * @param string|null $source The markdown source to parse
     * @param array $options Passed in parameters via a template filter call
     *
     * @return Markup
     */
    public function parse(?string $source = null, array $options = []): Markup
    {
        if (!$this->canBeSafelyParsed($source)) {
            return new Markup('', Craft::$app->charset);
        }

        $options = array_merge(Doxter::$plugin->getSettings()->getAttributes(), $options);
        $codeBlockSnippet = $options['codeBlockSnippet'] ?? null;
        $addHeaderAnchorsTo = $options['addHeaderAnchorsTo'] ?? null;
        $startingHeaderLevel = $options['startingHeaderLevel'] ?? null;
        $addTypographyHyphenation = $options['addTypographyHyphenation'] ?? null;

        // Parsing reference tags first so that we can parse markdown within them
        if ($options['parseReferenceTags'] ?? false) {
            $source = $this->_triggerBeforeParseEvent(self::EVENT_BEFORE_REFERENCETAG_PARSE, $source);

            $source = $this->parseReferenceTags($source, $options);
        }

        if ($options['parseShortcodes'] ?? false) {
            $source = $this->_triggerBeforeParseEvent(self::EVENT_BEFORE_SHORTCODE_PARSE, $source);

            $source = $this->parseShortcodes($source);
        }

        $source = $this->_triggerBeforeParseEvent(self::EVENT_BEFORE_MARKDOWN_PARSE, $source);

        $source = $this->parseMarkdown($source);

        $source = $this->_triggerBeforeParseEvent(self::EVENT_BEFORE_CODEBLOCK_PARSE, $source);

        $source = $this->parseCodeBlocks($source, compact('codeBlockSnippet'));

        if ($options['addHeaderAnchors'] ?? false) {
            $source = $this->_triggerBeforeParseEvent(self::EVENT_BEFORE_HEADER_PARSE, $source);

            $source = $this->parseHeaders($source, compact('addHeaderAnchorsTo', 'startingHeaderLevel'));
        }

        if ($options['addTypographyStyles']) {
            $source = $this->_triggerBeforeParseEvent(self::EVENT_BEFORE_TYPOGRAPHY, $source);

            $source = $this->parseTypography($source, compact('addTypographyHyphenation'));
        }

        $source = Doxter::$plugin->getService()->decodeUnicodeEntities($source);

        // Create an event so we can update the source from it later
        $event = new DoxterEvent(compact('source'));

        $this->trigger(self::EVENT_AFTER_PARSE, $event);

        // Keep the final extension point inside the trust boundary so listeners cannot reintroduce unsafe markup.
        $source = $this->purifyHtml($event->source, $options);

        return Template::raw($source);
    }

    /**
     * Applies Doxter's output trust policy to generated HTML.
     *
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function purifyHtml(string $source, array $options = []): string
    {
        $options = array_merge(Doxter::$plugin->getSettings()->getAttributes(), $options);
        $purifierConfig = array_replace(
            Settings::DEFAULT_PURIFIER_CONFIG,
            $options['purifierConfig'] ?? [],
        );

        if ($options['allowUnsafeHtml'] ?? false) {
            return $source;
        }

        return HtmlPurifier::process($source, $purifierConfig);
    }

    /**
     * Parses Markdown and front matter from a file within the Doxter template directory.
     */
    public function parseFile(string $slug, array $options = []): ?array
    {
        if (!$this->_isValidFileSlug($slug)) {
            return null;
        }

        $root = realpath(Craft::$app->path->getSiteTemplatesPath() . '/_doxter');

        if ($root === false) {
            return null;
        }

        $source = $this->_readContainedFile($root, $slug . '.md');

        if ($source === null) {
            return null;
        }

        $md = YamlFrontMatter::parse($source);

        return array_merge($md->matter(), [
            'body' => $this->parse($md->body(), $options),
        ]);
    }

    public function parseToc(?string $source = null, array $options = []): array
    {
        return Toc::instance()->parse($source ?? '', $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseMarkdown(string $source, array $options = []): string
    {
        return Markdown::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parseMarkdownInline(string $source): string
    {
        return Markdown::instance()->parseInline($source);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseReferenceTags(string $source, array $options = []): string
    {
        return ReferenceTag::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseHeaders(string $source, array $options = []): string
    {
        return Header::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseCodeBlocks(string $source, array $options = []): string
    {
        return CodeBlock::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseShortcodes(string $source, array $options = []): string
    {
        return Shortcode::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseTypography(string $source, array $options = []): string
    {
        return Typography::instance()->parse($source, $options);
    }

    /**
     * Ensures that a valid list of parseable headers is returned
     *
     * @param string $headerString
     *
     * @return array
     */
    public function getHeadersToParse(string $headerString = ''): array
    {
        $allowedHeaders = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

        $headers = ArrayHelper::filterEmptyStringsFromArray(ArrayHelper::toArray($headerString));

        if (count($headers)) {
            foreach ($headers as $key => $header) {
                $header = strtolower($header);

                if (!in_array($header, $allowedHeaders)) {
                    unset($headers[$key]);
                }
            }
        }

        return $headers;
    }

    /**
     * @param string $template
     * @param array $vars
     *
     * @return string|null
     *
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderPluginTemplate(string $template, array $vars = []): ?string
    {
        $view = Craft::$app->getView();

        $rendered = null;
        $template = sprintf('doxter/%s', $template);
        $oldMode = $view->getTemplateMode();

        $view->setTemplateMode(View::TEMPLATE_MODE_CP);

        if ($view->doesTemplateExist($template)) {
            $rendered = $view->renderTemplate($template, $vars);
        }

        $view->setTemplateMode($oldMode);

        return $rendered;
    }

    /**
     * @param array $shortcodes
     */
    public function registerShortcodes(array $shortcodes): void
    {
        Shortcode::instance()->registerShortcodes($shortcodes);
    }

    /**
     * @param $shortcode
     * @param $callback
     */
    public function registerShortcode($shortcode, $callback): void
    {

        Shortcode::instance()->registerShortcode($shortcode, $callback);
    }

    /**
     * Decodes html entities starting with &#x generally associated with emoji
     * Handles emoji within code blocks that are in the &amp;#x format
     *
     * @param $value
     *
     * @return string|string[]|null
     */
    public function decodeUnicodeEntities($value): array|string|null
    {
        return preg_replace_callback('/((\&\#x[a-z\d]+\;)|(\&amp\;\#x[a-z\d]+\;))/i', function($matches) {
            return html_entity_decode($matches[1], ENT_HTML5, Craft::$app->charset);
        }, $value);
    }

    /**
     * Reports whether the source string can be safely parsed
     *
     * @param mixed|null $source
     *
     * @return bool
     */
    public function canBeSafelyParsed(mixed $source = null): bool
    {
        if (empty($source)) {
            return false;
        }

        return (is_string($source) || is_callable([$source, '__toString']));
    }


    // Private Methods
    // =========================================================================

    private function _triggerBeforeParseEvent(string $eventName, string $source): string
    {
        $event = new DoxterEvent(compact('source'));

        $this->trigger($eventName, $event);

        return $event->source;
    }

    private function _isValidFileSlug(string $slug): bool
    {
        if (
            $slug === '' ||
            str_contains($slug, "\0") ||
            str_contains($slug, '\\') ||
            str_starts_with($slug, '/') ||
            preg_match('/^[A-Za-z]:/', $slug) === 1
        ) {
            return false;
        }

        return !in_array('..', explode('/', $slug), true);
    }

    /**
     * Reads from the same file handle whose canonical location and identity are verified.
     */
    private function _readContainedFile(string $root, string $relativePath): ?string
    {
        $candidate = $root . DIRECTORY_SEPARATOR . $relativePath;
        $handle = @fopen($candidate, 'rb');

        if ($handle === false) {
            return null;
        }

        try {
            clearstatcache(true, $candidate);
            $file = realpath($candidate);

            if ($file === false || !FileHelper::isWithin($file, $root)) {
                return null;
            }

            clearstatcache(true, $file);
            $openStat = fstat($handle);
            $fileStat = @stat($file);

            if (
                $openStat === false ||
                $fileStat === false ||
                $openStat['dev'] !== $fileStat['dev'] ||
                $openStat['ino'] !== $fileStat['ino'] ||
                ($openStat['mode'] & 0170000) !== 0100000
            ) {
                return null;
            }

            $contents = stream_get_contents($handle);

            return $contents === false ? null : $contents;
        } finally {
            fclose($handle);
        }
    }
}
