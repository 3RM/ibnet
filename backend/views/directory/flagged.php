<?php

/* @var $this yii\web\View */

use common\models\profile\Profile;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

$this->title = 'Flagged Profiles';
?>

<div class="site-index">

	<div class="header-row">
		<p class="col-60"><?= Html::icon('check') ?></p>
		<p class="col-60">ID</p>
		<p class="col-60">UID</p>
		<p class="col-100">Type</p>
		<p class="col-180">Name</p>
		<p class="col-150">Created</p>
		<p class="col-150">Renewal</p>
		<p class="col-100">Status</p>
	</div>
	
	<?php foreach ($flaggedProfiles as $profile) { ?>
		<div class="list-row flagged" <?= $profile->status == Profile::STATUS_TRASH ? 'style="color: #CCC;"' : NULL ?>>
			<p class="col-60 review"><?= $profile->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-profile', 'id' => $profile->id]) ?></p>
			<p class="col-60" id=<?= '"profile-' . $profile->id . '"' ?>><?= Html::button($profile->id, ['class' => 'btn-link']) ?></p>
    		<p class="col-60" id=<?= '"user-' . $profile->id . '-' . $profile->user_id . '"' ?>><?= Html::button($profile->user_id, ['class' => 'btn-link']) ?></p>
    		<p class="col-100"><?= $profile->type ?></p>
    		<p class="col-180"><?= $profile->category == Profile::CATEGORY_ORG ? $profile->org_name : $profile->formatName ?></p>
    		<p class="col-150"><?= $profile->created_at ?></p>
    		<p class="col-150"><?= $profile->renewal_date ?></p>
    		<p class="col-100">
        		<?php if ($profile->status == Profile::STATUS_NEW) {
        		        echo '<span style="color:blue">New</span>';
        		    } elseif ($profile->status == Profile::STATUS_ACTIVE) {
        		        echo '<span style="color:green">Active</span>';
        		    } elseif ($profile->status == Profile::STATUS_INACTIVE) {
        		        echo '<span style="color: orange;">Inactive</span>';  
        		    } elseif ($profile->status == Profile::STATUS_EXPIRED) {
        		        echo '<span style="color: red;">Expired</span>';  
        		    } elseif ($profile->status == Profile::STATUS_TRASH) {
        		        echo '<span style="color: #CCC;">Trash</span>';    
        		    }
        		?>
        	</p>
        	<?= Html::a(Html::icon('check'), ['clear-flag', 'id' => $profile->id], ['class' => 'action']) ?>
    		<?= Html::a(Html::icon('ban-circle'), ['disable-profile', 'id' => $profile->id], ['class' => 'action']) ?>
        </div>
	<?php } ?>

	<div class="margin-200"></div>
	<section class="content-header">
		<h1>Banned Profiles</h1>
	</section>
	<div class="header-row">
		<p class="col-60"><?= Html::icon('check') ?></p>
		<p class="col-60">ID</p>
		<p class="col-60">UID</p>
		<p class="col-100">Type</p>
		<p class="col-180">Name</p>
		<p class="col-150">Created</p>
		<p class="col-150">Renewal</p>
		<p class="col-100">Status</p>
	</div>
	
	<?php foreach ($bannedProfiles as $profile) { ?>
		<div class="list-row flagged" <?= $profile->status == Profile::STATUS_TRASH ? 'style="color: #CCC;"' : NULL ?>>
			<p class="col-60 review"><?= $profile->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-profile', 'id' => $profile->id]) ?></p>
			<p class="col-60" id=<?= '"banned-profile-' . $profile->id . '"' ?>><?= Html::button($profile->id, ['class' => 'btn-link']) ?></p>
    		<p class="col-60" id=<?= '"banned-user-' . $profile->id . '-' . $profile->user_id . '"' ?>><?= Html::button($profile->user_id, ['class' => 'btn-link']) ?></p>
    		<p class="col-100"><?= $profile->type ?></p>
    		<p class="col-180"><?= $profile->category == Profile::CATEGORY_ORG ? $profile->org_name : $profile->formatName ?></p>
    		<p class="col-150"><?= $profile->created_at ?></p>
    		<p class="col-150"><?= $profile->renewal_date ?></p>
    		<p class="col-100">
        		<?php if ($profile->status == Profile::STATUS_NEW) {
        		        echo '<span style="color:blue">New</span>';
        		    } elseif ($profile->status == Profile::STATUS_ACTIVE) {
        		        echo '<span style="color:green">Active</span>';
        		    } elseif ($profile->status == Profile::STATUS_INACTIVE) {
        		        echo '<span style="color: orange;">Inactive</span>';  
        		    } elseif ($profile->status == Profile::STATUS_EXPIRED) {
        		        echo '<span style="color: red;">Expired</span>';  
        		    } elseif ($profile->status == Profile::STATUS_TRASH) {
        		        echo '<span style="color: #CCC;">Trash</span>';    
        		    }
        		?>
        	</p>
        	<?= Html::a(Html::icon('check'), ['clear-flag', 'id' => $profile->id], ['class' => 'action']) ?>
    		<?= Html::a(Html::icon('trash'), ['trash-profile', 'id' => $profile->id], ['class' => 'action']) ?>
        </div>
	<?php } ?>

</div>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'profile-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'profile-detail-modal-body'],
]);
    echo '<div id="profile-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'user-detail-modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'banned-profile-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'profile-detail-modal-body'],
]);
    echo '<div id="baned-profile-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'banned-user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'user-detail-modal-body'],
]);
    echo '<div id="banned-user-detail-content"></div>';
Modal::end(); ?>

<?php $this->registerJS("$('#profile-" . $profile->id . "').click(function(e) {
    $.get('/directory/profile-detail', {id: " . $profile->id . "}, function(data) {
        $('#profile-detail-modal').modal('show').find('#profile-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
<?php $this->registerJS("$('#user-" . $profile->id . '-' . $profile->user_id . "').click(function(e) {
    $.get('/accounts/account-detail', {id: " . $profile->user_id . "}, function(data) {
        $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
<?php $this->registerJS("$('#banned-profile-" . $profile->id . "').click(function(e) {
    $.get('/directory/profile-detail', {id: " . $profile->id . "}, function(data) {
        $('#banned-profile-detail-modal').modal('show').find('#banned-profile-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
<?php $this->registerJS("$('#banned-user-" . $profile->id . '-' . $profile->user_id . "').click(function(e) {
    $.get('/accounts/account-detail', {id: " . $profile->user_id . "}, function(data) {
        $('#banned-user-detail-modal').modal('show').find('#banned-user-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
