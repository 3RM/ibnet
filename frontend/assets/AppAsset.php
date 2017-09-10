<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/profilePages.css',
        'css/pushmenu.css',
        'css/glyphicons.css',
        'css/glyphicons-social.css',
    ];
    public $js = [
        'js/site.js',
        'js/pushmenu.js',
        //'js/ajax.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];
}