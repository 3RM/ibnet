<?php

use common\models\User;
use common\models\profile\Profile;
use common\models\Utility;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
?>



<div class="detail-head">
	<div class="picture">
		<?= $user->usr_image ? Html::img(Yii::$app->params['frontendUrl'] . $user->usr_image) : Html::img('@images/user.png') ?>
	</div>
	<div class="name">
		<h2><?= $user->fullName ?></h2>
		<h5><em><?= $user->first_name . ' ' . $user->last_name ?></em></h5>
	</div>
	<div class="actions">
		<?= Html::button('<span class="glyphicon glyphicon-list-alt"></span>', ['class' => 'btn-link', 'id' => 'user-detail-btn', 'title' => 'Details']) ?>
		<?= Html::button(Html::icon('edit'), ['class' => 'btn-link', 'id' => 'user-edit-btn', 'title' => 'Edit']) ?>
		<?= $user->status == User::STATUS_ACTIVE ? 
			Html::button(Html::icon('ban-circle'), ['class' => 'btn-link', 'id' => 'user-ban-btn', 'title' => 'Ban']) :
			NULL ?>
		<?= $user->status == User::STATUS_BANNED || $user->status == User::STATUS_DELETED ? 
			Html::button(Html::icon('ok-circle'), ['class' => 'btn-link', 'id' => 'user-restore-btn', 'title' => 'Restore Account']) :
			NULL ?>
		<?= $user->status == User::STATUS_BANNED || !empty($hasBanHistory) ? 
			Html::button('<i class="fa fa-history"></i>', ['class' => 'btn-link', 'id' => 'user-history-btn', 'title' => 'Ban history']) :
			NULL ?>
		<?= Html::button(Html::icon('user'), ['class' => 'btn-link', 'id' => 'user-role-btn', 'title' => 'RBAC Role']) ?>
	</div>
</div>

<div id="user-detail" class="detail">

	<p>ID: <?= $user->id ?></p>
	<p>Role: <?= array_keys(Yii::$app->authManager->getRolesByUser($user->id))[0] ?></p>
	<p>Status:&nbsp;
		<?php if ($user->status == User::STATUS_DELETED) {
    		echo '<span style="color:orange">Deleted</span>';
    	} elseif ($user->status == User::STATUS_ACTIVE) {
    		echo '<span style="color:green">Active</span>';
    	} elseif ($user->status == User::STATUS_BANNED) {
    		echo '<span style="color:red">Banned</span>';
    	}?>
    </p>
    <p>First & Last Name: <?= $user->first_name . ' ' . $user->last_name ?></p>
    <?= $user->display_name ? '<p>Display Name: ' . $user->display_name . '</p>' : NULL ?>
	<p>Home Church: <?= $church ? $church->org_name . ', ' . $church->org_city . ', ' . ($church->org_country == 'United States' ? $church->org_st_prov_reg : $church->org_country) : '--' ?></p>
	<p>Primary Role: <?= $user->primary_role ?? '--' ?></p>
	<p>Username: <?= $user->username ?></p>
	<p>Email: <?= $user->email ?? '--' ?></p>
	<?= $user->new_email ? '<p>New Email: ' . $user->new_email . '</p>' : NULL ?>
	<?= $user->new_email_token ? '<p>New Email Token: ' . $user->new_email_token . '</p>' : NULL ?>
	<p>Email Preferences: <?= $user->emailMaintenance . $user->emailPrefProfile . $user->emailPrefLinks . $user->emailPrefComments . $user->emailPrefFeatures . $user->emailPrefBlog ?></p>
	<p>Created: <?= Yii::$app->formatter->asDate($user->created_at, 'php:Y-m-d') ?> <span class="ago">(<?= Utility::time_elapsed_string(Yii::$app->formatter->asDate($user->created_at, 'php:Y-m-d'))?>)</span></p>
	<p>Last login: <?= $user->last_login ?> <span class="ago">(<?= Utility::time_elapsed_string($user->last_login)?>)</span></p>
	<p>IP Address: <?= $user->ip ?? '--' ?></p>
	<p>Location: </p>
	<?= $user->password_reset_token ? '<p>Password Reset Token: ' . $user->password_reset_token . '</p>' : NULL ?>
	<p>Profiles: <?= count($profiles) ?? '--' ?></p>
	<?php if ($profiles) {
		foreach ($profiles as $profile) { ?>
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
            } elseif ($profile->status == Profile::STATUS_BANNED) {
            	$status = 'Banned';
            } ?>	
			<p class=<?= '"indent ' . $status . '"' ?>>
				<?= $profile->image2 ? Html::img(Yii::$app->params['frontendUrl'] . $profile->image2 . ' ') : NULL ?>
				<?= $profile->status == Profile::STATUS_ACTIVE ?
					Html::a(($profile->category == Profile::CATEGORY_ORG ? $profile->org_name : $profile->formatName), 
							'https://ibnet.org/' . ProfileController::$profilePageArray[$profile->type] . '/' . $profile->url_loc . '/' . $profile->url_name . '/' . $profile->id, ['target' => '_blank']) :
					($profile->category == Profile::CATEGORY_ORG ? $profile->org_name : $profile->formatName) ?> 
					(<span class=<?= '"' . $status . '"' ?>><?= $status ?></span>)		 
			</p>
		<?php } 
	} ?>
	<p>Comments: <?= $comments ?? '--' ?></p>
	<p>Networks: <?= $networks ?? '--' ?></p>

