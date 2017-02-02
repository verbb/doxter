<?php
namespace selvinortiz\doxter\common\parsers;

/**
 * The markdown parser
 *
 * Class DoxterCodeBlockParser
 *
 * @package Craft
 */
class Markdown extends BaseParser {

    /**
     * @var Markdown
     */
    protected static $_instance;

    /**
     * @var \ParsedownExtra
     */
    protected static $markdownHelper;

    /**
     * @return \ParsedownExtra
     */
    public static function getMarkdownHelper() {
        if (! self::$markdownHelper) {
            self::$markdownHelper = new \ParsedownExtra();
        }

        return self::$markdownHelper;
    }

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parse($source, array $options = []) {
        return self::getMarkdownHelper()->text($source);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parseInline($source) {
        $source = self::getMarkdownHelper()->text($source);

        return preg_replace('/^[ ]*\<p\>(.*)\<\/p\>[ ]*$/', '$1', $source);
    }
}
