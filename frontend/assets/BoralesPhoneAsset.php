<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Include intlTelInput-jquery.min.js; 
 * Todo: make pure js version work (intlTelInput.js)
 * https://github.com/jackocnr/intl-tel-input/tree/master/build/js
 */
class BoralesPhoneAsset extends AssetBundle
{
    public $sourcePath = '@bower/intl-tel-input/build/js';
    public $css = [];
    public $js = ['intlTelInput-jquery.min.js'];
    public $depends = ['yii\web\JqueryAsset'];
}