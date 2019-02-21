<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

$this->title = 'Update User';
?>

<div class="site-index">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($user, 'first_name')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'last_name')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'new_email')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'new_email_token')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'auth_key')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'password_hash')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'password_reset_token')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'created_at')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'updated_at')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'last_login')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'status')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'display_name')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'home_church')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'primary_role')->textInput(['maxlength' => true]) ?>
	<?= $form->field($user, 'emailPrefLinks')->checkbox() ?>
	<?= $form->field($user, 'emailPrefComments')->checkbox() ?>
	<?= $form->field($user, 'emailPrefFeatures')->checkbox() ?>
	<?= $form->field($user, 'emailPrefBlog')->checkbox() ?>
	<?= $form->field($user, 'reviewed')->checkbox() ?>

	<?= Html::a('Cancel', ['/accounts/users'], ['class' => 'btn btn-primary']) ?>
	<?= HTML::submitbutton('Save', [
        'method' => 'POST',
        'class' => 'btn btn-primary',
        'name' => 'save',
    ]) ?>

	<?php $form = ActiveForm::end(); ?>

</div>
