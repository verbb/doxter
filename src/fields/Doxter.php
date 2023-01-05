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

    public static function displayName(): string
    {
        return 'Doxter';
    }


    // Properties
    // =========================================================================

    public int $tabSize = 2;
    public bool $indentWithTabs = false;
    public bool $enableLineWrapping = true;
    public bool $enableSpellChecker = false;
    public bool $showToolbar = true;

    public array $enabledToolbarIconNames = [
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


    // Public Methods
    // =========================================================================

    public function __construct(array $config = [])
    {
        // Config normalization
        if (array_key_exists('darkMode', $config)) {
            unset($config['darkMode']);
        }

        if (array_key_exists('toolbarSettings', $config)) {
            unset($config['toolbarSettings']);
        }

        parent::__construct($config);
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();
        $inputId = Html::id($this->handle);
        $namespacedId = $view->namespaceInputId($inputId);

        $settings = Json::encode($this->settings);

        $view->registerAssetBundle(DoxterFieldAsset::class);
        $view->registerJs("new Doxter().init('{$namespacedId}', {$settings}).render();");

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

    public function normalizeValue($value, ElementInterface $element = null): DoxterData
    {
        return new DoxterData($value);
    }

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


    // Private Methods
    // =========================================================================

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
