<?php

use kartik\checkbox\CheckboxX;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Subscriptions';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= '<i class="fas fa-at"></i> ' . $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container">


    <div class="row">
        <div class="col-md-6">
            <p><b>Email</b>: <?= $sub->email ?></p>
        </div>
    </div>
    <p class="top-margin"></p>

    <?php $form = ActiveForm::begin(); ?>

    <?php if (isset($registered)) { ?>
        <?= $form->field($sub, 'profile')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
            'theme' => 'krajee-flatblue',
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($sub, 'links')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($sub, 'comments')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($sub, 'features')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($sub, 'blog')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
    <?php } ?>

    <p class="top-margin-40"></p>

    <?= $form->field($sub, 'unsubscribe')->widget(CheckboxX::classname(), [
        'initInputType' => CheckboxX::INPUT_CHECKBOX,
        'autoLabel' => true,
        'pluginOptions'=>[
            'theme' => 'krajee-flatblue',
            'enclosedLabel' => true,
            'threeState'=>false, 
        ]
    ])->label(false) ?>

    <div class="row top-margin-40">
        <div class = "col-md-6">
            <?= Html::submitButton('Unsubscribe', [
                'method' => 'post',
                'class' => 'btn btn-primary',
                'name' => 'unsubscribe',
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>