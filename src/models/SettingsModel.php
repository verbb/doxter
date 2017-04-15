<?php
namespace selvinortiz\doxter\models;

use craft\base\Model;

/**
 * Class Settings
 *
 * @package selvinortiz\doxter\models
 */
class SettingsModel extends Model
{
    public $codeBlockSnippet = '';
    public $addHeaderAnchors = true;
    public $addHeaderAnchorsTo = ['h1', 'h2', 'h3'];
    public $addTypographyStyles = true;
    public $startingHeaderLevel = 1;
    public $parseReferenceTags = true;
    public $parseShortcodes = true;
    public $enableCpTab = false;
    public $pluginAlias = 'Doxter';

    public function rules()
    {
        return [
            [['codeBlockSnippet', 'addHeaderAnchors'], 'required']
        ];
    }
}
