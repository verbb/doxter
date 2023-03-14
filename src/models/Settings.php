<?php
namespace verbb\doxter\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public array $shortcodes = [];
    public string $codeBlockSnippet = '';
    public bool $addHeaderAnchors = true;
    public array|null $addHeaderAnchorsTo = ['h1', 'h2', 'h3'];
    public int $startingHeaderLevel = 1;
    public bool $addTypographyStyles = true;
    public bool $addTypographyHyphenation = true;
    public bool $parseReferenceTags = true;
    public bool $parseShortcodes = true;
    protected bool $_didRegisterShortcodeTags = false;


    // Public Methods
    // =========================================================================

    public function __construct(array $config = [])
    {
        // Config normalization
        if (array_key_exists('$pluginAlias', $config)) {
            unset($config['$pluginAlias']);
        }

        if (array_key_exists('$enableCpTab', $config)) {
            unset($config['$enableCpTab']);
        }

        parent::__construct($config);
    }

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
