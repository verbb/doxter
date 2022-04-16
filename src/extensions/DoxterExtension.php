<?php
namespace selvinortiz\doxter\extensions;

use Craft;
use craft\helpers\Template;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

use selvinortiz\doxter\common\parsers\Typography;
use Twig\Markup;

use function selvinortiz\doxter\doxter;

/**
 * Class DoxterTwigExtension
 *
 * @package Craft
 */
class DoxterExtension extends AbstractExtension
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

        if (is_object($source) && $source instanceof Markup)
        {
            return new Markup($parsed, Craft::$app->charset);
        }

        return $parsed;
    }

    /**
     * Convert markdown and front matter from a file to structured entry
     *
     * @param string $slug    The slug string that implements __toString
     * @param array  $options Filter arguments passed in from twig
     *
     * @return string|null The parsed string or null if not a valid slug
     */
    public function doxterFile($slug = '', array $options = [])
    {
        return doxter()->api->parseFile($slug, $options);
    }

    /**
     * @param string $source
     *
     * @return \Twig\Markup
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
            new TwigFilter('doxter', [$this, 'doxter']),
            new TwigFilter('doxterFile', [$this, 'doxterFile']),
            new TwigFilter('doxterTypography', [$this, 'doxterTypography'])
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('doxter', [$this, 'doxter']),
            new TwigFunction('doxterFile', [$this, 'doxterFile']),
            new TwigFunction('doxterTypography', [$this, 'doxterTypography'])
        ];
    }
}
