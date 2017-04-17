<?php
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="row top-margin">
    <div class="col-xs-2">
		<!-- Return Link -->
		<?= Html::a('&laquo; Return to Results',  Url::previous()) ?>
    </div>
    <div class="col-xs-2">
    	<p>&nbsp;</p>
    </div>
    <div class="col-xs-8">
        <!--- Begin Flag as Inappropriate Modal -->
		<span style="float: right;"><?php Modal::begin([
			'header' => '<h3>Flag Inappropriate Content</h3>',
			'toggleButton' => [
				'label' => Html::icon('flag'),
				'alt' => 'Flag innapropriate content',
				'class' => 'btn btn-danger']
		]); ?></span>
		<div class="modal-body">
			<p>
			If you see inappropriate, false, misleading, or otherwise objectionable content in this profile, we want to know!  
			Click below to notify us immediately.  We will review as soon as possible and take 
			appropriate action.
			<p>
				
			<p>Thank you for helping us to keep these profiles clean and safe!</p> 

			<div class="modal-footer">
				<?php $form = ActiveForm::begin([
					'method' => 'post', 
					'action' => ['flag-profile']
				]); ?>
					<?= HTML::submitButton(Html::icon('flag'), [
				        'method' => 'post',
				        'class' => 'btn btn-danger',
				        'name' => 'flag',
				        'value' => $id
				    ]) ?>
				<?php $form = ActiveForm::end(); ?>
			</div>
		</div>
		<?php Modal::end() ?>
		<!--- End Flag as Inappropriate Modal -->
	</div>	
</div>