</div>

<div id="user-edit" class="detail"></div>
<div id="user-ban" class="detail"></div>
<div id="user-restore" class="detail"></div>
<div id="user-history" class="detail"></div>
<div id="user-role" class="detail"></div>

<?php $this->registerJs("$('#user-detail-btn').click(function(e) {
	$('#user-detail').fadeIn();
	$('#user-edit').fadeOut();
	$('#user-ban').fadeOut();
	$('#user-restore').fadeOut();
	$('#user-history').fadeOut();
	$('#user-role').fadeOut();
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#user-edit-btn').click(function(e) {
	$('#user-detail').fadeOut();
	$('#user-edit').fadeIn();
	$('#user-ban').fadeOut();
	$('#user-restore').fadeOut();
	$('#user-history').fadeOut();
	$('#user-role').fadeOut();
	$.get('/accounts/view-edit', {id: " . $user->id . "}, function(data) {
        $('#user-edit').html(data);
    })
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#user-ban-btn').click(function(e) {
	$('#user-detail').fadeOut();
	$('#user-edit').fadeOut();
	$('#user-ban').fadeIn();
	$('#user-restore').hide();
	$('#user-history').fadeOut();
	$('#user-role').hide();
	$.get('/accounts/view-ban', {id: " . $user->id . "}, function(data) {
        $('#user-ban').html(data);
    })
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#user-restore-btn').click(function(e) {
	$('#user-detail').fadeOut();
	$('#user-edit').fadeOut();
	$('#user-ban').fadeOut();
	$('#user-restore').fadeIn();
	$('#user-history').fadeOut();
	$('#user-role').fadeOut();
	$.get('/accounts/view-ban', {id: " . $user->id . "}, function(data) {
        $('#user-restore').html(data);
    })
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#user-history-btn').click(function(e) {
	$('#user-detail').fadeOut();
	$('#user-edit').fadeOut();
	$('#user-ban').fadeOut();
	$('#user-restore').fadeOut();
	$('#user-history').fadeIn();
	$('#user-role').fadeOut();
	$.get('/accounts/view-history', {id: " . $user->id . "}, function(data) {
        $('#user-history').html(data);
    })
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#user-role-btn').click(function(e) {
	$('#user-detail').fadeOut();
	$('#user-edit').fadeOut();
	$('#user-ban').fadeOut();
	$('#user-restore').fadeOut();
	$('#user-history').fadeOut();
	$('#user-role').fadeIn();
	$.get('/accounts/view-role', {id: " . $user->id . "}, function(data) {
        $('#user-role').html(data);
    })
})", \yii\web\View::POS_READY); ?>