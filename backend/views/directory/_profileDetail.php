<?php

use common\models\User;
use common\models\profile\Profile;
use common\models\Utility;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
?>

<?= $profile->image1 ? Html::img(Yii::$app->params['frontendUrl'] . $profile->image1, ['class' => 'image1']) : NULL ?>
<div class="detail-head">
	<div class="picture">
		<?= $profile->image2 ? Html::img(Yii::$app->params['frontendUrl'] . $profile->image2) : Html::img('@img.profile/profile-logo.png') ?>
	</div>
	<div class="name">
		<h2><?= $profile->formatName ?></h2>
		<h5><em><?= $profile->tagline ?></em></h5>
		<?= $profile->type . ($profile->sub_type != $profile->type ? ' (' . $profile->sub_type . ')' : NULL) ?>
	</div>
    <div class="actions">
        <?= Html::button('<span class="glyphicon glyphicon-list-alt"></span>', ['class' => 'btn-link', 'id' => 'profile-detail-btn', 'title' => 'Details']) ?>
        <?= Html::button(Html::icon('edit'), ['class' => 'btn-link', 'id' => 'profile-edit-btn', 'title' => 'Edit']) ?>
        <?php if ($profile->status == Profile::STATUS_ACTIVE) {
            echo Html::button('<span class="glyphicon glyphicon-ban-circle"></span>', ['class' => 'btn-link', 'id' => 'profile-inactivate-btn', 'title' => 'Inactivate']);
        } elseif ($profile->status == Profile::STATUS_INACTIVE) {
            echo Html::button('<span class="glyphicon glyphicon-trash"></span>', ['class' => 'btn-link',  'id' => 'profile-trash-btn', 'title' => 'Trash']);
        } elseif ($profile->status == Profile::STATUS_TRASH) {
            echo Html::button('<span class="glyphicon glyphicon-ok-circle"></span>', ['class' => 'btn-link', 'id' => 'profile-inactivate-btn', 'title' => 'Reset to Inactive']);
        } ?>
        <?= Html::button(Html::icon('flag'), ['class' => 'btn-link', 'id' => 'profile-flag-btn', 'title' => 'Flag as inappropriate']) ?>
    </div>
</div>

