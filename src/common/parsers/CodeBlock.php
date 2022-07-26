<?php
namespace verbb\doxter\common\parsers;

class CodeBlock extends BaseParser
{
    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;
    protected static ?string $codeBlockSnippet = null;


    // Public Methods
    // =========================================================================

    /**
     * Parses code blocks within html content and returns a new code block markup based on settings
     *
     * @param string $source
     * @param array $options
     *
     * @return mixed
     */
    public function parse(string $source, array $options = []): mixed
    {
        $codeBlockSnippet = $options['codeBlockSnippet'] ?? null;

        if (empty($codeBlockSnippet)) {
            return $source;
        }

        self::$codeBlockSnippet = $codeBlockSnippet;

        return $this->parseCodeBlocks($source);
    }


    // Protected Methods
    // =========================================================================

    /**
     * Parses code blocks within html source
     *
     * @param string $source
     *
     * @return string
     */
    protected function parseCodeBlocks(string $source): string
    {
        if (!$this->canBeSafelyParsed($source) || stripos($source, '<pre>') === false) {
            return $source;
        }

        $pattern = '/<pre>(.?)<code class\="([a-z\-\_]+)">(.*?)<\/code>(.?)<\/pre>/ism';
        return preg_replace_callback($pattern, [$this, 'handleBlockMatch'], $source);
    }

    /**
     * Returns a new code block based on matched content and settings
     *
     * @param array $matches
     *
     * @return string
     */
    protected function handleBlockMatch(array $matches = []): string
    {
        $lang = str_replace('language-', '', $matches[2]);
        $code = $matches[3];

        return str_replace(['{languageClass}', '{sourceCode}'], [$lang, $code], self::$codeBlockSnippet);
    }
}
