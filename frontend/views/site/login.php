<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use common\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
?>
<?= Alert::widget() ?>

<div class="site-login">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "<div class=\"col-md-12\">{label}\n{input}</div>\n<div class=\"col-md-12\">{error}</div>",
            'labelOptions' => ['class' => 'control-label'],
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-4 center top-margin-40">
            <?= Html::img('@web/images/ibnet-large.png'); ?>
        </div>
        <div class="col-md-4">


            <?= $form->field($model, 'loginId')->textInput(['autocomplete' => false]) ?>
            <?= $form->field($model, 'password')->passwordInput() ?>

            <div class="login-links">
                <?= html::a('Forget your password?', ['site/request-password-reset']) ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <div class="form-group">
                <div class="login-buttons">
                    <?= Html::submitButton('Login', [
                        'method' => 'POST',
                        'class' => 'btn btn-primary', 
                        'name' => 'login-button']) ?>
                    <?= Html::a('Register', ['register'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>