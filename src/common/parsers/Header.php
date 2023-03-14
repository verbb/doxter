<?php
namespace verbb\doxter\common\parsers;

use craft\helpers\ElementHelper;

class Header extends BaseParser
{
    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;
    protected ?array $addHeaderAnchorsTo = null;
    protected ?int $startingHeaderLevel = null;


    // Public Methods
    // =========================================================================

    /**
     * Parses headers and adds anchors to them if necessary
     *
     * @param string $source HTML source to search for headers within
     * @param array $options Passed in parsing options
     *
     * @return mixed
     */
    public function parse(string $source, array $options = []): mixed
    {
        $addHeaderAnchorsTo = $options['addHeaderAnchorsTo'] ?? [];
        $startingHeaderLevel = $options['startingHeaderLevel'] ?? 1;

        $this->addHeaderAnchorsTo = $addHeaderAnchorsTo;
        $this->startingHeaderLevel = $startingHeaderLevel;

        // Match against all header tags
        $headers = implode('|', array_map('trim', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']));
        $pattern = sprintf('/<(?<tag>%s)>(?<text>.*?)<\/(%s)>/i', $headers, $headers);
        return preg_replace_callback($pattern, [$this, 'handleMatch'], $source);
    }

    /**
     * Uses the matched headers to create an anchor for them
     *
     * @param array $matches
     *
     * @return string
     */
    public function handleMatch(array $matches = []): string
    {
        $tag = $matches['tag'];
        $text = $matches['text'];
        $slug = ElementHelper::generateSlug(htmlspecialchars_decode($text));
        $clean = strip_tags($text);

        $currentHeaderLevel = (int)substr($tag, 1, 1);
        $updatedHeaderLevel = min(6, $currentHeaderLevel + ($this->startingHeaderLevel - 1));

        if ($this->startingHeaderLevel) {
            $tag = sprintf('h%s', $updatedHeaderLevel);
        }

        if (in_array($tag, $this->addHeaderAnchorsTo)) {
            return "<{$tag} id=\"{$slug}\">{$text} <a class=\"anchor\" href=\"#{$slug}\" title=\"{$clean}\">#</a></{$tag}>";
        }

        return "<{$tag}>{$text}</{$tag}>";
    }
}
