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

    <h3 class="top-margin-28">Membership</h3>

    <p><?= HTML::icon('info-sign') ?> All members are required to identify their home church from the directory.</p>

    <?= $form->field($network, 'permit_user')->widget(CheckboxX::classname(), [
        'initInputType' => CheckboxX::INPUT_CHECKBOX,
        'autoLabel' => true,
        'pluginOptions'=>[
            'theme' => 'krajee-flatblue',
            'enclosedLabel' => true,
            'threeState'=>false, 
        ]
    ])->label(false) ?>

    <h3 class="top-margin-28">Privacy</h3>
    <?= $form->field($network, 'private')->widget(CheckboxX::classname(), [
        'initInputType' => CheckboxX::INPUT_CHECKBOX,
        'autoLabel' => true,
        'pluginOptions'=>[
        'theme' => 'krajee-flatblue',
            'theme' => 'krajee-flatblue',
            'enclosedLabel' => true,
            'threeState'=>false, 
        ]
    ])->label(false) ?>
    <?= $form->field($network, 'hide_on_profiles')->widget(CheckboxX::classname(), [
        'initInputType' => CheckboxX::INPUT_CHECKBOX,
        'autoLabel' => true,
        'pluginOptions'=>[
        'theme' => 'krajee-flatblue',
            'theme' => 'krajee-flatblue',
            'enclosedLabel' => true,
            'threeState'=>false, 
        ]
    ])->label(false) ?>
    <?= $form->field($network, 'not_searchable')->widget(CheckboxX::classname(), [
        'initInputType' => CheckboxX::INPUT_CHECKBOX,
        'autoLabel' => true,
        'pluginOptions'=>[
        'theme' => 'krajee-flatblue',
            'theme' => 'krajee-flatblue',
            'enclosedLabel' => true,
            'threeState'=>false, 
        ]
    ])->label(false) ?>

    <?= $this->render('_networkFormFooter', ['network' => $network]) ?>
    
    <?php ActiveForm::end(); ?>
 
    </div>

</div>