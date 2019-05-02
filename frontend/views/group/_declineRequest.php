<?php

use common\models\group\Group;
use common\models\group\GroupMember;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>


<?php $form = ActiveForm::begin(['action' => '/group/decline-request']); ?>

<div class="member-picture-name">
    <?= $user->usr_image ? Html::img($user->usr_image) : Html::img('@img.site/user.png') ?>
    <h3><?= $user->fullName ?></h3>
</div>

<p>The user will be notified that their request to join has been declined. Include an optional message to the user.</p>
<?= $form->field($group, 'message')->textArea()->label('Message (optional):') ?>
<?= $form->field($group, 'id')->hiddenInput(['value' => $group->id])->label(false) ?>
<?= Html::submitButton('Decline', [
    'method' => 'POST',
    'name' => 'decline',
    'value' => $user->id,
    'class' => 'btn btn-primary',
]) ?>
    
<?php $form = ActiveForm::end(); ?>