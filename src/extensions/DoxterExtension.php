<?php
namespace selvinortiz\doxter\extensions;

use \Twig_Extension;
use \Twig_SimpleFilter;

use Craft;
use craft\helpers\Template;
use craft\fields\data\RichTextData;

use selvinortiz\doxter\Doxter;

/**
 * Class DoxterTwigExtension
 *
 * @package Craft
 */
class DoxterExtension extends Twig_Extension {

    /**
     * @return string
     */
    public function getName() {
        return 'Doxter Extension';
    }

    /**
     * The doxter filter converts markdown to html
     *
     * - Handle empty strings safely @link https://github.com/selvinortiz/craft.doxter/issues/5
     * - Handle parseRefs returned value @link https://github.com/selvinortiz/craft.doxter/issues/6
     *
     * @param string $source  The source string or object that implements __toString
     * @param array  $options Filter arguments passed in from twig
     *
     * @return mixed The parsed string or false if not a valid source
     */
    public function doxter($source = '', array $options = []) {
        $parsed = Doxter::$api->parse($source, $options);

        if (is_object($source) && $source instanceof RichTextData) {
            return new RichTextData($parsed, Craft::$app->getView()->twig->getCharset());
        }

        return $parsed;
    }

    public function doxterTypography($source = '') {
        return Template::raw(typogrify($source));
    }

    /**
     * Makes the filters available to the template context
     *
     * @return array
     */
    public function getFilters() {
        return [
            'doxter'           => new Twig_SimpleFilter('doxter', [$this, 'doxter']),
            'doxterTypography' => new Twig_SimpleFilter('doxterTypography', [$this, 'doxterTypography'])
        ];
    }
}
