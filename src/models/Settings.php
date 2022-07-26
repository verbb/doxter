<?php
namespace verbb\doxter\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    /**
     * Defines how shortcodes should be rendered
     *
     * @var array
     */
    public $shortcodes = [];

    /**
     * Defines how code blocks should be rendered
     *
     * @var string
     */
    public $codeBlockSnippet = '';

    /**
     * Whether anchors should be added to headers
     *
     * @var boolean
     */
    public $addHeaderAnchors = true;

    /**
     * Defines a list of headers to which anchors should be added
     *
     * @note Only applies if $addHeaderAnchors is true
     *
     * @var array
     */
    public $addHeaderAnchorsTo = ['h1', 'h2', 'h3'];

    /**
     * Defines the header level that will be considered the first (h1)
     *
     * @var integer
     */
    public $startingHeaderLevel = 1;

    /**
     * Whether typography styles should be applied to rendered content
     *
     * @var boolean
     */
    public $addTypographyStyles = true;

    /**
     * Whether hyphenation should be applied to typography styling of rendered content
     *
     * @var boolean
     */
    public $addTypographyHyphenation = true;

    /**
     * Whether Reference Tags should be parsed
     *
     * @var boolean
     */
    public $parseReferenceTags = true;

    /**
     * Whether Shortcodes should be parsed
     *
     * @var boolean
     */
    public $parseShortcodes = true;

    /**
     * Whether  shortcode tags have been registered
     *
     * @var bool
     */
    protected $_didRegisterShortcodeTags = false;


    // TODO - to remove
    public $pluginAlias = 'Doxter';
    public $enableCpTab = false;


    // Public Methods
    // =========================================================================

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [['codeBlockSnippet'], 'required'];

        return $rules;
    }

    /**
     * Returns a list of registered shortcode tags
     *
     * e.g. ['audio', 'vimeo:youtube']
     *
     * @return array
     */
    public function getRegisteredShortcodeTags(): array
    {
        if (!$this->_didRegisterShortcodeTags) {
            $map = $this->shortcodes['tags'] ?? [];
            $tags = [];

            if (!empty($map)) {
                foreach ($map as $tag => $template) {
                    $aliases = explode(':', $tag);

                    foreach ($aliases as $alias) {
                        $tags[$alias] = $template;
                    }
                }
            }

            $this->shortcodes['tags'] = $tags;
            $this->_didRegisterShortcodeTags = true;
        }

        return $this->shortcodes['tags'];
    }
}
