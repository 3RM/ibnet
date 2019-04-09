<?php

use common\models\User;
use common\models\profile\Profile;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
// use yii\helpers\HtmlPurifier;
?>

<div class="list-row" <?= $model->inappropriate ? 'style="background-color:red;"' : NULL ?> <?= $model->status == Profile::STATUS_TRASH ? 'style="color:#ccc;"' : NULL ?>>
	<p class="col-60 review"><?= $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-profile', 'id' => $model->id]) ?></p>
	<p class="col-60 button" id=<?= '"profile-' . $model->id . '"' ?>><?= Html::button($model->id, ['class' => 'btn-link']) ?></p>
    <p class="col-60 button" id=<?= '"user-' . $model->id . '-' . $model->user_id . '"' ?>><?= Html::button($model->user_id, ['class' => 'btn-link']) ?></p>
    <p class="col-100"><?= $model->type ?></p>
    <p class="col-180"><?= $model->category == Profile::CATEGORY_ORG ? $model->org_name : $model->formatName ?></p>
    <p class="col-150"><?= $model->created_at ?></p>
    <p class="col-150"><?= $model->renewal_date ?></p>
    <p class="col-60">
        <?php if ($model->status == Profile::STATUS_NEW) {
                echo '<span style="color:blue">New</span>';
            } elseif ($model->status == Profile::STATUS_ACTIVE) {
                echo '<span style="color:green">Active</span>';
            } elseif ($model->status == Profile::STATUS_INACTIVE) {
                echo '<span style="color: orange;">Inactive</span>';  
            } elseif ($model->status == Profile::STATUS_EXPIRED) {
                echo '<span style="color: red;">Expired</span>';  
            } elseif ($model->status == Profile::STATUS_TRASH) {
                echo '<span style="color: #CCC;">Trash</span>';    
            }
        ?>
    </p>
</div>

<?php $this->registerJS("$('#profile-" . $model->id . "').click(function(e) {
    $.get('/directory/profile-detail', {id: " . $model->id . "}, function(data) {
        $('#profile-detail-modal').modal('show').find('#profile-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
<?php $this->registerJS("$('#user-" . $model->id . '-' . $model->user_id . "').click(function(e) {
    $.get('/accounts/account-detail', {id: " . $model->user_id . "}, function(data) {
        $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
 