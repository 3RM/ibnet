<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Network asset bundle.
 */
class NetworkAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/network.css',
        'css/network-admin.css',
        'css/glyphicons.css',
        'css/glyphicons-social.css',
        'css/glyphicons-filetypes.css',
    ];
    public $js = [
        //'js/prayer.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}