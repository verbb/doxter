<?php
namespace selvinortiz\doxter\assetbundles\doxterplugin;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DoxterPluginBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@selvinortiz/doxter/assetbundles/doxterplugin/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/settings.js',
        ];

        parent::init();
    }
}
