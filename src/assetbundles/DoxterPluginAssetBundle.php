<?php
namespace selvinortiz\doxter\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class DoxterPluginAssetBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@selvinortiz/doxter/assetbundles/plugin';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/settings.js',
        ];

        $this->css = [
            'css/settings.css'
        ];

        parent::init();
    }
}
