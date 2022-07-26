<?php
namespace verbb\doxter\fields\data;

use verbb\doxter\Doxter;

use Craft;

use Twig\Markup;

class DoxterData extends Markup
{
    // Public Methods
    // =========================================================================

    protected $raw;
    protected $html;


    // Public Methods
    // =========================================================================

    public function __construct($raw)
    {
        $this->raw = $raw;
        $this->html = Doxter::$plugin->getService()->parse($raw);

        parent::__construct($this->html, Craft::$app->charset);
    }

    public function __toString()
    {
        return $this->getRaw();
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

    public function getToc(array $options = [])
    {
        return Doxter::$plugin->getService()->parseToc($this->raw, $options);
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
        if (!empty($options)) {
            $this->html = Doxter::$plugin->getService()->parse($this->raw, $options);
        }

        return $this->html;
    }
}
