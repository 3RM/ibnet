<?php

use common\models\User;
use common\models\profile\Profile;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
// use yii\helpers\HtmlPurifier;
?>

<div class=<?= '"list-row ' . ($model->inappropriate ? ' row-flagged' : NULL) . ($model->status == Profile::STATUS_TRASH ? ' row-trash' : NULL) . '"' ?>>
	<p class="col-60 review"><?= $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-profile', 'id' => $model->id], ['title' => 'Mark reviewed']) ?></p>
	<p class="col-60 button" id=<?= '"profile-' . $model->id . '"' ?>><span class="mb-label">ID: </span><?= Html::button($model->id, ['class' => 'btn-link']) ?></p>
    <p class="col-60 button" id=<?= '"user-' . $model->id . '-' . $model->user_id . '"' ?>><span class="mb-label">UID: </span><?= Html::button($model->user_id, ['class' => 'btn-link']) ?></p>
    <p class="col-100"><span class="mb-label">Type: </span><?= $model->type ?></p>
    <p class="col-180"><span class="mb-label">Name: </span><?= $model->category == Profile::CATEGORY_ORG ? $model->org_name : $model->formatName ?></p>
    <p class="col-150"><span class="mb-label">Created: </span><?= Yii::$app->formatter->asDate($model->created_at, 'php:Y-m-d') ?></p>
    <p class="col-150"><span class="mb-label">Renewal: </span><?= $model->renewal_date ?></p>
    <p class="col-100"><span class="mb-label">Status: </span>
        <?php if ($model->status == Profile::STATUS_NEW) {
            $status = 'New';
        } elseif ($model->status == Profile::STATUS_ACTIVE) {
            $status = 'Active';
        } elseif ($model->status == Profile::STATUS_INACTIVE) {
            $status = 'Inactive';
        } elseif ($model->status == Profile::STATUS_EXPIRED) {
            $status = 'Expired';
        } elseif ($model->status == Profile::STATUS_TRASH) {
            $status = 'Trash';
        } ?>
        <span class=<?= '"' . $status . '"' ?>><?= $status ?></span>
    </p>
</div>

<?php $this->registerJS("$('#profile-" . $model->id . "').click(function(e) {
    $.get('/directory/view-detail', {id: " . $model->id . "}, function(data) {
        $('#profile-detail-modal').modal('show').find('#profile-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
<?php $this->registerJS("$('#user-" . $model->id . '-' . $model->user_id . "').click(function(e) {
    $.get('/accounts/view-detail', {id: " . $model->user_id . "}, function(data) {
        $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
 