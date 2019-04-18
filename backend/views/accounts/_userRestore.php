<?php

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <p>Restore user <?= $user->id ?> to status Active.  The user will be able to log in again. Any profiles that were 
        previously deleted as a result of the ban will be reset to status inactive. The user will be notified by email of this change.</p>

    <?php $form = ActiveForm::begin(['action' => '/accounts/update']); ?>

    <?= Html::submitButton('Restore', [
        'name' => 'restore',
        'value' => $user->id,
        'method' => 'post',
        'class' => 'btn-main',
        'onclick' => 'return confirm("Are you sure you want to restore this account? Click to confirm.")'
    ]); ?>   

    <?php $form = ActiveForm::end(); ?>
