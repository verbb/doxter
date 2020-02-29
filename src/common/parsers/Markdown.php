<?php

namespace selvinortiz\doxter\common\parsers;

use cebe\markdown\Parser;

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
     * @var Parser
     */
    protected static $_parser;

    public function __construct($parser = null)
    {
        self::$_parser = $parser ? $parser : new \cebe\markdown\GithubMarkdown();
    }

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parse($source, array $options = [])
    {
        return static::$_parser->parse($source);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parseInline($source)
    {
        return static::$_parser->parseParagraph($source);
    }
}
