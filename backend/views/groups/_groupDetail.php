<?php

use common\models\User;
use common\models\group\Group;
use common\models\Utility;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
?>

<div class="detail-head">
	<div class="picture">
        <?= empty($group->image) ? Html::img('@img.profile/profile-logo.png', ['class' => '']) : Html::img(Yii::$app->params['url.frontend'] . $group->image) ?>
	</div>
	<div class="name">
		<h2><?= $group->name . ' ' ?><span style="color:lightgray; font-size:0.8em;"><?= $group->private ? '<i class="fa fa-lock"></i>' : '<i class="fa fa-unlock"></i>' ?></span></h2>
        <p><em>Members: <?= $memberCount ?></em></p>
	</div>
    <div class="actions">
        <?= Html::button('<span class="glyphicon glyphicon-list-alt"></span>', ['class' => 'btn-link', 'id' => 'group-detail-btn', 'title' => 'Details']) ?>
        <?= Html::button(Html::icon('edit'), ['class' => 'btn-link', 'id' => 'group-edit-btn', 'title' => 'Edit']) ?>
        <?php if ($group->status == Group::STATUS_ACTIVE) {
            echo Html::button('<span class="glyphicon glyphicon-ban-circle"></span>', ['class' => 'btn-link', 'id' => 'group-inactivate-btn', 'title' => 'Inactivate']);
        } elseif (($group->status == Group::STATUS_INACTIVE) || ($group->status == Group::STATUS_NEW)) {
            echo Html::button('<span class="glyphicon glyphicon-trash"></span>', ['class' => 'btn-link',  'id' => 'group-trash-btn', 'title' => 'Trash']);
        } ?>
    </div>
</div>

<div id="group-detail" class="detail">

	<p>Description: <?= empty($group->description) ? '<em>No description</em>' : $group->description ?></p>
	<p>ID: <?= $group->id ?></p>
	<p>Status: 
		<?php if ($group->status == Group::STATUS_NEW) {
                echo '<span style="color:blue">New</span>';
            } elseif ($group->status == Group::STATUS_ACTIVE) {
                echo '<span style="color:green">Active</span>';
            } elseif ($group->status == Group::STATUS_INACTIVE) {
                echo '<span style="color: orange;">Inactive</span>';  
            } elseif ($group->status == Group::STATUS_TRASH) {
                echo '<span style="color: #CCC;">Trash</span>';    
            }
        ?>
    </p>
    <p>Level: 
        <?php if ($group->group_level == Group::LEVEL_LOCAL) {
                echo 'Local';
            } elseif ($group->group_level == Group::LEVEL_REGIONAL) {
                echo 'Regional';
            } elseif ($group->group_level == Group::LEVEL_STATE) {
                echo 'State';  
            } elseif ($group->group_level == Group::LEVEL_NATIONAL) {
                echo 'National';    
            }elseif ($group->group_level == Group::LEVEL_INTERNATIONAL) {
                echo 'International';    
            }
        ?>
    </p>
    <p>
        Created: <?= Yii::$app->formatter->asDate($group->created_at, 'php:Y-m-d') ?> 
        <span class="ago">(<?= Yii::$app->formatter->asRelativeTime($group->created_at, time())?>)</span>
    </p>
    <p>
        Last Visit: <?= Yii::$app->formatter->asDate($group->last_visit, 'php:Y-m-d') ?> 
        <span class="ago">(<?= Yii::$app->formatter->asRelativeTime($group->last_visit, time())?>)</span>
    </p>
    <p>Hide on Profiles: <?= $group->hide_on_profiles ?></p>
    <p>Searchable: <?= $group->not_searchable ? 'No' : 'Yes' ?></p>
    <p>Ministry Id: <?= $group->ministry_id ?></p>
    <p>Discourse Group Name: <?= $group->discourse_group_name ?></p>
    <p>Discourse Group Id: <?= $group->discourse_group_id ?></p>
    <p>Discourse Category Id: <?= $group->discourse_category_id ?></p>
    <p>Prayer Feature: <?= $group->feature_prayer ? 'Yes' : 'No' ?></p>
    <p>Calendar Feature: <?= $group->feature_calendar ? 'Yes' : 'No' ?></p>
    <p>Forum Feature: <?= $group->feature_forum ? 'Yes' : 'No' ?></p>
    <p>Updates Feature: <?= $group->feature_update ? 'Yes' : 'No' ?></p>
    <p>Notification Feature: <?= $group->feature_notification ? 'Yes' : 'No' ?></p>
    <p>Document Share Feature: <?= $group->feature_document ? 'Yes' : 'No' ?></p>
    <p>Grace Giving Feature: <?= $group->feature_donation ? 'Yes' : 'No' ?></p>
    <p>Prayer Email: <?= $group->prayer_email ?></p>
    <p>Prayer Email Password: <?= $group->prayer_email_pwd ?></p>
    <p>Notification Email: <?= $group->notice_email ?></p>
    <p>Notification Email Password: <?= $group->notice_email_pwd ?></p>	
</div>

<div id="group-edit" class="detail"></div>
<div id="group-inactivate" class="detail"></div>
<div id="group-trash" class="detail"></div>

<?php $this->registerJs("$('#group-detail-btn').click(function(e) {
    $('#group-detail').fadeIn();
    $('#group-edit').fadeOut();
    $('#group-inactivate').hide();
    $('#group-trash').hide();
    $('#group-flag').hide();
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#group-edit-btn').click(function(e) {
    $('#group-detail').fadeOut();
    $('#group-edit').fadeIn();
    $('#group-inactivate').hide();
    $('#group-trash').hide();
    $('#group-flag').hide();
    $.get('/groups/view-edit', {id: " . $group->id . "}, function(data) {
        $('#group-edit').html(data);
    });
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#group-inactivate-btn').click(function(e) {
    $('#group-detail').fadeOut();
    $('#group-edit').fadeOut();
    $('#group-inactivate').show();
    $('#group-trash').hide();
    $('#group-flag').hide();
     $.get('/groups/view-inactivate', {id: " . $group->id . "}, function(data) {
        $('#group-inactivate').html(data);
    });
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#group-trash-btn').click(function(e) {
    $('#group-detail').fadeOut();
    $('#group-edit').fadeOut();
    $('#group-inactivate').fadeOut();
    $('#group-trash').show();
    $('#group-restore').hide();
    $('#group-flag').hide();
     $.get('/groups/view-trash', {id: " . $group->id . "}, function(data) {
        $('#group-trash').html(data);
    });
})", \yii\web\View::POS_READY); ?>