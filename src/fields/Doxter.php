<?php
namespace verbb\doxter\fields;

use verbb\doxter\assetbundles\DoxterFieldAsset;
use verbb\doxter\fields\data\DoxterData;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\db\mysql\Schema;
use Craft\helpers\Html;
use Craft\helpers\Json;
use craft\helpers\StringHelper;

class Doxter extends Field
{
    // Static Methods
    // =========================================================================

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return 'Doxter';
    }


    // Properties
    // =========================================================================

    public $tabSize = 2;
    public $darkMode = false; // @todo Remove on next major release
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


    // Public Methods
    // =========================================================================

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();
        $inputId = Html::id($this->handle);
        $namespacedId = $view->namespaceInputId($inputId);

        $view->registerAssetBundle(DoxterFieldAsset::class);
        $view->registerJs("new Doxter().init('{$namespacedId}', {$this->getJsonEncodedEditorSettings()}).render();");

        return $view->renderTemplate('doxter/_field/input', [
            'id' => $inputId,
            'name' => $this->handle,
            'value' => $value,
            'class' => 'doxter-editor',
            'rows' => 5,
        ]);
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('doxter/_field/settings', [
            'field' => $this,
            'toolbarIconOptions' => $this->getToolbarIconOptions(),
        ]);
    }

    /**
     * @param string $value
     * @param ElementInterface|null $element
     *
     * @return DoxterData
     */
    public function normalizeValue($value, ElementInterface $element = null): DoxterData
    {
        return new DoxterData($value);
    }

    /**
     * @param DoxterData $value
     * @param ElementInterface|null $element
     *
     * @return string
     */

    public function serializeValue($value, ElementInterface $element = null): string
    {
        $value = is_string($value) ? $value : $value->getRaw();
        return StringHelper::encodeMb4($value);
    }

    public function getSearchKeywords($value, ElementInterface $element): string
    {
        $keywords = parent::getSearchKeywords($value, $element);
        return StringHelper::encodeMb4($keywords);
    }

    /**
     * @return string
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * Returns a Javascript Friendly array of settings
     * Making sure that enabledToolbarIconNames is returned as a flat array of names
     */
    public function getJsonEncodedEditorSettings(): string
    {
        $editorSettings = get_object_vars($this);

        // Flatten enabledToolbarIconNames from ['bold' => '1', 'italic' => ''] into ['bold']
        $enabledToolbarIconNames = $editorSettings['enabledToolbarIconNames'];
        $enabledToolbarIconNames = array_keys(array_filter($enabledToolbarIconNames));
        $editorSettings['enabledToolbarIconNames'] = $enabledToolbarIconNames;

        return Json::encode($editorSettings);
    }


    // Private Methods
    // =========================================================================

    /**
     * Returns all icon options.
     *
     * @return array
     */
    private function getToolbarIconOptions(): array
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
