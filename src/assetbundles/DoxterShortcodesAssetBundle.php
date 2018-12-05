<?php
namespace selvinortiz\doxter\assetbundles;

use craft\web\AssetBundle;

class DoxterShortcodesAssetBundle extends AssetBundle {

    public function init() {
        $this->sourcePath = '@selvinortiz/doxter/assetbundles/shortcodes';

        $this->js = [
            'js/jquery.fitvids.js',
        ];

        parent::init();
    }
}
