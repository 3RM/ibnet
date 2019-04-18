<?php

use common\models\User;
use yii\bootstrap\Html;
$assignment = array_keys(Yii::$app->authManager->getRolesByUser($model->id))[0];
?>

<div class="list-row">
	<p class="col-60 review"><?= $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review', 'id' => $model->id], ['title' => 'Mark reviewed']) ?></p>
	<p class="col-60 button" id=<?= '"user-' . $model->id . '"' ?>><span class="mb-label">ID: </span><?= Html::button($model->id, ['class' => 'btn-link']) . '</span>' ?></p>
    <p class="col-180"><span class="mb-label">Name: </span><?= $model->fullName ?></p>
    <p class="col-240 wrap-anywhere"><span class="mb-label">Username: </span><?= $model->username ?></p>
    <p class="col-300 wrap-anywhere"><span class="mb-label">Email: </span><?php if ($model->email) {
        	echo $model->email;
        } elseif ($model->new_email) {
        	echo '<span style="color:red">' . $model->new_email . '</span>';
        } ?>
    </p>
    <p class="col-150"><span class="mb-label">Role: </span><span class=<?= '"' . $assignment . '"' ?>><?= $assignment ?></span></p>    
    <p class="col-150"><span class="mb-label">Last Login: </span><?= Yii::$app->formatter->asDate($model->last_login); ?></p>
    <p class="col-100"><span class="mb-label">Status: </span>
        <?php if ($model->status == User::STATUS_DELETED) {
            $status = 'Deleted';
        } elseif ($model->status == User::STATUS_ACTIVE) {
            $status = 'Active';
        } elseif ($model->status == User::STATUS_BANNED) {
            $status = 'Banned';
        } ?>
        <span class=<?= '"' . $status . '"' ?>><?= $status ?></span>
    </p>
</div>

<?php $this->registerJS("$('#user-" . $model->id . "').click(function(e) {
    $.get('/accounts/view-detail', {id: " . $model->id . "}, function(data) {
        $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
