<?php
namespace selvinortiz\doxter\common\parsers;

use Michelf\MarkdownExtra;

/**
 * The markdown parser
 *
 * Class DoxterCodeBlockParser
 *
 * @package Craft
 */
class Markdown extends BaseParser
{
    /**
     * @var Markdown
     */
    protected static $_instance;

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parse($source, array $options = [])
    {
        return MarkdownExtra::defaultTransform($source);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parseInline($source)
    {
        $source = MarkdownExtra::defaultTransform($source);

        return preg_replace('/^[ ]*\<p\>(.*)\<\/p\>[ ]*$/', '$1', $source);
    }
}
