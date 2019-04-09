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
			<p class="col-60">
    		    <?php if ($profile->status == Profile::STATUS_NEW) {
    		        echo '<span style="color:blue">' . $profile->id . '</span>';
    		    } elseif ($profile->status == Profile::STATUS_ACTIVE) {
    		        echo '<span style="color:green">' . $profile->id . '</span>';
    		    } elseif ($profile->status == Profile::STATUS_INACTIVE) {
    		        echo '<span style="color: orange;">' . $profile->id . '</span>'; 
    		    } elseif ($profile->status == Profile::STATUS_EXPIRED) {
    		        echo '<span style="color: red;">' . $profile->id . '</span>';  
    		    } else {
    		        echo '<span style="color: #CCC;">' . $profile->id . '</span>';    
    		    } ?>    
    		</p>
    		<p class="col-60"><?= Html::a($profile->user_id, ['accounts/view', 'id' => $profile->user_id]) ?></p>
    		<p class="col-100"><?= $profile->type ?></p>
    		<p class="col-180"><?= $profile->category == Profile::CATEGORY_ORG ? $profile->org_name : $profile->formattedNames ?></p>
    		<?= $form->field($profile, 'email')->textInput()->label(false) ?>
    		<p class="col-300"><?= $profile->email_pvt ?></p>
    		<p class="col-180"><?= $profile->email_pvt_status == Profile::PRIVATE_EMAIL_PENDING ? 'Pending (20)' : NULL ?></p>
    		<?= Html::a(Html::icon('check'), ['activate-forward', 'id' => $profile->id], ['class' => 'action']) ?>
    		<?= Html::a(Html::icon('unchecked'), ['cancel-forward', 'id' => $profile->id], ['class' => 'action']) ?>
		</div>
	<?php } ?>
	<?php $form = ActiveForm::end(); ?>

</div>
<!-- <script src="https://use.fontawesome.com/1db1e4efa2.js"></script> -->
