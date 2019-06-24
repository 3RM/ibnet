<?php

use common\models\group\Group;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<p>
	Send an invite to a single email or a list of comma-separated emails.  The email will include the invitation along with instructions 
	for registering with IBNet (if requried) and joining the group.  Include an optional, additional invitation message.  Note 
	that anyone wishing to join the group must register with IBNet and identify their home church from the directory to unlock the feature.
</p>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($group, 'emails')->textInput()->label('Email(s)') ?>
<?= $form->field($group, 'message')->textArea()->label('Message (optional):') ?>
<?= Html::submitButton('Send', [
    'id' => 'invite',
    'method' => 'POST',
    'class' => 'btn btn-primary longer',
    'name' => 'invite'
]); ?>
<?php $form = ActiveForm::end(); ?>