<?php
namespace selvinortiz\doxter\fields;

use Craft;
use craft\base\Field;
use craft\db\mysql\Schema;
use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use Craft\helpers\Json;

use selvinortiz\doxter\assetbundles\doxterfield\DoxterFieldAssetBundle;
use selvinortiz\doxter\fields\data\DoxterData;

class DoxterField extends Field
{
    // Properties
    // =========================================================================

    public $tabSize = 2;
    public $indentWithTabs = false;
    public $enableLineWrapping = true;
    public $enableSpellChecker = false;
    public $toolbarSettings;

    // Static
    // =========================================================================

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return 'Doxter';
    }

    /**
     * Returns all icon options.
     *
     * @return array
     */
    private static function getIconOptions(): array
    {
        return [
            'toolbar' => 'Toolbar - turn off to hide toolbar',
            'bold' => 'Bold',
            'italic' => 'Italic',
            'quote' => 'Quote',
            'ordered-list' => 'Ordered-List',
            'unordered-list' => 'Unordered-List',
            'link' => 'Link',
            'image' => 'Image',
            'doxter-users' => 'User Reference',
            'doxter-entries' => 'Entry Reference',
            'doxter-assets' => 'Assets Reference',
            'doxter-tags' => 'Tag Reference',
            'fullscreen' => 'Fullscreen'
        ];
    }

    private function parseToolbarSettings() {

        if (!$this->toolbarSettings['toolbar']) {
            $this->toolbarSettings = false;
            return;
        }

        $disbledTools = [];
        
        foreach ($this->toolbarSettings as $key => $value) {
            if ($key != 'toolbar' && !$value) {
                $disbledTools[] = $key;
            }
        }

        $this->toolbarSettings = $disbledTools;

        return;
    }

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        if ($this->toolbarSettings === null) {
            $this->toolbarSettings = self::getIconOptions();
        } 
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        self::parseToolbarSettings();
        $inputId = Craft::$app->view->formatInputId($this->handle);
        $namespacedId = Craft::$app->view->namespaceInputId($inputId);
        $editorSettings = Json::encode(get_object_vars($this));

        Craft::$app->getView()->registerAssetBundle(DoxterFieldAssetBundle::class);
        Craft::$app->getView()->registerJs("new Doxter().init('{$namespacedId}', $editorSettings).render();");

        return Craft::$app->getView()->renderTemplate(
            'doxter/fields/_input',
            [
                'id' => $inputId,
                'name' => $this->handle,
                'value' => $value,
                'class' => 'doxter-editor',
                'rows' => 5
            ]
        );
    }

    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('doxter/fields/_settings',
            [
             'field' => $this,
             'iconOptions' => self::getIconOptions()
            ]);
    }

    /**
     * @param string                $value
     * @param ElementInterface|null $element
     *
     * @return DoxterData
     */
    public function normalizeValue($value, ElementInterface $element = null): DoxterData
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

    public function getSearchKeywords($value, ElementInterface $element): string
    {
        $keywords = parent::getSearchKeywords($value, $element);
        $keywords = StringHelper::encodeMb4($keywords);

        return $keywords;
    }

    /**
     * @return string
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }
}
