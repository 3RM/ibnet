<?php

use common\widgets\Alert;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Transfer Group';
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
            <p>Would you like someone else to manage this group?  Use this form to hand it off to a different user.</p>
            <p><?= Html::icon('info-sign') ?> The user must already be registered with IBNet.  Enter an email below.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <?= Alert::widget() ?>
        </div>
    </div>
    <div class="row">
        <div class = "col-md-4">
            <?= $form->field($group, 'newUserEmail')->textInput(['placeholder' => 'Enter a user email ...']) ?>
        </div>
        <div class = "col-md-4">
            <?= Html::submitButton(Html::icon('send') . ' &nbsp; Send', ['class' => 'btn btn-primary top-margin']) ?>
        </div>
    </div>
    <div class="row">
        <?= Html::a(Html::icon('arrow-left') . ' &nbsp; Return', '/group/my-groups', ['class' => 'btn btn-primary top-margin']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>