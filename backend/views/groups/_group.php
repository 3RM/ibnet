<?php

use common\models\group\Group;
use yii\bootstrap\Html;
?>

<div class="list-row">
	<p class="col-60 review"><?= $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review', 'id' => $model->id], ['title' => 'Mark reviewed']) ?></p>
	<p class="col-60 button" id=<?= '"group-' . $model->id . '"' ?>><span class="mb-label">ID: </span><?= Html::button($model->id, ['class' => 'btn-link']) . '</span>' ?></p>
    <p class="col-60 button" id=<?= '"user-' . $model->id . '-' . $model->user_id . '"' ?>><span class="mb-label">UID: </span><?= Html::button($model->user_id, ['class' => 'btn-link']) ?></p>
    <p class="col-300"><span class="mb-label">Name: </span><?= $model->name ?></p>
    <p class="col-100"><span class="mb-label">Level: </span>
        <?php if ($model->group_level == Group::LEVEL_LOCAL) {
            $level = 'Local';
        } elseif ($model->group_level == Group::LEVEL_REGIONAL) {
            $level = 'Regional';
        } elseif ($model->group_level == Group::LEVEL_STATE) {
            $level = 'State';
        } elseif ($model->group_level == Group::LEVEL_NATIONAL) {
            $level = 'National';
        } elseif ($model->group_level == Group::LEVEL_INTERNATIONAL) {
            $level = 'International';
        } else {
            $level = '';
        } ?>
        <span class=<?= '"' . $level . '"' ?>><?= $level ?></span>
    </p>
    <p class="col-60"><span class="mb-label">Prayer: </span><?= $model->feature_prayer ? Html::icon('ok') : '' ?></p>
    <p class="col-60"><span class="mb-label">Calendar: </span><?= $model->feature_calendar ? Html::icon('ok') : '' ?></p>
    <p class="col-60"><span class="mb-label">Notification: </span><?= $model->feature_notification ? Html::icon('ok') : '' ?></p>
    <p class="col-60"><span class="mb-label">Forum: </span><?= $model->feature_forum ? Html::icon('ok') : '' ?></p>
    <p class="col-60"><span class="mb-label">Update: </span><?= $model->feature_update ? Html::icon('ok') : '' ?></p>
    <p class="col-60"><span class="mb-label">Documents: </span><?= $model->feature_document ? Html::icon('ok') : '' ?></p>
    <p class="col-60"><span class="mb-label">Donations: </span><?= $model->feature_donation ? Html::icon('ok') : '' ?></p>
    <p class="col-100"><span class="mb-label">Status: </span>
        <?php if ($model->status == Group::STATUS_NEW) {
            $status = 'New';
        } elseif ($model->status == Group::STATUS_ACTIVE) {
            $status = 'Active';
        } elseif ($model->status == Group::STATUS_INACTIVE) {
            $status = 'Inactive';
        } elseif ($model->status == Group::STATUS_TRASH) {
            $status = 'Trash';
        } else {
            $satus = '';
        }?>
        <span class=<?= '"' . $status . '"' ?>><?= $status ?></span>
    </p>
</div>

<?php $this->registerJS("$('#group-" . $model->id . "').click(function(e) {
    $.get('/groups/view-detail', {id: " . $model->id . "}, function(data) {
        $('#group-detail-modal').modal('show').find('#group-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
<?php $this->registerJS("$('#user-" . $model->id . '-' . $model->user_id . "').click(function(e) {
    $.get('/accounts/view-detail', {id: " . $model->user_id . "}, function(data) {
        $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
