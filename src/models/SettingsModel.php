<?php
namespace selvinortiz\doxter\models;

use craft\base\Model;

/**
 * Class Settings
 *
 * @package selvinortiz\doxter\models
 */
class SettingsModel extends Model {

    public $codeBlockSnippet;
    public $addHeaderAnchors;
    public $addHeaderAnchorsTo;
    public $addTypographyStyles;
    public $startingHeaderLevel;
    public $parseReferenceTags;
    public $parseShortcodes = true;
    public $enableCpTab;
    public $pluginAlias;

    public function rules() {
        return [
            [['codeBlockSnippet', 'addHeaderAnchors'], 'required']
        ];
    }
}
