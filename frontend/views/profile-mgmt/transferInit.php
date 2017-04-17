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
            <h3>We found <?= $email ?>.</h3>
            <p>After you click the button, the user will receive an email with a link to complete the transfer.  They will have one week to respond before the link expires.</p>
        </div>
    </div>
    <div class="row top-margin">
        <div class = "col-md-4">
            <?= Html::submitButton('Initiate Transfer', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>