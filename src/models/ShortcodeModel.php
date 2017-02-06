<?php
namespace selvinortiz\doxter\models;

use craft\base\Model;

use selvinortiz\doxter\common\parsers\Shortcode;

/**
 * Models the properties of a parsed shortcode
 *
 * Class DoxterShortcodeModel
 *
 * @package selvinortiz\doxter\models
 *
 * @property string $name
 * @property array  $params
 * @property string $content
 *
 * @example
 * [image src="/images/image.jpg" alt="My Image"/]
 *
 * {
 *  name: "image",
 *  params: {src: "/images/image.jpg", alt: "My Image"},
 *  content: ""
 * }
 *
 * [updates]
 *  [note type="added"]Added a very cool feature[/note]
 * [/updates]
 *
 * {
 *  name: "updates"
 *  params: {},
 *  content: "[note type=\"added\"]Added a very cool feature[/note]"
 * }
 */
class ShortcodeModel extends Model {

    public $name;
    public $params;
    public $content;

    /**
     * Returns the value of an attribute if found or $default value
     *
     * @param string     $attribute
     * @param null|mixed $default
     *
     * @return null|mixed
     */
    public function get($attribute, $default = null) {
        return isset($this->{$attribute}) ? $this->{$attribute} : $default;
    }

    /**
     * Returns a parsed shortcode parameter value if found or $default value
     *
     * @param string     $name
     * @param null|mixed $default
     *
     * @return null|mixed
     */
    public function getParam($name, $default = null) {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }

    public function parseContent() {
        if (! empty($this->content)) {
            if (strpos($this->content, '[') !== false || strpos($this->content, '{') !== false) {
                return Shortcode::instance()->parse($this->content);
            }

            return $this->content;
        }
    }
}
