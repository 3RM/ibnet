<?php

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <p>ID: <?= $user->id ?></p>

    <?php $form = ActiveForm::begin(['action' => '/accounts/update']); ?>

    <?= $form->field($user, 'first_name')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($user, 'last_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($user, 'display_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($user, 'new_email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($user, 'new_email_token')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>  
    <?= $form->field($user, 'password_reset_token')->textInput(['maxlength' => true]) ?>
    <?= $form->field($user, 'usr_image')->textInput(['maxlength' => true])->label('User Image') ?>      
    <?= $form->field($user, 'home_church')->textInput(['maxlength' => true]) ?>

    <?= Html::submitButton('Save', [
        'name' => 'save',
        'value' => $user->id,
        'method' => 'post',
        'class' => 'btn-main',
        'onclick' => 'return confirm("Be careful! You are updating user data. Do you have admin and/or user authorization to make changes? Click to confirm.")'
    ]); ?>   

    <?php $form = ActiveForm::end(); ?>
