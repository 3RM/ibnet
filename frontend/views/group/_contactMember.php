<?php

use common\models\group\Group;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField; 
/* @var $this yii\web\View */
?>

<p>Send a message to user's registered email. The "From" address will be your registered email.</p>
<?php $form = ActiveForm::begin(['action' => '/group/contact-member']); ?>
<div class="contact-email">
	<div class="row">
	    <div class="col-md-2">
	        <b>From:</b>
	    </div>
	    <div class="col-md-10">
	        <?= Html::textInput('to', $owner->fullName, ['readonly' => true]) ?>
	    </div>
	</div>
 	<div class="row">
	    <div class="col-md-2">
	        <b>To:</b>
	    </div>
	    <div class="col-md-10">
	        <?= Html::textInput('to', $user->fullName, ['readonly' => true]) ?>
	    </div>
	</div>
    <div class="row">
        <div class="col-md-2">
            <b class="man-required">Subject:</b>
        </div>
        <div class="col-md-10">
            <?= $form->field($group, 'subject')->textInput()->label(false) ?>
            <?= $form->field($group, 'id')->hiddenInput(['value' => $group->id])->label(false) ?>
            <?= $form->field($group, 'user_id')->hiddenInput(['value' => $group->user_id])->label(false) ?>
            <?= $form->field($group, 'name')->hiddenInput(['value' => $group->name])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
			<?= $form->field($group, 'message')->textArea()->label('Message:') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= Html::submitButton('Send', [
			    'method' => 'POST',
			    'class' => 'btn btn-primary longer',
			    'name' => 'contact',
			    'value' => $user->id,
			]); ?>
        </div>
    </div>
</div>

<?php $form = ActiveForm::end(); ?>