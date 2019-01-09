<?php
namespace selvinortiz\doxter\extensions;

use \Twig_Extension;
use \Twig_SimpleFilter;
use \Twig_SimpleFunction;

use Craft;
use craft\helpers\Template;
use craft\redactor\FieldData;

use selvinortiz\doxter\common\parsers\Typography;

use function selvinortiz\doxter\doxter;

/**
 * Class DoxterTwigExtension
 *
 * @package Craft
 */
class DoxterExtension extends Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
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
    public function doxter($source = '', array $options = [])
    {
        $parsed = doxter()->api->parse($source, $options);

        if (is_object($source) && $source instanceof FieldData) {
            return new FieldData($parsed);
        }

        return $parsed;
    }

    /**
     * @param string $source
     *
     * @return \Twig_Markup
     */
    public function doxterTypography($source = '')
    {
        return Template::raw(Typography::instance()->parse($source));
    }

    /**
     * Makes the filters available to the template context
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('doxter', [$this, 'doxter']),
            new Twig_SimpleFilter('doxterTypography', [$this, 'doxterTypography'])
        ];
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('doxter', [$this, 'doxter']),
            new Twig_SimpleFunction('doxterTypography', [$this, 'doxterTypography'])
        ];
    }
}
