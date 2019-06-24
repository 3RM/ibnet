<?php

use kartik\checkbox\CheckboxX;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Privacy Settings';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container-form">

    <?php $form = ActiveForm::begin(); ?>

    <p><?= HTML::icon('info-sign') ?> All members are required to identify their home church from the directory.</p>
    
    <?= $form->field($group, 'private')->widget(CheckboxX::classname(), [
        'initInputType' => CheckboxX::INPUT_CHECKBOX,
        'autoLabel' => true,
        'pluginOptions'=>[
        'theme' => 'krajee-flatblue',
            'theme' => 'krajee-flatblue',
            'enclosedLabel' => true,
            'threeState'=>false, 
        ]
    ])->label(false) ?>
    <?= $form->field($group, 'hide_on_profiles')->widget(CheckboxX::classname(), [
        'initInputType' => CheckboxX::INPUT_CHECKBOX,
        'autoLabel' => true,
        'pluginOptions'=>[
        'theme' => 'krajee-flatblue',
            'theme' => 'krajee-flatblue',
            'enclosedLabel' => true,
            'threeState'=>false, 
        ]
    ])->label(false) ?>
    <?= $form->field($group, 'not_searchable')->widget(CheckboxX::classname(), [
        'initInputType' => CheckboxX::INPUT_CHECKBOX,
        'autoLabel' => true,
        'pluginOptions'=>[
        'theme' => 'krajee-flatblue',
            'theme' => 'krajee-flatblue',
            'enclosedLabel' => true,
            'threeState'=>false, 
        ]
    ])->label(false) ?>

    <?= $this->render('_groupFormFooter', ['group' => $group]) ?>
    
    <?php ActiveForm::end(); ?>
 
    </div>

</div>