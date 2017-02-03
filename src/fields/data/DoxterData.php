<?php
namespace selvinortiz\doxter\fields\data;

use Craft;

use selvinortiz\doxter\Doxter;

/**
 * Represents raw (source) and html (output) for the field type value
 *
 * Class DoxterData
 *
 * @package selvinortiz\doxter\fields\data
 */
class DoxterData extends \Twig_Markup {

    protected $raw;
    protected $html;

    public function __construct($raw) {
        $this->raw  = $raw;
        $this->html = Doxter::$api->parse($raw);

        parent::__construct($this->html, Craft::$app->charset);
    }

    /**
     * Returns the field type text (markdown source)
     *
     * @return string
     */
    public function getRaw() {
        return ! empty($this->raw) ? $this->raw : '';
    }

    /**
     * Alias of parse()
     *
     * @see parse()
     *
     * @param array $options
     *
     * @return \Twig_Markup
     */
    public function getHtml(array $options = []) {
        return $this->parse($options);
    }

    /**
     * Returns the field type html (parsed output)
     *
     * @param array $options Parsing options if any
     *
     * @return \Twig_Markup
     */
    public function parse(array $options = []) {
        if (! empty($options)) {
            $this->html = Doxter::getInstance()->service->parse($this->raw, $options);
        }

        return $this->html;
    }
}
