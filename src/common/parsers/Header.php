<?php

namespace selvinortiz\doxter\common\parsers;

use craft\helpers\ElementHelper;

use function selvinortiz\doxter\doxter;

/**
 * Class Header
 *
 * @package Craft
 */
class Header extends BaseParser
{
    /**
     * @var Header
     */
    protected static $_instance;

    /**
     * The headers to add anchors to
     *
     * @var array
     */
    protected $addHeaderAnchorsTo;

    /**
     * The header level to start output at
     *
     * @var int
     */
    protected $startingHeaderLevel;

    /**
     * Parses headers and adds anchors to them if necessary
     *
     * @param string $source  HTML source to search for headers within
     * @param array  $options Passed in parsing options
     *
     * @return string
     */
    public function parse($source, array $options = [])
    {
        $addHeaderAnchorsTo = ['h1', 'h2', 'h3'];
        $startingHeaderLevel = 1;

        extract($options);

        if (!is_array($addHeaderAnchorsTo)) {
            $addHeaderAnchorsTo = doxter()->api->getHeadersToParse($addHeaderAnchorsTo);
        }

        $this->addHeaderAnchorsTo = $addHeaderAnchorsTo;
        $this->startingHeaderLevel = $startingHeaderLevel;

        // Match against all header tags
        $headers = implode('|', array_map('trim', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']));
        $pattern = sprintf('/<(?<tag>%s)>(?<text>.*?)<\/(%s)>/i', $headers, $headers);
        $source = preg_replace_callback($pattern, [$this, 'handleMatch'], $source);

        return $source;
    }

    /**
     * Uses the matched headers to create an anchor for them
     *
     * @param array $matches
     *
     * @return string
     */
    public function handleMatch(array $matches = [])
    {
        $tag = $matches['tag'];
        $text = $matches['text'];
        $slug = ElementHelper::createSlug(htmlspecialchars_decode($text));
        $clean = strip_tags($text);

        $currentHeaderLevel = (int) substr($tag, 1, 1);
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
