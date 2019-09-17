<?php

use common\models\network\Group;
use common\widgets\Alert;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\widgets\ListView;

/* @var $this yii\web\View */
$this->title = 'My Account';
?>

<div class="account-header-container">
    <div class="account-header acc-group-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
</div>

<div class="container">

	<?= Alert::widget() ?>

    <h2 class="top-margin-60"><i class="fas fa-users-cog"></i> Manage Members</h2> 
    For group <span class="lead">"<?= $group->name ?>"</span>
    <br><br>
	
	<?php if ($group->private && $pendingDataProvider->getTotalCount() > 0) { ?>
		<h3>Pending Approval</h3>
    	<div class="member-list">
    	    <?= ListView::widget([
    	        'dataProvider' => $pendingDataProvider,
    	        'showOnEmpty' => false,
    	        'emptyText' => 'No pending approvals',
    	        'itemView' => '_pendingItem',
    	        'viewParams' => ['group' => $group],
    	        'itemOptions' => ['class' => 'item-bordered'],
    	        'layout' => '{items}{pager}',
    	    ]); ?>
    	</div>
    	<div class="top-margin-60"></div>
    <?php } ?>

	<h3>Active Members</h3>
    <div class="member-list">
        <?= ListView::widget([
            'dataProvider' => $memberDataProvider,
            'showOnEmpty' => false,
            'emptyText' => 'No members yet...',
            'itemView' => '_memberItem',
            'viewParams' => ['group' => $group],
            'itemOptions' => ['class' => 'item-bordered'],
            'layout' => '<div class="summary-row hidden-print clearfix">{summary}</div>{items}{pager}',
        ]); ?>
    </div>

	<div class="top-margin-60"></div>
    <?= Html::a('<span class="glyphicons glyphicons-arrow-left" style="margin-top:-3px;"></span> Return', ['my-groups'], ['class' => 'btn btn-primary']) ?>

</div>

<?php Modal::begin([
    'header' => '<h3><i class="fas fa-user-times"></i> Decline Request</h3>',
    'id' => 'decline-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="decline-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3><i class="fas fa-user-times"></i> Remove Member</h3>',
    'id' => 'remove-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="remove-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3><i class="fas fa-user-slash"></i> Ban Member</h3>',
    'id' => 'ban-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="ban-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3><i class="fas fa-user-check"></i> Remove Ban</h3>',
    'id' => 'restore-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="restore-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3><i class="far fa-envelope"></i> Contact User</h3>',
    'id' => 'contact-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="contact-content"></div>';
Modal::end(); ?>