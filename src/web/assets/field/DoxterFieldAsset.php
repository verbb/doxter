<?php
namespace verbb\doxter\web\assets\field;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DoxterFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/doxter/web/assets/field/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/simplemde.css',
            'doxter.css',
        ];

        $this->js = [
            'js/simplemde.js',
            'doxter.js',
        ];

        parent::init();
    }
}
