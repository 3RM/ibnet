<?php

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <?php $form = ActiveForm::begin(['action' => '/groups/update']); ?>

    <p>
        Delete group <?= $group->id ?>.  All data associated with the group will be hard deleted and the group name 
        will be changed to "DELETED-<?= $group->id . '-' . $group->name ?>". The owner will be notified by email.
    </p>
    <p class="danger">Warning: This change is irriversible!</p>

    <?= $form->field($group, 'message')->textArea(['rows' => 2 ])->label('Additional message to owner (optional)') ?> 

    <?= Html::submitButton('Trash', [
            'name' => 'trash',
            'value' => $group->id,
            'method' => 'post',
            'class' => 'btn-main',
            'onclick' => 'return confirm("Are you sure you want to delete this group? Click to confirm.")'
        ]); 
    ?>

    <?php $form = ActiveForm::end(); ?>