<?php
namespace selvinortiz\doxter\fields;

use Craft;
use craft\base\Field;
use craft\db\mysql\Schema;
use craft\base\ElementInterface;
use craft\helpers\StringHelper;

use selvinortiz\doxter\assetbundles\doxterfield\DoxterFieldAssetBundle;
use selvinortiz\doxter\fields\data\DoxterData;

class DoxterField extends Field
{
    public $tabSize = 2;
    public $indentWithTabs = false;
    public $enableLineWrapping = true;
    public $enableSpellChecker = false;
    public $enableParserToolbar = true;

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return 'Doxter';
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $inputId = Craft::$app->view->formatInputId($this->handle);
        $namespacedId = Craft::$app->view->namespaceInputId($inputId);
        $editorSettings = json_encode(get_object_vars($this));

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
