<?php

namespace inblank\activeuser\assets;

use yii\web\AssetBundle;

class BackendAsset extends AssetBundle
{
    public $sourcePath = '@inblank/activeuser/assets/files';
    public $css = [
        'css/backend.css',
    ];
    public $js = [
        'js/backend.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
