<?php
namespace verbb\doxter\controllers;

use verbb\doxter\Doxter;

use craft\web\Controller;

use yii\web\Response;

class BaseController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response
    {
        $settings = Doxter::$plugin->getSettings();

        return $this->renderTemplate('doxter/settings', [
            'settings' => $settings,
        ]);
    }

}