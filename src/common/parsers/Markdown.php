<?php
namespace selvinortiz\doxter\common\parsers;

use yii\helpers\Markdown as MarkdownHelper;

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
     * @var \ParsedownExtra
     */
    protected static $_parser;

    public function __construct($parser = null)
    {
        self::$_parser = $parser ? $parser : new \ParsedownExtra();
    }

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parse($source, array $options = [])
    {
        return MarkdownHelper::process($source, 'gfm'); // static::$_parser->text($source);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parseInline($source)
    {
        $source = MarkdownHelper::processParagraph($source, 'gfm'); // static::$_parser->text($source);

        return preg_replace('/^[ ]*\<p\>(.*)\<\/p\>[ ]*$/', '$1', $source);
    }
}
