<?php
use frontend\assets\AjaxAsset;
use frontend\controllers\ProfileController;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;;

AjaxAsset::register($this);
\Eddmash\Clipboard\ClipboardAsset::register($this);
?>

	<div id="p" class="profile-footer">
		<?= Html::a(Html::icon('chevron-left') . Html::icon('chevron-left', ['class' => 'chevron-left']), Url::previous(), ['class' => 'return']) ?>
		
		<p class="last-updated">Updated <?= Yii::$app->formatter->asDate($profile->last_update) ?></p>
		
		<div class="icons">
			<div id="like-result">
				<?php $likes = $likeCount > 0 ? '<span class="badge">' . $likeCount . '</span>' : NULL; ?>
				<?php if (!Yii::$app->user->isGuest) { ?>
					<?php if ($likes && ($iLike == false)) {
						echo $likes . Html::a(Html::icon('heart'), ['ajax/like', 'iLike' => $iLike, 'likeCount' => $likeCount, 'pid' => $profile->id], [
						    'id' => 'like-id', 
                	        'data-on-done' => 'likeDone', 
                	        'class' => 'ind-icon'
						]);
					} elseif (!$likes && ($iLike == false)) {
						echo Html::a(Html::icon('heart-empty'), ['ajax/like', 'iLike' => $iLike, 'likeCount' => $likeCount, 'pid' => $profile->id], [
						    'id' => 'like-id', 
                	        'data-on-done' => 'likeDone', 
                	        'class' => 'ind-icon']);
					} else {
						echo $likes . Html::a(Html::icon('heart'), ['ajax/like', 'iLike' => $iLike, 'likeCount' => $likeCount, 'pid' => $profile->id], [
						    'id' => 'like-id', 
                	        'data-on-done' => 'likeDone', 
                	        'class' => 'ind-icon heart'
						]); 
					} ?>
					<?php $this->registerJs("$('#like-result').on('click', '#like-id', handleAjaxSpanLink);", \yii\web\View::POS_READY); ?>
				<?php } else { ?>
					<?= $likes ? 
						$likes . Html::icon('heart', ['class' => 'ind-icon']) :
						Html::icon('heart-empty', ['class' => 'ind-icon']); ?>
				<?php } ?>
			</div>	

			<?php Modal::begin([
			'header' => '<h3>Link To This Profile</h3>',
			'toggleButton' => [
				'label' => Html::icon('link'),
				'alt' => 'Flag innapropriate content',
				'class' => 'ind-icon'],
			'headerOptions' => ['class' => 'modal-header'],
			'bodyOptions' => ['class' => 'link-modal-body'],
			]); ?>
				<div class="link-to-profile">

					<div class="right">
						<a href=<?= '"' . Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'id' => $profile->id], 'https') . '"' ?>>Visit us at IBNet</a>
						<a href=<?= '"' . Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'id' => $profile->id], 'https') . '"' ?>><?= Html::img('@web/images/content/ibnet_icon.png', ['alt' => 'IBNet']) ?> Visit us at IBNet</a>
						<a href=<?= '"' . Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'id' => $profile->id], 'https') . '"' ?>><?= Html::img('@web/images/content/ibnet_icon_gr.png', ['alt' => 'IBNet']) ?> Visit us at IBNet</a>
					</div>

					<div class="left">
						<h4>URL:</h4>
						<?= \Eddmash\Clipboard\Clipboard::input($this, 'text', 'URL', Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'id' => $profile->id], 'https'), ['id' => 'url']); ?>
						
						<h4>Text Link:</h4>
						<?= \Eddmash\Clipboard\Clipboard::input($this, 'text', 'URL', Html::a('Visit us at IBNet', Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'id' => $profile->id], 'https')), ['size' => 20, 'id' => 'text-link']); ?>
						
						<h4>Orange Logo Link:</h4>
						<?= \Eddmash\Clipboard\Clipboard::input($this, 'text', 'URL', Html::a(Html::img(Url::toRoute(['/images/content/ibnet_icon.png'], 'https'), ['alt' => 'IBNet']) . 'Visit us at IBNet', Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'id' => $profile->id], 'https')), ['size' => 20, 'id' => 'text-logo']); ?>
											
						<h4>Gray Logo Link:</h4>
						<?= \Eddmash\Clipboard\Clipboard::input($this, 'text', 'URL', Html::a(Html::img(Url::toRoute(['/images/content/ibnet_icon_gr.png'], 'https'), ['alt' => 'IBNet']) . 'Visit us at IBNet', Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'id' => $profile->id], 'https')), ['size' => 20, 'id' => 'text-logo-gr']); ?>
					</div>

				</div>
			<?php Modal::end() ?>
			
			<?php Modal::begin([
			'header' => '<h3>Flag Inappropriate Content</h3>',
			'toggleButton' => [
				'label' => Html::icon('flag'),
				'alt' => 'Flag innapropriate content',
				'class' => 'ind-icon'],
			'headerOptions' => ['class' => 'flag-modal-header'],
			'bodyOptions' => ['class' => 'flag-modal-body'],
			]); ?>
				<p>If you see inappropriate, false, misleading, or otherwise objectionable content in this profile, we want to know!  
				Click below to notify us immediately.  We will review as soon as possible and take appropriate action.<p>
				<p>Thank you for helping us protect this site and our users.</p> 	
				<div class="flag-modal-footer">			
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
			<?php Modal::end() ?>

		</div>
	</div>