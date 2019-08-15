<?php

/* @var $this yii\web\View */

use common\models\profile\Profile;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

$this->title = 'Flagged Profiles';
?>

<div class="site-index">

	<div class="header-row">
		<p class="col-60">ID</p>
		<p class="col-60">UID</p>
		<p class="col-100">Type</p>
		<p class="col-180">Name</p>
		<p class="col-150">Created</p>
		<p class="col-150">Last Update</p>
		<p class="col-100">Status</p>
	</div>
	
	<?php foreach ($flaggedProfiles as $profile) { ?>
		<div class="list-row flagged" <?= $profile->status == Profile::STATUS_TRASH ? 'style="color: #ccc;"' : NULL ?>>
			<p class="col-60" id=<?= '"profile-' . $profile->id . '"' ?>><?= Html::button($profile->id, ['class' => 'btn-link']) ?></p>
    		<p class="col-60" id=<?= '"user-' . $profile->id . '-' . $profile->user_id . '"' ?>><?= Html::button($profile->user_id, ['class' => 'btn-link']) ?></p>
    		<p class="col-100"><?= $profile->type ?></p>
    		<p class="col-180"><?= $profile->category == Profile::CATEGORY_ORG ? $profile->org_name : $profile->formatName ?></p>
    		<p class="col-150"><?= Yii::$app->formatter->asDate($profile->created_at, 'php:Y-m-d') ?></p>
    		<p class="col-150"><?= $profile->last_update ?></p>
    		<p class="col-100">
        		<?php if ($profile->status == Profile::STATUS_NEW) {
                    $status = 'New';
                } elseif ($profile->status == Profile::STATUS_ACTIVE) {
                    $status = 'Active';
                } elseif ($profile->status == Profile::STATUS_INACTIVE) {
                    $status = 'Inactive';
                } elseif ($profile->status == Profile::STATUS_EXPIRED) {
                    $status = 'Expired';
                } elseif ($profile->status == Profile::STATUS_TRASH) {
                    $status = 'Trash';
                } ?>
                <span class=<?= '"' . $status . '"' ?>><?= $status ?></span>
        	</p>
            <?php $form = ActiveForm::begin(['action' => '/directory/flagged', 'id' => 'clear-id']); ?>
        	<?= Html::submitButton(Html::icon('thumbs-up'), [
                'name' => 'clear',
                'value' => $profile->id,
                'method' => 'post',
                'title' => 'Clear Flag',
                'class' => 'btn-link action',
            ]); ?>
            <?php $form = ActiveForm::end(); ?>
            <?= Html::button(Html::icon('ban-circle'), ['id' => 'profile-ban-' . $profile->id, 'class' => 'btn-link action', 'title' => 'Ban profile']) ?> 
        </div>
        <!-- Flagged profile detail -->
        <?php $this->registerJS("$('#profile-" . $profile->id . "').click(function(e) {
            $.get('/directory/view-detail', {id: " . $profile->id . "}, function(data) {
                $('#profile-detail-modal').modal('show').find('#profile-detail-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <!-- Flagged user detail -->
        <?php $this->registerJS("$('#user-" . $profile->id . '-' . $profile->user_id . "').click(function(e) {
            $.get('/accounts/view-detail', {id: " . $profile->user_id . "}, function(data) {
                $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <!-- Ban meta (description) -->
        <?php $this->registerJS("$('#profile-ban-" . $profile->id . "').click(function(e) {
            $.get('/directory/view-ban', {id: " . $profile->id . "}, function(data) {
                $('#ban-meta-modal').modal('show').find('#ban-meta-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
    <?php } ?>

	<div class="margin-200"></div>
	<section class="content-header">
		<h1 style="margin-bottom:15px;">Banned Profiles</h1>
	</section>
	<div class="header-row">
		<p class="col-60">ID</p>
		<p class="col-60">UID</p>
		<p class="col-100">Type</p>
		<p class="col-180">Name</p>
		<p class="col-150">Created</p>
		<p class="col-150">Last Update</p>
		<p class="col-100">Status</p>
	</div>

	<?php foreach ($bannedProfiles as $profile) { ?>
		<div class="list-row flagged" <?= $profile->status == Profile::STATUS_TRASH ? 'style="color: #CCC;"' : NULL ?>>
			<p class="col-60" id=<?= '"banned-profile-' . $profile->id . '"' ?>><?= Html::button($profile->id, ['class' => 'btn-link']) ?></p>
    		<p class="col-60" id=<?= '"banned-user-' . $profile->id . '-' . $profile->user_id . '"' ?>><?= Html::button($profile->user_id, ['class' => 'btn-link']) ?></p>
    		<p class="col-100"><?= $profile->type ?></p>
    		<p class="col-180"><?= $profile->category == Profile::CATEGORY_ORG ? $profile->org_name : $profile->formatName ?></p>
    		<p class="col-150"><?= Yii::$app->formatter->asDate($profile->created_at, 'php:Y-m-d') ?></p>
    		<p class="col-150"><?= $profile->last_update ?></p>
    		<p class="col-100">(<span class="Banned">Banned</span>)
        	</p>
            <?= Html::button(Html::icon('thumbs-up'), ['id' => 'profile-restore-' . $profile->id, 'class' => 'btn-link action', 'title' => 'Restore']) ?>
            <?php $form = ActiveForm::begin(['action' => '/directory/flagged', 'id' => 'delete-id']); ?>
            <?= Html::submitButton(Html::icon('remove'), [
                'name' => 'delete',
                'value' => $profile->id,
                'method' => 'post',
                'title' => 'Hard Delete',
                'class' => 'btn-link action',
                'onclick' => 'return confirm("Are you sure you want to permanently delete this profile?  Click to confirm.")'
            ]); ?> 
            <?php $form = ActiveForm::end(); ?>
            <?= Html::button('<i class="fa fa-history"></i>', ['id' => 'ban-history-' . $profile->id, 'class' => 'btn-link action', 'title' => 'Ban history']) ?>
        </div>
        <!-- Banned profile detail -->
        <?php $this->registerJS("$('#banned-profile-" . $profile->id . "').click(function(e) {
            $.get('/directory/view-detail', {id: " . $profile->id . "}, function(data) {
                $('#banned-profile-detail-modal').modal('show').find('#banned-profile-detail-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <!-- Banned user detail -->
        <?php $this->registerJS("$('#banned-user-" . $profile->id . '-' . $profile->user_id . "').click(function(e) {
            $.get('/accounts/view-detail', {id: " . $profile->user_id . "}, function(data) {
                $('#banned-user-detail-modal').modal('show').find('#banned-user-detail-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <!-- Ban meta (description) -->
        <?php $this->registerJS("$('#profile-restore-" . $profile->id . "').click(function(e) {
            $.get('/directory/view-ban', {id: " . $profile->id . "}, function(data) {
                $('#ban-meta-modal').modal('show').find('#ban-meta-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <!-- Ban meta (description) -->
        <?php $this->registerJS("$('#ban-history-" . $profile->id . "').click(function(e) {
            $.get('/directory/view-history', {id: " . $profile->id . "}, function(data) {
                $('#ban-history-modal').modal('show').find('#ban-history-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
	<?php } ?>

</div>

<!-- Flagged profile detail -->
<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'profile-detail-modal',
    'headerOptions' => ['class' => 'modal-header-profile'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="profile-detail-content"></div>';
Modal::end(); ?>

<!-- Flagged user detail -->
<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>

<!-- Banned profile detail -->
<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'banned-profile-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="banned-profile-detail-content"></div>';
Modal::end(); ?>

<!-- Banned user detail -->
<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'banned-user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="banned-user-detail-content"></div>';
Modal::end(); ?>

<!-- Ban meta (description) -->
<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'ban-meta-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="ban-meta-content"></div>';
Modal::end(); ?>

<!-- Ban history -->
<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'ban-history-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="ban-history-content"></div>';
Modal::end(); ?>