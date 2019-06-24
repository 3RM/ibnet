<?php

use common\models\group\Group;
use common\models\group\GroupMember;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<div class="member-card">
    <?= $model->user->usr_image ? Html::img($model->user->usr_image) : Html::img('@img.site/user.jpg'); ?>
    <div class="member-name">
        <p class="title">
            <?= $model->user->fullName ?> <?= $model->status == GroupMember::STATUS_BANNED ? '<span class="badge" style="background-color:#05aa36">Banned</span>' : NULL ?>
        </p>
        <p class="subtitle"><?= $model->user->primary_role ?? NULL ?></p> 
        <p class="subtitle">Requested <?=  Yii::$app->formatter->asDate($model->created_at) ?></p>
    </div>
    <?php $form = ActiveForm::begin() ?>
    <div class="member-actions">
        <?= Html::submitButton('<i class="fas fa-user-check"></i>', [
            'method' => 'POST', 
            'name' => 'approve', 
            'value' => $model->group_id . '-' . $model->user_id,
            'class' => 'btn btn-member-action', 
            'title' => 'Approve member',
        ]) ?>
        <?= Html::button('<i class="fas fa-user-times"></i>', [
            'id' => 'decline-' . $model->id, 
            'class' => 'btn btn-member-action', 
            'title' => 'Decline request',
        ]) ?>
        <?= Html::button('<i class="far fa-envelope"></i>', [
            'id' => 'contact-' . $model->id, 
            'class' => 'btn btn-member-action', 
            'title' => 'Contact'
        ]) ?>
    </div>
    <?php $form = ActiveForm::end(); ?>
    
</div>

<?php $this->registerJS("$('#decline-" . $model->id . "').click(function(e) {
    $.get('/group/decline-request', {id: " . $group->id . ", uid: " . $model->user_id . "}, function(data) {
        $('#decline-modal').modal('show').find('#decline-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>

<?php $this->registerJS("$('#contact-" . $model->id . "').click(function(e) {
    $.get('/group/contact-member', {id: " . $group->id . ", uid: " . $model->user_id . "}, function(data) {
        $('#contact-modal').modal('show').find('#contact-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>