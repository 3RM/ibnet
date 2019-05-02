<?php

use common\models\group\Group;
use common\models\group\GroupMember;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>


<?php $form = ActiveForm::begin(['action' => '/group/restore']); ?>
    
<div class="member-picture-name">
    <?= $user->usr_image ? Html::img($user->usr_image) : Html::img('@img.site/user.png') ?>
    <h3><?= $user->fullName ?></h3>
</div>

<p>
	Removing the ban will reset the user status to non-member and give the option to rejoin the group.  
	The user will be notified via email. Include an optional message to the user.
</p>

<?= $form->field($group, 'message')->textArea()->label('Message (optional):') ?>
<?= $form->field($group, 'id')->hiddenInput(['value' => $group->id])->label(false) ?>
<?= Html::submitButton('Remove Ban', [
    'method' => 'POST',
    'name' => 'restore',
    'value' => $user->id,
    'class' => 'btn btn-primary',
]) ?>

<?php $form = ActiveForm::end(); ?>