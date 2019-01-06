<?php
namespace selvinortiz\doxter\fields;

use Craft;
use craft\base\Field;
use craft\db\mysql\Schema;
use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use Craft\helpers\Json;

use selvinortiz\doxter\fields\data\DoxterData;
use selvinortiz\doxter\assetbundles\DoxterFieldAssetBundle;

class DoxterField extends Field
{
    public $tabSize = 2;
    public $darkMode = false;
    public $indentWithTabs = false;
    public $enableLineWrapping = true;
    public $enableSpellChecker = false;
    public $showToolbar = true;
    public $enabledToolbarIconNames = [
        'bold' => true,
        'italic' => true,
        'quote' => true,
        'ordered-list' => true,
        'unordered-list' => true,
        'link' => true,
        'image' => true,
        'doxter-users' => true,
        'doxter-entries' => true,
        'doxter-assets' => true,
        'doxter-tags' => true,
        'preview' => false,
        'fullscreen' => false,
    ];

    /**
     * @todo Remove once we're able to clean up the db
     */
    public $toolbarSettings;

    /**
     * @return string
     */
    public static function displayName() : string
    {
        return 'Doxter';
    }

    public function init()
    {
        parent::init();
    }

    public function getInputHtml($value, ElementInterface $element = null) : string
    {
        $inputId = Craft::$app->view->formatInputId($this->handle);
        $namespacedId = Craft::$app->view->namespaceInputId($inputId);

        Craft::$app->getView()->registerAssetBundle(DoxterFieldAssetBundle::class);
        Craft::$app->getView()->registerJs("new Doxter().init('{$namespacedId}', {$this->getJsonEncodedEditorSettings()}).render();");

        return Craft::$app->getView()->renderTemplate(
            'doxter/fields/_input',
            [
                'id' => $inputId,
                'name' => $this->handle,
                'value' => $value,
                'class' => 'doxter-editor',
                'dark' => $this->darkMode,
                'rows' => 5
            ]
        );
    }

    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'doxter/fields/_settings',
            [
                'field' => $this,
                'toolbarIconOptions' => $this->getToolbarIconOptions()
            ]
        );
    }

    /**
     * @param string                $value
     * @param ElementInterface|null $element
     *
     * @return DoxterData
     */
    public function normalizeValue($value, ElementInterface $element = null) : DoxterData
    {
        return new DoxterData($value);
    }

    /**
     * @param DoxterData            $value
     * @param ElementInterface|null $element
     *
     * @return mixed
     */

    public function serializeValue($value, ElementInterface $element = null)
    {
        $value = $value->getRaw();
        $value = StringHelper::encodeMb4($value);

        return $value;
    }

    public function getSearchKeywords($value, ElementInterface $element) : string
    {
        $keywords = parent::getSearchKeywords($value, $element);
        $keywords = StringHelper::encodeMb4($keywords);

        return $keywords;
    }

    /**
     * @return string
     */
    public function getContentColumnType() : string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * Returns a Javascript Friendly array of settings
     * Making sure that enabledToolbarIconNames is returned as a flat array of names
     */
    public function getJsonEncodedEditorSettings()
    {
        $editorSettings = get_object_vars($this);

        // Flatten enabledToolbarIconNames from ['bold' => '1', 'italic' => ''] into ['bold']
        $enabledToolbarIconNames = $editorSettings['enabledToolbarIconNames'];
        $enabledToolbarIconNames = array_keys(array_filter($enabledToolbarIconNames));
        $editorSettings['enabledToolbarIconNames'] = $enabledToolbarIconNames;

        return Json::encode($editorSettings);
    }

    /**
     * Returns all icon options.
     *
     * @return array
     */
    private function getToolbarIconOptions() : array
    {
        return [
            'bold' => 'Bold',
            'italic' => 'Italic',
            'quote' => 'Quote',
            'ordered-list' => 'Ordered List',
            'unordered-list' => 'Unordered List',
            'link' => 'Link',
            'image' => 'Image',
            'doxter-users' => 'User Reference',
            'doxter-entries' => 'Entry Reference',
            'doxter-assets' => 'Assets Reference',
            'doxter-tags' => 'Tag Reference',
        ];
    }
}
