<?php

use common\models\group\GroupMember;
use yii\bootstrap\Html;
?>

<div class="detail-head">
	<div class="picture">
        <?= empty($member->image) ? Html::img('@img.profile/profile-logo.png', ['class' => '']) : Html::img(Yii::$app->params['url.frontend'] . $member->image) ?>
	</div>
	<div class="name">
		<h2><?= $member->fullName ?></h2>
        <p><em>Primary Role: <?= $member->primaryRole ?></em></p>
	</div>
</div>

<div id="group-detail" class="detail">
	<p>ID: <?= $member->id ?></p>
    <p>Group: <?= $member->group->name ?> (<?= $member->group_id ?>)</p>
    <p>User: <?= $member->user_id ?></p>
    <p>Profile: <?= $member->profile_id ?></p>
    <p>Missionary: <?= $member->missionary_id ?></p>
    <p>Group Owner: <?= $member->group_owner ? 'Yes' : 'No' ?></p>
    <p>
        Created: <?= Yii::$app->formatter->asDate($member->created_at, 'php:Y-m-d') ?> 
        <span class="ago">(<?= Yii::$app->formatter->asRelativeTime($member->created_at, time())?>)</span>
    </p>
	<p>Status: 
		<?php if ($member->status == GroupMember::STATUS_PENDING) {
                echo '<span style="color:blue">Pending</span>';
            } elseif ($member->status == GroupMember::STATUS_ACTIVE) {
                echo '<span style="color:green">Active</span>';
            } elseif ($member->status == GroupMember::STATUS_LEFT) {
                echo '<span style="color: #ccc;">Left</span>';  
            } elseif ($member->status == GroupMember::STATUS_REMOVED) {
                echo '<span style="color: #ccc;">Trash</span>';    
            } elseif ($member->status == GroupMember::STATUS_BANNED) {
                echo '<span style="color: red;">Banned</span>';
            }
        ?>
    </p>
    <p>
        Approved: <?= Yii::$app->formatter->asDate($member->approval_date, 'php:Y-m-d') ?> 
        <span class="ago">(<?= Yii::$app->formatter->asRelativeTime($member->approval_date, time())?>)</span>
    </p>
    <p>
        Inactivated: <?= Yii::$app->formatter->asDate($member->inactivate_date, 'php:Y-m-d') ?> 
        <span class="ago">(<?= Yii::$app->formatter->asRelativeTime($member->inactivate_date, time())?>)</span>
    </p>
    <p>Show Updates: <?= $member->show_updates ? 'Yes' : 'No' ?></p>
    <p>Prayer Alert: <?= $member->email_prayer_alert ? 'Yes' : 'No' ?></p>
    <p>Prayer Weekly Summary: <?= $member->email_prayer_summary ? 'Yes' : 'No' ?></p>
    <p>Update Alert: <?= $member->email_update_alert ? 'Yes' : 'No' ?></p>
</div>