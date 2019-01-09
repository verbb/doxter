<?php
namespace selvinortiz\doxter\models;

use craft\base\Model;

/**
 * Class Settings
 *
 * @package selvinortiz\doxter\models
 *
 * @method SettingsModel getSettings()
 */
class SettingsModel extends Model
{
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
     * Whether or not anchors should be added to headers
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
     * Whether or not typography styles should be applied to rendered content
     *
     * @var boolean
     */
    public $addTypographyStyles = true;

    /**
     * Whether or not Reference Tags should be parsed
     *
     * @var boolean
     */
    public $parseReferenceTags = true;

    /**
     * Whether or not Shortcodes should be parsed
     *
     * @var boolean
     */
    public $parseShortcodes = true;

    /**
     * Whether or not the plugin icon/link should be displayed on the sidebar
     *
     * @var boolean
     */
    public $enableCpTab = false;

    /**
     * An alternate name/title for the plugin (e.g Markdown)
     *
     * @var string
     */
    public $pluginAlias = 'Doxter';

    /**
     * Whether or not shortcode tags have been registered
     *
     * @var bool
     */
    protected $_didRegisterShortcodeTags = false;

    public function rules()
    {
        return [
            [['codeBlockSnippet', 'addHeaderAnchors'], 'required']
        ];
    }

    /**
     * Returns a list of registered shortcode tags
     *
     * e.g. ['audio', 'vimeo:youtube']
     *
     * @return array
     */
    public function getRegisteredShortcodeTags()
    {
        if (!$this->_didRegisterShortcodeTags)
        {
            $map  = $this->shortcodes['tags'] ?? [];
            $tags = [];

            if (!empty($map))
            {
                foreach ($map as $tag => $template)
                {
                    $aliases = explode(':', $tag);

                    foreach ($aliases as $alias)
                    {
                        $tags[$alias] = $template;
                    }
                }
            }

            $this->shortcodes['tags']        = $tags;
            $this->_didRegisterShortcodeTags = true;
        }

        return $this->shortcodes['tags'];
    }
}
