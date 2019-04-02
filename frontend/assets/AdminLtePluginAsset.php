<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Network plugins asset bundle.
 */
class AdminLtePluginAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
    public $js = [
        // 'fullcalendar/fullcalendar.min.js',
        '../../../js/calendar.js',
    ];
    public $css = [
        // 'fullcalendar/fullcalendar.min.css',
    ];
    public $depends = [
        // 'omnilight\assets\MomentAsset',
        // 'yii\jui\JuiAsset',
        'dmstr\web\AdminLteAsset',
    ];
}