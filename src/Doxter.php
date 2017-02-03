<?php
namespace selvinortiz\doxter;

use yii\base\Event;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

use selvinortiz\doxter\fields\DoxterField;
use selvinortiz\doxter\models\SettingsModel;
use selvinortiz\doxter\services\DoxterService;
use selvinortiz\doxter\variables\DoxterVariable;
use selvinortiz\doxter\extensions\DoxterExtension;

/**
 * Class Doxter
 *
 * @package selvinortiz\doxter;
 *
 * @property DoxterService $service
 */
class Doxter extends Plugin {

    /** @var  DoxterService */
    public static $api;

    public function init() {
        parent::init();

        self::$api = $this->get('api');

        Craft::$app->view->twig->addExtension(new DoxterExtension());

        Event::on(
            Fields::className(),
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = DoxterField::class;
            }
        );
    }

    /**
     * @return SettingsModel
     */
    public function createSettingsModel() {
        return new SettingsModel();
    }

    /**
     * @return string
     */
    public function defineTemplateComponent() {
        return DoxterVariable::class;
    }

    public function registerShortcodes() {
        return [
            'image'         => 'selvinortiz\\doxter\\common\\shortcodes\\DoxterShortcodes@image',
            'audio'         => 'selvinortiz\\doxter\\common\\shortcodes\\DoxterShortcodes@audio',
            'updates'       => 'selvinortiz\\doxter\\common\\shortcodes\\DoxterShortcodes@updates',
            'vimeo:youtube' => 'selvinortiz\\doxter\\common\\shortcodes\\DoxterShortcodes@video',
        ];
    }
}
