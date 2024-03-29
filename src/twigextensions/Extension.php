<?php
namespace verbb\doxter\twigextensions;

use verbb\doxter\Doxter;
use verbb\doxter\common\parsers\Typography;

use craft\helpers\Template;
use craft\redactor\FieldData;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use yii\base\Exception;

class Extension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Doxter Extension';
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('doxter', [$this, 'doxter']),
            new TwigFilter('doxterFile', [$this, 'doxterFile']),
            new TwigFilter('doxterTypography', [$this, 'doxterTypography']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('doxter', [$this, 'doxter']),
            new TwigFunction('doxterFile', [$this, 'doxterFile']),
            new TwigFunction('doxterTypography', [$this, 'doxterTypography']),
        ];
    }

    /**
     * The doxter filter converts markdown to html
     *
     * - Handle empty strings safely @link https://github.com/selvinortiz/craft.doxter/issues/5
     * - Handle parseRefs returned value @link https://github.com/selvinortiz/craft.doxter/issues/6
     *
     * @param mixed $source The source string or object that implements __toString
     * @param array $options Filter arguments passed in from twig
     *
     * @return mixed The parsed string or false if not a valid source
     */
    public function doxter(mixed $source = '', array $options = []): mixed
    {
        $parsed = Doxter::$plugin->getService()->parse($source, $options);

        if (is_object($source) && $source instanceof FieldData) {
            return new FieldData($parsed);
        }

        return $parsed;
    }

    /**
     * Convert markdown and front matter from a file to structured entry
     *
     * @param string $slug The slug string that implements __toString
     * @param array $options Filter arguments passed in from twig
     *
     * @return string|null The parsed string or null if not a valid slug
     * @throws Exception
     * @throws Exception
     */
    public function doxterFile(string $slug = '', array $options = []): ?string
    {
        return Doxter::$plugin->getService()->parseFile($slug, $options);
    }

    /**
     * @param string $source
     *
     * @return Markup
     */
    public function doxterTypography(string $source = ''): Markup
    {
        return Template::raw(Typography::instance()->parse($source));
    }
}
