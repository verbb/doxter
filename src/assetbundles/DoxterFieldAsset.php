<?php
namespace verbb\doxter\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DoxterFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/doxter/resources/field/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/simplemde.css',
            'css/doxter.css',
        ];

        $this->js = [
            'js/simplemde.js',
            'js/doxter.js',
        ];

        parent::init();
    }
}
