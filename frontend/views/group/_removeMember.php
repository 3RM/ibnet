<?php

use common\models\group\Group;
use common\models\group\GroupMember;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>


<?php $form = ActiveForm::begin(['action' => '/group/remove-member']); ?>
    
<div class="member-picture-name">
    <?= $user->usr_image ? Html::img($user->usr_image) : Html::img('@img.site/user.png') ?>
    <h3><?= $user->fullName ?></h3>
</div>

<p>
	The member will be removed from the group, and have the option to rejoin at a later date.  
	The user will be notified of their removal. Include an optional message to the user.
</p>

<?= $form->field($group, 'message')->textArea()->label('Message (optional):') ?>
<?= $form->field($group, 'id')->hiddenInput(['value' => $group->id])->label(false) ?>
<?= Html::submitButton('Remove', [
    'method' => 'POST',
    'name' => 'remove',
    'value' => $user->id,
    'class' => 'btn btn-primary',
]) ?>

<?php $form = ActiveForm::end(); ?>