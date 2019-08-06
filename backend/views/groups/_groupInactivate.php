<?php

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <?php $form = ActiveForm::begin(['action' => '/groups/update']); ?>

    <p>Inactivate group <?= $group->id ?>. The owner will be notified by email the group has been inactivated.  They will have the ability to reactivate it.</p>

    <?= $form->field($group, 'message')->textArea(['rows' => 2 ])->label('Additional message to owner (optional)') ?> 

    <?= Html::submitButton('Inactivate', [
            'name' => 'inactivate',
            'value' => $group->id,
            'method' => 'post',
            'class' => 'btn-main',
            'onclick' => 'return confirm("Are you sure you want to inactivate this group? Click to confirm.")'
        ]); 
    ?>

    <?php $form = ActiveForm::end(); ?>