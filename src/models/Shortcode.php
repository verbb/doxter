<?php
namespace verbb\doxter\models;

use verbb\doxter\common\parsers\Shortcode as ShortcodeParser;

use craft\base\Model;

class Shortcode extends Model
{
    // Properties
    // =========================================================================

    public string $name = '';
    public array $params = [];
    public string $content = '';


    // Public Methods
    // =========================================================================

    /**
     * Returns a parameter value if one is found
     * Useful when accessing via twig
     *
     * @param string $param
     *
     * @return mixed|null
     */
    public function __get($param)
    {
        return $this->getParam($param);
    }

    /**
     * Returns a parsed shortcode parameter value if found or $default value
     *
     * @param string $name
     * @param mixed|null $default
     *
     * @return null|mixed
     */
    public function getParam(string $name, mixed $default = null): mixed
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * @return mixed|string
     */
    public function parseContent(): mixed
    {
        if (empty($this->content)) {
            return '';
        }

        if (mb_stripos($this->content, '[') !== false || mb_stripos($this->content, '{') !== false) {
            return ShortcodeParser::instance()->parse($this->content);
        }

        return $this->content;
    }
}
