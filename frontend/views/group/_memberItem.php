<?php

use common\models\group\Group;
use common\models\group\GroupMember;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
$new = $model->created_at > $group->last_visit;
/* @var $this yii\web\View */
?>

<div class="member-card">
    <?= $model->user->usr_image ? Html::img($model->user->usr_image) : Html::img('@img.site/user.jpg'); ?>
    <div class="member-name">
        <p class="title"><?= $model->user->fullName ?> <?= $new ? '<span class="badge" style="background-color:#05aa36">New</span>' : NULL ?><?= $model->status == GroupMember::STATUS_BANNED ? '<span class="badge" style="background-color:#f00">Banned</span>' : NULL ?></p>
        <p class="subtitle"><?= $model->user->primary_role ?? NULL ?></p> 
        <p class="subtitle">Joined <?=  Yii::$app->formatter->asDate($model->created_at) ?></p>
        <?php if ($model->user->isMissionary && $group->feature_update) { ?>
            <?= $model->show_updates ? 
                '<p class="subtitle"><i class="far fa-check-circle"></i> Showing updates</p>' :
                '<p class="subtitle"><i class="far fa-times-circle"></i> Not showing updates</p>' 
            ?>
        <?php } ?>
    </div>
    <?php $form = ActiveForm::begin() ?>
    <div class="member-actions">
        <?php if ($model->status == GroupMember::STATUS_BANNED) { ?>
            <?= Html::button('<i class="fas fa-user-check"></i>', [
                'id' => 'restore-' . $model->id,
                'class' => 'btn btn-member-action', 
                'title' => 'Remove ban'
            ]) ?>
        <?php } else { ?>
            <?= Html::button('<i class="fas fa-user-times"></i>', [
                'id' => 'remove-' . $model->id,
                'class' => 'btn btn-member-action', 
                'title' => 'Remove member'
            ]) ?>
            <?= !$group->private ? 
                Html::button('<i class="fas fa-user-slash"></i>', [
                    'id' => 'ban-' . $model->id, 
                    'class' => 'btn btn-member-action', 
                    'title' => 'Contact'
                ]) : NULL
            ?>
        <?php } ?>
        <?= Html::button('<i class="far fa-envelope"></i>', [
            'id' => 'contact-' . $model->id, 
            'class' => 'btn btn-member-action', 
            'title' => 'Contact'
        ]) ?>
    </div>
    <?php $form = ActiveForm::end(); ?>
    
</div>

<?php $this->registerJS("$('#remove-" . $model->id . "').click(function(e) {
    $.get('/group/remove-member', {id: " . $model->group_id . ", uid: " . $model->user_id . "}, function(data) {
        $('#remove-modal').modal('show').find('#remove-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>

<?php $this->registerJS("$('#ban-" . $model->id . "').click(function(e) {
    $.get('/group/ban-member', {id: " . $model->group_id . ", uid: " . $model->user_id . "}, function(data) {
        $('#ban-modal').modal('show').find('#ban-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>

<?php $this->registerJS("$('#restore-" . $model->id . "').click(function(e) {
    $.get('/group/restore', {id: " . $model->group_id . ", uid: " . $model->user_id . "}, function(data) {
        $('#restore-modal').modal('show').find('#restore-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>

<?php $this->registerJS("$('#contact-" . $model->id . "').click(function(e) {
    $.get('/group/contact-member', {id: " . $model->group_id . ", uid: " . $model->user_id . "}, function(data) {
        $('#contact-modal').modal('show').find('#contact-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>