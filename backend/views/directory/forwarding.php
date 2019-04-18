<?php

/* @var $this yii\web\View */

use common\models\profile\Profile;
use yii\widgets\ListView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

$this->title = 'Forwarding Email Requests';
?>

<div class="site-index">

	<div class="header-row">
		<p class="col-60">ID</p>
		<p class="col-60">UID</p>
		<p class="col-100">Type</p>
		<p class="col-180">Name</p>
		<p class="col-300">Public Email</p>
		<p class="col-300">Private Email</p>
		<p class="col-180">Private Email Status</p>
	</div>
	
	<?php $form = ActiveForm::begin(); ?>
	<?php foreach ($profiles as $profile) { ?>
		<div class="list-row">
			<p class="col-60 button" id=<?= '"profile-' . $profile->id . '"' ?>><?= Html::button($profile->id, ['class' => 'btn-link']) ?></p>
            <p class="col-60 button" id=<?= '"user-' . $profile->id . '-' . $profile->user_id . '"' ?>><?= Html::button($profile->user_id, ['class' => 'btn-link']) ?></p>
    		<p class="col-100"><?= $profile->type ?></p>
    		<p class="col-180"><?= $profile->category == Profile::CATEGORY_ORG ? $profile->org_name : $profile->formattedNames ?></p>
    		<?= $form->field($profile, 'email')->textInput(['class' => 'forward-email'])->label(false) ?>
    		<p class="col-300"><?= $profile->email_pvt ?></p>
    		<p class="col-180"><?= $profile->email_pvt_status == Profile::PRIVATE_EMAIL_PENDING ? 'Pending (20)' : NULL ?></p>
            <?= Html::submitButton(Html::icon('save'), [
                'name' => 'save',
                'value' => $profile->id,
                'method' => 'post',
                'title' => 'Save',
                'class' => 'btn-link action',
            ]); ?>
            <?= Html::submitButton(Html::icon('remove'), [
                'name' => 'remove',
                'value' => $profile->id,
                'method' => 'post',
                'title' => 'Cancel Request',
                'class' => 'btn-link action',
            ]); ?>
		</div>
        <?php $this->registerJS("$('#profile-" . $profile->id . "').click(function(e) {
            $.get('/directory/view-detail', {id: " . $profile->id . "}, function(data) {
                $('#profile-detail-modal').modal('show').find('#profile-detail-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <?php $this->registerJS("$('#user-" . $profile->id . '-' . $profile->user_id . "').click(function(e) {
            $.get('/accounts/view-detail', {id: " . $profile->user_id . "}, function(data) {
                $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
	<?php } ?>
	<?php $form = ActiveForm::end(); ?>

</div>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'profile-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="profile-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>
