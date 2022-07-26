<?php
namespace verbb\doxter\common\parsers;

use cebe\markdown\GithubMarkdown;
use cebe\markdown\Parser;

class Markdown extends BaseParser
{
    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;
    protected static ?Parser $_parser = null;


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
     * @return mixed
     */
    public function parse(string $source, array $options = []): mixed
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
