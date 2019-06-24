<?php

use common\models\group\Group;
use common\models\group\GroupMember;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>


<?php $form = ActiveForm::begin(['action' => '/group/ban-member']); ?>
    
<div class="member-picture-name">
    <?= $user->usr_image ? Html::img($user->usr_image) : Html::img('@img.site/user.png') ?>
    <h3><?= $user->fullName ?></h3>
</div>

<p>
	Banning a member means they will be removed from the group and will not have the option to rejoin.  
	The user will be notified via email. Include an optional message to the user.
</p>

<?= $form->field($group, 'message')->textArea()->label('Message (optional):') ?>
<?= $form->field($group, 'id')->hiddenInput(['value' => $group->id])->label(false) ?>
<?= Html::submitButton('Ban', [
    'method' => 'POST',
    'name' => 'ban',
    'value' => $user->id,
    'class' => 'btn btn-primary',
]) ?>

<?php $form = ActiveForm::end(); ?>