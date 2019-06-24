<?php

use common\models\group\Group;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($answer, 'answer_description')->textArea(['maxlength' => true]); ?>
<?= Html::submitButton('Save', [
    'method' => 'POST',
    'class' => 'btn btn-primary longer',
]); ?>
<?php $form = ActiveForm::end(); ?>