<?php

use common\models\User;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <p>
        <?= $user->status == User::STATUS_BANNED ?
        'Restore user ' . $user->id . ' to status Active. The user will be able to log in again. Any profiles that were previously 
        deleted as a result of the ban will be reset to status inactive. The user will be notified by email of this change.' :
        'Ban user ' . $user->id . '. The user will be permanentally locked out of their account, their role will be 
        set to the default "User" and all profiles will be banned. The user will be notified by email of this change.' ?>
    </p>

    <?php $form = ActiveForm::begin(['action' => '/accounts/update', 'id' => 'description-form']); ?>

    <?= $form->field($user, 'select')->textArea(['id' => 'description', 'rows' => 1])->label('Description') ?>

    <?= $user->status == User::STATUS_BANNED ?
        Html::submitButton('Restore', [
            'name' => 'restore',
            'value' => $user->id,
            'method' => 'post',
            'class' => 'btn-main',
            'onclick' => 'return confirm("Are you sure you want to restore this account? Click to confirm.")'
        ]) :
        Html::submitButton('Ban', [
            'name' => 'ban',
            'value' => $user->id,
            'method' => 'post',
            'class' => 'btn-main',
            'onclick' => 'return confirm("Are you sure you want to ban this account? Click to confirm.")'
        ]);
    ?>   

    <?php $form = ActiveForm::end(); ?>
