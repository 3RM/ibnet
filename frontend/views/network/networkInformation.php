<?php

use frontend\assets\AjaxAsset;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */
AjaxAsset::register($this);
$this->title = 'Network Information';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container-form">

    <?php $form = ActiveForm::begin([
        'id' => $network->formName(), 
        'enableAjaxValidation' => true,
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($network, 'name')->textInput(['maxLength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($network, 'description')->textArea([
                'rows' => 6, 
                'maxLength' => true, 
                'placeholder' => 'Short description...'
            ]); ?>
        </div>
    </div>

    <div style="width:250px;">
        <?= $form->field($network, 'network_image')->widget(\sadovojav\cutter\Cutter::className(), [
            'cropperOptions' => [
                'viewMode' => 1,
                'aspectRatio' => 1,         // 160px x 160px
                'movable' => false,
                'rotatable' => true,
                'scalable' => false,
                'zoomable' => false,
                'zoomOnTouch' => false,
                'zoomOnWheel' => false,
            ],
        ]) ?>
    </div>

     <div class="row top-margin-40">
        <div class="col-md-4">
            <?= $form->field($network, 'ministry_id')->widget(Select2::classname(), [ 
                'data' => $initialData,
                'options' => ['placeholder' => 'Search by name or city...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => Url::to(['ajax/find-ministry']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'templateResult' => new JsExpression('function(profile) { 
                        if(profile.org_city > "" && profile.org_st_prov_reg > "") {
                            return profile.text+", "+profile.org_city+", "+profile.org_st_prov_reg;
                        } else {
                            return profile.text;
                        };
                    }'),
                    'templateSelection' => new JsExpression('function (profile) { 
                        if(profile.org_city > "" && profile.org_st_prov_reg > "") {
                            return profile.text+", "+profile.org_city+", "+profile.org_st_prov_reg;
                        } else {
                            return profile.text;
                        };
                    }'),
                ],
            ]); ?>
         </div>
    </div>

    <?= $this->render('_networkFormFooter', ['network' => $network]) ?>   

    <?php ActiveForm::end(); ?>

</div>