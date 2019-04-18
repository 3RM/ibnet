<?php

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
	<?php $form = ActiveForm::begin(['action' => '/directory/update']); ?>

    <p>Trash profile <?= $profile->id ?>.  The user will be notified by email that their profile has been deleted.  Trash is a soft delete.  
        It can be restored at a later date by resetting the status to inactive with the restore button.</p>

    <?= Html::submitButton('Trash', [
        'name' => 'trash',
        'value' => $profile->id,
        'method' => 'post',
        'class' => 'btn-main',
        'onclick' => 'return confirm("Are you sure you want to delete this profile? Click to confirm.")'
    ]); ?>

    <?php $form = ActiveForm::end(); ?>