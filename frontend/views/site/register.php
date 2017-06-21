<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use common\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Register';
?>
<?= Alert::widget() ?>

<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-11 col-lg-10">

            <p>Registration is free and will enable you to create profile listings and take advantage of current and future features of IBNet. By registering you agree that you have read and agreed to our <?= HTML::a('Terms', ['site/terms'], ['target' => 'blank']) ?> of use.</P>

            <?php $form = ActiveForm::begin([ 
            'id' => 'contact-form',
                //'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "<div class=\"col-md-12\">{label}\n{input}</div>\n<div class=\"col-md-12\">{error}</div>",
                    'labelOptions' => ['class' => 'control-label'],
                ],
            ]); ?>
            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'username')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">{image}</div><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">{input}</div></div>',
                    'options' => ['placeholder' => 'Enter verification code', 'class' => 'form-control',], 
                ]) ?></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= Html::submitButton('Register', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
                </div>
            </div>
            <div class="h"><?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?></div>
            <?php $form = ActiveForm::end() ?>
        </div>
    </div>
</div>
