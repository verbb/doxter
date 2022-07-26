<?php
namespace verbb\doxter;

use verbb\doxter\base\PluginTrait;
use verbb\doxter\fields\Doxter as DoxterField;
use verbb\doxter\models\Settings;
use verbb\doxter\twigextensions\Extension;
use verbb\doxter\variables\DoxterVariable;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;
use markhuot\CraftQL\CraftQL;

class Doxter extends Plugin
{
    // Properties
    // =========================================================================

    public string $schemaVersion = '4.0.0';
    public bool $hasCpSettings = true;


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_setPluginComponents();
        $this->_setLogging();
        $this->_registerTwigExtensions();
        $this->_registerCpRoutes();
        $this->_registerVariables();
        $this->_registerFieldTypes();
        $this->_registerThirdPartyEventListeners();
    }

    public function getPluginName(): string
    {
        return Craft::t('doxter', 'Doxter');
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('doxter/settings'));
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'doxter/settings' => 'doxter/base/settings',
            ]);
        });
    }

    private function _registerTwigExtensions(): void
    {
        Craft::$app->getView()->registerTwigExtension(new Extension);
    }

    private function _registerFieldTypes(): void
    {
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = DoxterField::class;
        });
    }

    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('doxter', DoxterVariable::class);
        });
    }

    private function _registerThirdPartyEventListeners(): void
    {
        if (class_exists(CraftQL::class)) {
            Event::on(DoxterField::class, 'craftQlGetFieldSchema', function($event) {
                $event->handled = true;

                $outputSchema = $event->schema->createObjectType(ucfirst($event->sender->handle) . 'DoxterFieldData');

                $outputSchema->addStringField('text')
                    ->resolve(function($root) {
                        return (string)$root->getRaw();
                    });

                $outputSchema->addStringField('html')
                    ->resolve(function($root) {
                        return (string)$root->getHtml();
                    });

                $event->schema->addField($event->sender)->type($outputSchema);
            });
        }
    }
}
