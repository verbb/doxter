<?php
namespace selvinortiz\doxter\assetbundles\doxtershortcodes;

use craft\web\AssetBundle;
// use craft\web\assets\cp\CpAsset;

class DoxterShortcodesAssetBundle extends AssetBundle {

    public function init() {
        $this->sourcePath = '@selvinortiz/doxter/assetbundles/doxtershortcodes/dist';
        // $this->depends = [CpAsset::class];

        $this->js = [
            'js/jquery.fitvids.js',
        ];

        parent::init();
    }
}
