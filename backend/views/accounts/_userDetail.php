<?php

use common\models\User;
use common\models\profile\Profile;
use common\models\Utility;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
// use yii\helpers\HtmlPurifier;
?>

<div class="user-detail">
	<div class="picture-name">
		<div class="picture">
			<?= $user->usr_image ? Html::img('@images/user.png') : Html::img('@images/user.png') ?>
		</div>
		<div class="name">
			<h2><?= $user->fullName ?></h2>
			<h5><em><?= $user->first_name . ' ' . $user->last_name ?></em></h5>
		</div>
	</div>
	<p>Home Church: <?= $church ? $church->org_name : '--' ?></p>
	<p>Primary Role: <?= $user->primary_role ?? '--' ?></p>
	<p>Username: <?= $user->username ?></p> 
	<?php if ($user->status == User::STATUS_DELETED) {
    	echo '<p>Status:&nbsp;<span style="color:orange">Deleted</span></p>';
    } elseif ($user->status == User::STATUS_ACTIVE) {
    	echo '<p>Status:&nbsp;<span style="color:green">Active</span></p>';
    } elseif ($user->status == User::STATUS_BANNED) {
    	echo '<p>Status:&nbsp;<span style="color:red">Banned</span></p>';  
    }?>
	<p>Auth Assignment: <?= array_keys(Yii::$app->authManager->getRolesByUser($user->id))[0] ?></p>
	<p>Email Preferences: <?= $user->emailMaintenance . $user->emailPrefProfile . $user->emailPrefLinks . $user->emailPrefComments . $user->emailPrefFeatures . $user->emailPrefBlog ?></p>
	<p>Created: <?= Yii::$app->formatter->asDate($user->created_at, 'php:Y-m-d'); ?></p>
	<p>Last login: <?= $user->last_login ?> (<?= Utility::time_elapsed_string($user->last_login)?>)</p>
	<p>IP Address: </p>
	<p>Location: </p>
	<p>Profiles: <?= count($profiles) ?? '--' ?></p>
	<?php if ($profiles) {
		foreach ($profiles as $profile) {
			echo '<p class="profile-link">' . Html::a(($profile->category == Profile::CATEGORY_ORG ? $profile->fullName : $profile->formatName), 
				'https://ibnet.org/' . ProfileController::$profilePageArray[$profile->type] . '/' . $profile->url_loc . '/' . $profile->url_name . '/' . $profile->id, ['target' => '_blank']) . '</p>';
		} 
	} ?>
	<p>Comments: <?= $comments ?? '--' ?></p>
	<p>Networks: <?= $networks ?? '--' ?></p>
</div>
