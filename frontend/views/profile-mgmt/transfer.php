<?php

use common\widgets\Alert;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Transfer Profile';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= Html::icon('transfer') . ' ' . $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <p>Are you moving to a different ministry or do you no longer wish to maintain your profile?  Use this form to hand your profile off to a different user.</p>
            <p><?= Html::icon('info-sign') ?> The user must already be registered with IBNet.  Find them using their registration email.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <?= Alert::widget() ?>
        </div>
    </div>
    <div class="row">
        <div class = "col-md-4">
            <?= $form->field($profile, 'select')->textInput(['placeholder' => 'Enter a user email ...']) ?>
        </div>
        <div class = "col-md-4">
            <?= Html::submitButton(Html::icon('search') . ' Find User', ['class' => 'btn btn-primary top-margin']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>