<?php

use common\models\profile\Profile;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <?php $form = ActiveForm::begin(['action' => '/directory/update']); ?>

    <?= $profile->status == Profile::STATUS_TRASH ?
        '<p>Restore profile ' . $profile->id . ' to a state of Inactive. It will show up in the user\'s profile list and they will have the ability to reactivate it.</p>' :
        '<p>Inactivate profile ' . $profile->id . '. The user will be notified by email that their profile has been inactivated.  They will have the ability to reactivate it.</p>' ?>

    <?= $profile->status == Profile::STATUS_TRASH ? 
        Html::submitButton('Restore', [
            'name' => 'restore',
            'value' => $profile->id,
            'method' => 'post',
            'class' => 'btn-main',
            'onclick' => 'return confirm("Are you sure you want to restore this profile to inactive state? Click to confirm.")'
        ]) : 
        Html::submitButton('Inactivate', [
            'name' => 'inactivate',
            'value' => $profile->id,
            'method' => 'post',
            'class' => 'btn-main',
            'onclick' => 'return confirm("Are you sure you want to inactivate this profile? Click to confirm.")'
        ]); 
    ?>

    <?php $form = ActiveForm::end(); ?>