<div id="profile-detail" class="detail">

	<p>Description: <?= empty($profile->description) ? '<em>No description</em>' : $profile->description ?></p>
	<?= $profile->inappropriate ? '<p style="color:red">' . Html::icon('flag') . ' Profile flagged as inappropriate</p>' : NULL ?>
	<p>ID: <?= $profile->id ?></p>
	<p>Status: 
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
            } elseif ($profile->status == Profile::STATUS_BANNED) {
            	echo '<span style="color: red;">Banned</span>'; 
            }
        ?>
    </p>
    <p>Category: <?= $profile->name ?></p>
    <p>Profile Name: <?= $profile->profile_name ?></p>
    <p>
        Created: <?= Yii::$app->formatter->asDate($profile->created_at, 'php:Y-m-d') ?> 
        <span class="ago">(<?= Utility::time_elapsed_string(Yii::$app->formatter->asDate($profile->created_at, 'php:Y-m-d'))?>)</span>
    </p>
    <p>
        Updated: <?= Yii::$app->formatter->asDate($profile->updated_at, 'php:Y-m-d') ?> 
        <span class="ago">(<?= Utility::time_elapsed_string(Yii::$app->formatter->asDate($profile->updated_at, 'php:Y-m-d'))?>)</span>
    </p>
    <p>
        Last User Update: <?= $profile->last_update ?> 
        <span class="ago">(<?= Utility::time_elapsed_string($profile->last_update)?>)</span>
    </p>
    <p>
        Renewal: <?= $profile->renewal_date ?> 
        <span class="ago">(<?= Utility::time_elapsed_string($profile->renewal_date)?>)</span>
    </p>
    <?= $profile->inactivation_date ? '<p>Inactivated: ' . $profile->inactivation_date . ' (' . Utility::time_elapsed_string($profile->inactivation_date) . ')</p>' : NULL; ?>
    <p>Has been inactivated: <?= $profile->has_been_inactivated ? ' Yes' : ' No' ?></p>
    <p>In edit mode (not new): <?= $profile->edit ? ' Yes' : ' No' ?></p>
    <?= $profile->title ? '<p>Staff Title: ' . $profile->title . '</p>' : NULL; ?>
    <?= $profile->flwsp_ass_level ? '<p>Fellowship/Association level: ' . $profile->flwsp_ass_level . '</p>' : NULL; ?>
    <?= ($profile->org_city && $profile->org_st_prov_reg) ? 
    	'<p>Org address: ' . 
    		$profile->org_address1 . 
    		($profile->org_address2 ? ', ' . $profile->org_address2 : NULL) . 
    		', ' . $profile->org_city . 
    		', ' . $profile->org_st_prov_reg . 
    		($profile->org_po_box ? ' PO Box ' . $profile->org_po_box : NULL) . 
    		($profile->org_country ? ', ' . $profile->org_country : NULL) . 
    	'</p>' : NULL 
    ?>
    <?= ($profile->ind_city && $profile->ind_st_prov_reg) ? 
    	'<p>Ind address: ' . 
    		$profile->ind_address1 . 
    		($profile->ind_address2 ? ', ' . $profile->ind_address2 : NULL) . 
    		', ' . $profile->ind_city . 
    		', ' . $profile->ind_st_prov_reg . 
    		($profile->ind_po_box ? ' PO Box ' . $profile->ind_po_box : NULL) . 
    		($profile->ind_country ? ', ' . $profile->ind_country : NULL) . 
    	'</p>' : NULL 
    ?>
    <?= $profile->org_loc ? '<p>Org coordinates: ' . $profile->org_loc . '</p>' : NULL ?>
    <?= $profile->ind_loc ? '<p>Ind coordinates: ' . $profile->ind_loc . '</p>' : NULL ?>
    <?= $profile->phone ? '<p>Phone: ' . $profile->phone . '</p>' : NULL ?>
    <?= $profile->email ? '<p>Email: ' . $profile->email . '</p>' : NULL ?>
    <?= $profile->email_pvt ? '<p>Private Email: ' . $profile->email_pvt . ($profile->email_pvt_status == Profile::PRIVATE_EMAIL_ACTIVE ? ' (Active)' : NULL) . 
            ($profile->email_pvt_status == Profile::PRIVATE_EMAIL_PENDING ? ' (pending)' : NULL) . '</p>' : NULL ?>
    <?= $profile->website ? '<p>Website: ' . $profile->website . '</p>' : NULL ?>
    <?= ($profile->pastor_interim || $profile->cp_pastor) ? '<p>Pastor: ' . ($profile->pastor_interim ? 'Interim' : NULL) . (($profile->pastor_interim && $profile->cp_pastor) ? ', ' : NULL) . 
            ($profile->cp_pastor ? 'Church-planting' : NULL) . '</p>' : NULL ?>
    <?= $profile->bible ? '<p>Bible: ' . $profile->bible . '</p>' : NULL ?>
    <?= $profile->worship_style ? '<p>Worship: ' . $profile->worship_style . '</p>' : NULL ?>
    <?= $profile->polity ? '<p>Polity: ' . $profile->polity . '</p>' : NULL ?>
    <?= $profile->packet ? '<p>Missions Packet: ' . $profile->packet . '</p>' : NULL ?>
	
</div>

<div id="profile-edit" class="detail"></div>
<div id="profile-inactivate" class="detail"></div>
<div id="profile-trash" class="detail"></div>
<div id="profile-flag" class="detail"></div>

<?php $this->registerJs("$('#profile-detail-btn').click(function(e) {
    $('#profile-detail').fadeIn();
    $('#profile-edit').fadeOut();
    $('#profile-inactivate').hide();
    $('#profile-trash').hide();
    $('#profile-flag').hide();
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#profile-edit-btn').click(function(e) {
    $('#profile-detail').fadeOut();
    $('#profile-edit').fadeIn();
    $('#profile-inactivate').hide();
    $('#profile-trash').hide();
    $('#profile-flag').hide();
    $.get('/directory/view-edit', {id: " . $profile->id . "}, function(data) {
        $('#profile-edit').html(data);
    });
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#profile-inactivate-btn').click(function(e) {
    $('#profile-detail').fadeOut();
    $('#profile-edit').fadeOut();
    $('#profile-inactivate').show();
    $('#profile-trash').hide();
    $('#profile-flag').hide();
     $.get('/directory/view-inactivate', {id: " . $profile->id . "}, function(data) {
        $('#profile-inactivate').html(data);
    });
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#profile-trash-btn').click(function(e) {
    $('#profile-detail').fadeOut();
    $('#profile-edit').fadeOut();
    $('#profile-inactivate').fadeOut();
    $('#profile-trash').show();
    $('#profile-restore').hide();
    $('#profile-flag').hide();
     $.get('/directory/view-trash', {id: " . $profile->id . "}, function(data) {
        $('#profile-trash').html(data);
    });
})", \yii\web\View::POS_READY); ?>

<?php $this->registerJs("$('#profile-flag-btn').click(function(e) {
    $('#profile-detail').fadeOut();
    $('#profile-edit').fadeOut();
    $('#profile-inactivate').hide();
    $('#profile-trash').hide();
    $('#profile-restore').hide();
    $('#profile-flag').show();
     $.get('/directory/view-flag', {id: " . $profile->id . "}, function(data) {
        $('#profile-flag').html(data);
    });
})", \yii\web\View::POS_READY); ?>