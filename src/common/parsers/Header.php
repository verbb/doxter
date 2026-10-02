<?php
namespace verbb\doxter\common\parsers;

use Craft;
use craft\helpers\ElementHelper;
use craft\helpers\Html;

class Header extends BaseParser
{
    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;
    protected ?array $addHeaderAnchorsTo = null;
    protected ?int $startingHeaderLevel = null;

    private array $_slugCounts = [];


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
        $this->_slugCounts = [];

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
        $clean = trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, Craft::$app->charset));

        $currentHeaderLevel = (int)substr($tag, 1, 1);
        $updatedHeaderLevel = min(6, $currentHeaderLevel + ($this->startingHeaderLevel - 1));

        if ($this->startingHeaderLevel) {
            $tag = sprintf('h%s', $updatedHeaderLevel);
        }

        if (in_array($tag, $this->addHeaderAnchorsTo)) {
            $slug = $this->_getUniqueSlug($clean);
            $title = Html::encode($clean);

            return "<{$tag} id=\"{$slug}\">{$text} <a class=\"anchor\" href=\"#{$slug}\" title=\"{$title}\">#</a></{$tag}>";
        }

        return "<{$tag}>{$text}</{$tag}>";
    }


    // Private Methods
    // =========================================================================

    private function _getUniqueSlug(string $text): string
    {
        $slug = ElementHelper::generateSlug($text);
        $count = ($this->_slugCounts[$slug] ?? 0) + 1;
        $this->_slugCounts[$slug] = $count;

        return $count === 1 ? $slug : sprintf('%s-%s', $slug, $count);
    }
}
