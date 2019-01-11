<?php
namespace selvinortiz\doxter\fields\data;

use Craft;

use function selvinortiz\doxter\doxter;

/**
 * Represents raw (source) and html (output) for the field type value
 *
 * Class DoxterData
 *
 * @package selvinortiz\doxter\fields\data
 */
class DoxterData extends \Twig_Markup
{
    protected $raw;
    protected $html;

    public function __construct($raw)
    {
        $this->raw = $raw;
        $this->html = doxter()->api->parse($raw);

        parent::__construct($this->html, Craft::$app->charset);
    }

    public function __toString()
    {
        return (string)$this->getRaw();
    }

    /**
     * Returns the field type text (markdown source)
     *
     * @return string
     */
    public function getRaw()
    {
        if (empty($this->raw)) {
            return '';
        }

        return doxter()->api->decodeUnicodeEntities($this->raw);
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
    public function getHtml(array $options = [])
    {
        return $this->parse($options);
    }

    public function getToc(array $options = [])
    {
        return doxter()->api->parseToc($this->raw, $options);
    }

    /**
     * Returns the field type html (parsed output)
     *
     * @param array $options Parsing options if any
     *
     * @return \Twig_Markup
     */
    protected function parse(array $options = [])
    {
        if (!empty($options))
        {
            $this->html = doxter()->api->parse($this->raw, $options);
        }

        return $this->html;
    }
}
