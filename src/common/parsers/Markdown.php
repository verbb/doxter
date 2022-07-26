<?php
namespace verbb\doxter\common\parsers;

use cebe\markdown\GithubMarkdown;
use cebe\markdown\Parser;

class Markdown extends BaseParser
{
    // Properties
    // =========================================================================

    /**
     * @var Markdown
     */
    protected static $_instance;

    /**
     * @var Parser
     */
    protected static $_parser;


    // Public Methods
    // =========================================================================

    public function __construct($parser = null)
    {
        self::$_parser = $parser ?: new GithubMarkdown();
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parse(string $source, array $options = []): string
    {
        return static::$_parser->parse($source);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parseInline(string $source): string
    {
        return static::$_parser->parseParagraph($source);
    }
}
