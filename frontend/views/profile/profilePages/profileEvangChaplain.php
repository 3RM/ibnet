<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use kartik\markdown\Markdown;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
$this->title = $profile->mainName;
?>

<div class="profile">
	<div class="profile-main">

		<div class="img1"><?= $profile->image1 ? Html::img($profile->image1, ['alt' => 'Header Image']) : Html::img('@img.profile/banner6.jpg', ['alt' => 'Header Image']) ?></div>
		<?= $profile->image2 ? Html::img($profile->image2, ['class' => 'img2', 'alt' => 'Logo image']) : Html::img('@img.profile/profile-logo.png', ['class' => 'img2', 'alt' => 'Logo Image']) ?>
	
		<div class="header-text-wrap">
			<h1><?= $this->title ?></h1>
			<p class="tagline"><?= $profile->tagline ? $profile->tagline : NULL ?></p>
			<p class="type"><?= Profile::$icon[$profile->type] . ' ' . $profile->type ?>
			</p>
		</div>

		<div class="description">
			<?= Markdown::convert($profile->description) ?>
		</div>

		<?= $profile->type == Profile::TYPE_EVANGELIST ? 
			$this->render('cards/_card-evangelist', ['church' => $church, 'parentMinistry' => $parentMinistry]) :
			$this->render('cards/_card-chaplain', ['church' => $church, 'missionAgcyProfile' => $missionAgcyProfile]) ?>
		<?= !empty($otherMinistries) ? $this->render('cards/_card-otherministries', ['otherMinistries' => $otherMinistries]) : NULL ?>
		<?= !empty($schoolsAttended) ? $this->render('cards/_card-school', ['schoolsAttended' => $schoolsAttended]) : NULL ?>
		<?= !empty($fellowships) ? $this->render('cards/_card-fellowships', ['fellowships' => $fellowships]) : NULL ?>
		<?= $this->render('cards/_card-contact-ind', ['profile' => $profile]) ?>
		<?= !empty($social) ? $this->render('cards/_card-social', ['social' => $social]) : NULL ?>

		<?= $this->render('_map', ['loc' => $loc]) ?>

	</div>
	<?= $this->render('_profileFooter', ['profile' => $profile, 'iLike' => $iLike, 'likeCount' => $likeCount]) ?>

	<?php if (!Yii::$app->user->isGuest) { ?>
		<?= $this->render('_addContent') ?>

    	<?php if ($p == 'comments') { ?>
			<div class="additional-content">
				<?= $this->render('comment/_comments', ['profile' => $profile]); ?>
			</div>
			
		<?php } elseif ($p == 'connections') { ?>
			<div class="additional-content">
				<?= !empty($pastor) ? $this->render('connection/_srPastor', ['type' => $profile->type, 'church' => $church, 'pastor' => $pastor]) : NULL ?>
				<?= (!empty($church) && !empty($churchStaff)) ? $this->render('connection/_orgStaffWithMinistry', ['staff' => $churchStaff, 'ministry' => $church]) : NULL ?>
				<?= !empty($parentMinistryStaff) ? $this->render('connection/_otherMinistriesStaff', ['parentMinistryStaff' => $parentMinistryStaff]) : NULL ?>
				<?= !empty($otherMinistriesStaff) ? $this->render('connection/_otherMinistriesStaff', ['otherMinistriesStaff' => $otherMinistriesStaff]) : NULL ?>
				<?= (!empty($church) && !empty($churchMembers) && !empty($churchMembers->churchMembers)) ? $this->render('connection/_churchFellowMembers', ['church' => $church, 'churchMembers' => $churchMembers->churchMembers]) : NULL ?>
				<?= !empty($likeProfiles) ? $this->render('connection/_likes', ['likeProfiles' => $likeProfiles]) : NULL ?>
				<?php if (empty($pastor) && (empty($church) || empty($churchStaff)) && empty($parentMinistryStaff) && empty($otherMinistriesStaff) && (empty($church) || empty($churchMembers) || empty($churchMembers->churchMembers)) && empty($likeProfiles)) {
					echo '<em>No connections found.</em>';
				} ?>
			</div>

		<?php } elseif ($p == 'history') { ?>
			<div class="additional-content">
				<?= $this->render('_history', ['profile' => $profile, 'events' => $events]); ?>
			</div>
		<?php } ?>

	<?php } ?>

</div>