<?php

namespace inblank\activeuser\assets;

use yii\web\AssetBundle;

class BackendAsset extends AssetBundle
{
    public $sourcePath = '@inblank/activeuser/assets/files';
    public $css = [
        'css/frontend.css',
    ];
    public $js = [
        'js/frontend.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
