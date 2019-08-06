<?php

/* @var $this yii\web\View */

use yii\widgets\ListView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

$this->title = 'Pending Group Emails';
?>

<div class="site-index">

	<div class="header-row">
		<p class="col-60">ID</p>
		<p class="col-60">UID</p>
		<p class="col-300">Name</p>
		<p class="col-240">Prayer Email</p>
		<p class="col-240">Prayer Email Pwd</p>
        <p class="col-240">Notification Email</p>
        <p class="col-240">Notification Email Pwd</p>
	</div>
	
	<?php foreach ($groups as $group) { ?>
		<div class="list-row group">
			<p class="col-60 button" id=<?= '"group-' . $group->id . '"' ?>><?= Html::button($group->id, ['class' => 'btn-link']) ?></p>
            <p class="col-60 button" id=<?= '"user-' . $group->id . '-' . $group->user_id . '"' ?>><?= Html::button($group->user_id, ['class' => 'btn-link']) ?></p>
    		<p class="col-300"><?= $group->name ?></p>
            <?php $form = ActiveForm::begin([
                'id' => 'group-emails-form-' . $group->id, 
                'enableAjaxValidation' => true,
            ]); ?>
    		<?= $form->field($group, 'prayer_email')->textInput(['class' => 'pending-group-emails'])->label(false) ?>
            <?= $form->field($group, 'prayer_email_pwd')->textInput(['class' => 'pending-group-emails'])->label(false) ?>
            <?= $form->field($group, 'notice_email')->textInput(['class' => 'pending-group-emails'])->label(false) ?>
            <?= $form->field($group, 'notice_email_pwd')->textInput(['class' => 'pending-group-emails'])->label(false) ?>
            <?= Html::submitButton(Html::icon('save'), [
                'name' => 'save',
                'value' => $group->id,
                'method' => 'post',
                'title' => 'Save',
                'class' => 'btn-link action',
            ]); ?>
            <?php $form = ActiveForm::end(); ?>
		</div>
        <?php $this->registerJS("$('#group-" . $group->id . "').click(function(e) {
            $.get('/groups/view-detail', {id: " . $group->id . "}, function(data) {
                $('#group-detail-modal').modal('show').find('#group-detail-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <?php $this->registerJS("$('#user-" . $group->id . '-' . $group->user_id . "').click(function(e) {
            $.get('/accounts/view-detail', {id: " . $group->user_id . "}, function(data) {
                $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
	<?php } ?>

</div>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-users"></i></h3>',
    'id' => 'group-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="group-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>
