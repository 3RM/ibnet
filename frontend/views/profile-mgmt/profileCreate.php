<?php

use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Profile Type';
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
        'id' => 'profile-form',
        'options' => ['class' => 'form-horizontal'],
        'method' => 'post',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>
            
    <p><?= HTML::icon('info-sign') ?> The profile name is used only for your own personal reference.  It will not appear on the public profile page.</p>

    <div class="row">
        <?= $form->field($profile, 'profile_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($profile, 'type')->widget(Select2::classname(), [
            'data' => $types,
            'language' => 'en',
            'theme' => 'krajee',
            'options' => ['placeholder' => 'Type ...', ['Individuals', 'Groups']],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>

        <div id="sub_pastor" style="display:none;">
            <?= $form->field($profile, 'ptype')->widget(Select2::classname(), [
                'data' => $pastorTypes,
                'language' => 'en',
                'theme' => 'krajee',
                'options' => ['placeholder' => 'Type ...', ['Individuals', 'Groups']],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>
        </div>

        <div id="sub_missionary" style="display:none;">
            <?= $form->field($profile, 'mtype')->widget(Select2::classname(), [
                'data' => $missionaryTypes,
                'language' => 'en',
                'theme' => 'krajee',
                'options' => ['placeholder' => 'Type ...', ['Individuals', 'Groups']],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>
        </div>
        
        <?= Html::submitButton('Create', ['class' => 'btn btn-primary', 'style' => 'margin-left:60px']) ?>

        <?php ActiveForm::end(); ?>
 
    </div>

</div>

<script>
$(document).ready(function () {
    $('#profile-type').on('change', function () {
        if (this.value == 'Pastor') {
            $("#sub_pastor").show();
        } else if (this.value == 'Missionary') {
            $("#sub_missionary").show();
        } else {
            $("#sub_pastor").hide();
            $("#sub_missionary").hide();
        }
    }).change();
});
</script>