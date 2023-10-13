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

    public static function dbType(): string
    {
        return Schema::TYPE_TEXT;
    }


    // Properties
    // =========================================================================

    public int $tabSize = 2;
    public bool $indentWithTabs = false;
    public bool $enableLineWrapping = true;
    public bool $enableSpellChecker = false;
    public bool $showToolbar = true;
    public array $enabledToolbarIconNames = [];


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

        // Normalise older field settings - stored as `[bold => 1]` not `[bold]`.
        // Re-saving the field will kick it into gear, but provide a nice transition
        if (array_key_exists('enabledToolbarIconNames', $config)) {
            if (!isset($config['enabledToolbarIconNames'][0])) {
                $config['enabledToolbarIconNames'] = array_keys($config['enabledToolbarIconNames']);
            }
        }

        parent::__construct($config);
    }

    protected function inputHtml(mixed $value, ?ElementInterface $element, bool $inline): string
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
        if (!$this->id) {
            $this->enabledToolbarIconNames = [
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
        }

        return Craft::$app->getView()->renderTemplate('doxter/_field/settings', [
            'field' => $this,
            'toolbarIconOptions' => $this->getToolbarIconOptions(),
        ]);
    }

    public function normalizeValue(mixed $value, ElementInterface $element = null): mixed
    {
        return new DoxterData($value);
    }

    public function serializeValue(mixed $value, ElementInterface $element = null): mixed
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
