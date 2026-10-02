<?php
namespace verbb\doxter\fields\data;

use verbb\doxter\Doxter;

use Craft;

use Throwable;

use Twig\Markup;

class DoxterData extends Markup
{
    // Properties
    // =========================================================================

    protected ?string $raw = null;

    private array $_htmlCache = [];


    // Public Methods
    // =========================================================================

    public function __construct($raw)
    {
        $this->raw = $raw;

        // Twig's parent stores eager content privately, so implicit consumers are overridden below.
        parent::__construct('', Craft::$app->charset);
    }

    public function __toString(): string
    {
        return (string)$this->getHtml();
    }

    public function count(): int
    {
        return mb_strlen((string)$this->getHtml(), $this->getCharset());
    }

    public function jsonSerialize(): string
    {
        return (string)$this->getHtml();
    }

    /**
     * Returns the field type text (markdown source)
     *
     * @return string
     */
    public function getRaw(): string
    {
        if (empty($this->raw)) {
            return '';
        }

        return Doxter::$plugin->getService()->decodeUnicodeEntities($this->raw);
    }

    /**
     * Alias of parse()
     *
     * @param array $options
     *
     * @return Markup
     * @see parse()
     *
     */
    public function getHtml(array $options = []): Markup
    {
        return $this->parse($options);
    }

    public function getToc(array $options = []): array
    {
        return Doxter::$plugin->getService()->parseToc((string)$this->getHtml($options), $options);
    }


    // Protected Methods
    // =========================================================================

    /**
     * Returns the field type html (parsed output)
     *
     * @param array $options Parsing options if any
     *
     * @return Markup
     */
    protected function parse(array $options = []): Markup
    {
        $templateMode = Craft::$app->getView()->getTemplateMode();

        // Template mode is part of the identity so site-rendered shortcodes never leak into CP output.
        foreach ($this->_htmlCache as $cached) {
            if ($cached['templateMode'] === $templateMode && $cached['options'] === $options) {
                return $cached['html'];
            }
        }

        // Publish a neutral value before invoking extensible parsing so the same value cannot re-enter uncached.
        $html = new Markup('', Craft::$app->charset);
        $this->_htmlCache[] = compact('templateMode', 'options', 'html');
        $cacheIndex = array_key_last($this->_htmlCache);

        try {
            $html = Doxter::$plugin->getService()->parse($this->raw, $options);
            $this->_htmlCache[$cacheIndex]['html'] = $html;
        } catch (Throwable $e) {
            unset($this->_htmlCache[$cacheIndex]);

            throw $e;
        }

        return $html;
    }
}
