<?php
use yii\bootstrap\Html;
?>

		<div class="card">
			<div class="social-wrapper">
				<?= empty($social->sermonaudio) ? NULL : HTML::a(Html::img('@img.profile/sa.png'), $social->sermonaudio, ['target' => 'blank']) ?>
				<?= empty($social->facebook) ? NULL : HTML::a(Html::icon('glyphicon social social-facebook', ['class' => 'icon-padding']), $social->facebook, ['target' => 'blank']) ?>
				<?= empty($social->twitter) ? NULL : HTML::a(Html::icon('glyphicon social social-twitter', ['class' => 'icon-padding']), $social->twitter, ['target' => 'blank']) ?>
				<?= empty($social->linkedin) ? NULL : HTML::a(Html::icon('glphyicon social social-linked-in', ['class' => 'icon-padding']), $social->linkedin, ['target' => 'blank']) ?>
				<?= empty($social->google) ? NULL : HTML::a(Html::icon('glyphicon social social-google-plus', ['class' => 'icon-padding']), $social->google, ['target' => 'blank']) ?>
				<?= empty($social->rss) ? NULL : HTML::a(Html::icon('glyphicon social social-rss', ['class' => 'icon-padding']), $social->rss, ['target' => 'blank']) ?>
				<?= empty($social->youtube) ? NULL : HTML::a(Html::icon('glyphicon social social-youtube', ['class' => 'icon-padding']), $social->youtube, ['target' => 'blank']) ?>
				<?= empty($social->vimeo) ? NULL : HTML::a(Html::icon('glyphicon social social-vimeo', ['class' => 'icon-padding']), $social->vimeo, ['target' => 'blank']) ?>
				<?= empty($social->pinterest) ? NULL : HTML::a(Html::icon('glyphicon social social-pinterest', ['class' => 'icon-padding']), $social->pinterest, ['target' => 'blank']) ?>
				<?= empty($social->tumblr) ? NULL : HTML::a(Html::icon('glyphicon social social-tumblr', ['class' => 'icon-padding']), $social->tumblr, ['target' => 'blank']) ?>
				<?= empty($social->soundcloud) ? NULL : HTML::a(Html::icon('glyphicon social social-soundcloud', ['class' => 'icon-padding']), $social->soundcloud, ['target' => 'blank']) ?>
				<?= empty($social->instagram) ? NULL : HTML::a(Html::icon('glyphicon social social-instagram', ['class' => 'icon-padding'], ['class' => 'icon-padding']), $social->instagram, ['target' => 'blank']) ?>
				<?= empty($social->flickr) ? NULL : HTML::a(Html::icon('glyphicon social social-flickr', ['class' => 'icon-padding']), $social->flickr, ['target' => 'blank']) ?>	
			</div>
		</div>