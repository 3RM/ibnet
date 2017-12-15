<?php

use common\models\profile\Profile;
use common\models\Utility;
use common\widgets\Alert;
use frontend\controllers\ProfileController;
use kartik\markdown\Markdown;
use tugmaks\GoogleMaps\Map;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = $profile->formattedNames;
?>

<div class="profile">
	<div class="profile-main">

		<div class="img1"><?= empty($profile->image1) ? Html::img('@web/images/content/banner6.jpg', ['alt' => 'Header Image']) : Html::img($profile->image1, ['alt' => 'Header Image']) ?></div>
		<?= empty($profile->image2) ? Html::img('@web/images/content/profile-logo.png', ['class' => 'img2', 'alt' => 'Logo Image']) : Html::img($profile->image2, ['class' => 'img2', 'alt' => 'Logo image']) ?>
	
		<div class="header-text-wrap">
			<h1><?= $this->title ?></h1>
			<p class="tagline"><?= empty($profile->tagline) ? NULL : $profile->tagline ?></p>
			<p class="type"><?= Profile::$icon[$profile->type] . ' ' . $profile->type ?>
			</p>
		</div>

		<div class="description">
			<?= Markdown::convert($profile->description) ?>
		</div>

		<?= $profile->type == 'Evangelist' ? 
			$this->render('cards/_card-evangelist', ['church' => $church, 'parentMinistry' => $parentMinistry]) :
			$this->render('cards/_card-chaplain', ['church' => $church, 'mission' => $mission, 'missionLink' => $missionLink]) ?>
		<?= empty($otherMinistryArray) ? NULL : $this->render('cards/_card-otherministries', ['otherMinistryArray' => $otherMinistryArray]) ?>
		<?= empty($schoolsAttended) ? NULL : $this->render('cards/_card-school', ['schoolsAttended' => $schoolsAttended]) ?>
		<?= empty($flwshipArray) ? NULL : $this->render('cards/_card-fellowships', ['flwshipArray' => $flwshipArray]) ?>
		<?= $this->render('cards/_card-distinctives', ['profile' => $profile]) ?>
		<?= $this->render('cards/_card-contact-ind', ['profile' => $profile]) ?>
		<?= empty($social) ? NULL : $this->render('cards/_card-social', ['social' => $social]) ?>

		<?= $this->render('_map', ['loc' => $loc]) ?>

	</div>
	<?= $this->render('_profileFooter', ['profile' => $profile, 'iLike' => $iLike, 'likeCount' => $likeCount]) ?>
	<?= $this->render('_addContent') ?>

    <?php if ($p == 'comments') { ?>
		<div class="additional-content">
			<?= $this->render('comment/_comments', ['profile' => $profile]); ?>
		</div>
	<?php } elseif ($p == 'connections') { ?>
		<div class="additional-content">
			<?= $this->render('connection/_' . ProfileController::$profilePageArray[$profile->type] . 'Connections', ['profile' => $profile, 'parentMinistry' => $parentMinistry, 'church' => $church, 'missionLink' => $missionLink, 'sChurchArray' => $sChurchArray, 'pastor' => $pastor, 'otherMinistryArray' => $otherMinistryArray, 'sOtherArray' => $sOtherArray, 'memberArray' => $memberArray, 'likeArray' => $likeArray]); ?>
		</div>
	<?php } elseif ($p == 'history') { ?>
		<div class="additional-content">
			<?= $this->render('_history', ['profile' => $profile, 'events' => $events]); ?>
		</div>
	<?php } ?>

</div>