<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Group asset bundle.
 */
class GroupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/group.css',
        'css/group-admin.css',
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