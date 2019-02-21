<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use kartik\markdown\Markdown;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = $profile->coupleName;
?>
<?= Alert::widget() ?>

<div class="profile">
	<div class="profile-main">

		<div class="img1"><?= $profile->image1 ? Html::img($profile->image1, ['alt' => 'Header Image']) : Html::img('@img.profile/banner6.jpg', ['alt' => 'Header Image']) ?></div>
		<?= $profile->image2 ? Html::img($profile->image2, ['class' => 'img2', 'alt' => 'Logo image']) : Html::img('@img.profile/profile-logo.png', ['class' => 'img2', 'alt' => 'Logo Image']) ?>
	
		<div class="header-text-wrap">
			<h1><?= $this->title ?></h1>
			<p class="tagline"><?= $profile->tagline ? $profile->tagline : NULL ?></p>

			<p class="type">
				<?= Profile::$icon[$profile->type] ?>
				<?php if ($profile->sub_type == 'Furlough Replacement') { ?>
					<?= $profile->spouse_first_name == NULL ? 'Furlough Replacement Missionary' : 'Furlough Replacement Missionaries' ?>
				<?php } elseif ($profile->sub_type == 'Bible Translator') { ?>
					<?= $profile->spouse_first_name == NULL ? 'Bible Translator' : 'Bible Translators' ?>
				<?php } else { ?>
					<?= $profile->spouse_first_name == NULL ? 'Missionary to ' : 'Missionaries to ' ?><?= $missionary->field ?>
				<?php } ?>
			</p>
		</div>

		<div class="description">
			<?= Markdown::convert($profile->description) ?>
		</div>

		<?= $missionary ? $this->render('cards/_card-missionary', ['profile' => $profile, 'missionary' => $missionary, 'church' => $church, 'missionAgcy' => $missionAgcy, 'missionAgcyProfile' => $missionAgcyProfile]) : NULL ?>
		<?= $churchPlant ? $this->render('cards/_card-churchplant', ['churchPlant' => $churchPlant]) : NULL ?>
		<?= $updates ? $this->render('cards/_card-missionary-updates', ['updates' => $updates]) : NULL ?>
		<?= $otherMinistries ? $this->render('cards/_card-otherministries', ['otherMinistries' => $otherMinistries]) : NULL ?>
		<?= $schoolsAttended ? $this->render('cards/_card-school', ['schoolsAttended' => $schoolsAttended]) : NULL ?>
		<?= $this->render('cards/_card-distinctives', ['profile' => $profile]) ?>
		<?= $this->render('cards/_card-contact-ind', ['profile' => $profile]) ?>
		<?= $social ? $this->render('cards/_card-social', ['social' => $social]) : NULL ?>

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
			<?= $pastor ? $this->render('connection/_srPastor', ['type' => $profile->type, 'church' => $church, 'pastor' => $pastor]) : NULL ?>
			<?= ($church && $churchStaff) ? $this->render('connection/_orgStaffWithMinistry', ['ministry' => $church, 'staff' => $churchStaff]) : NULL ?>
			<?= ($missionAgcy && $missionAgcyStaff) ? $this->render('connection/_orgStaffWithMinistry', ['ministry' => $missionAgcy, 'staff' => $missionAgcyStaff]) : NULL ?>
			<?= ($churchPlant && $churchPlantStaff) ? $this->render('connection/_orgStaffWithMinistry', ['ministry' => $churchPlant, 'staff' => $churchPlantStaff]) : NULL ?>
			<?= $otherMinistriesStaff ? $this->render('connection/_otherMinistriesStaff', ['otherMinistriesStaff' => $otherMinistriesStaff]) : NULL ?>
			<?= ($church && $churchMembers->churchMembers) ? $this->render('connection/_churchFellowMembers', ['church' => $church, 'churchMembers' => $churchMembers->churchMembers]) : NULL ?>
			<?= ($churchPlant && $churchPlantMembers->churchMembers) ? $this->render('connection/_churchFellowMembers', ['church' => $churchPlant, 'churchMembers' => $churchPlantMembers->churchMembers]) : NULL ?>
			<?= $likeProfiles ? $this->render('connection/_likes', ['likeProfiles' => $likeProfiles]) : NULL ?>
			<?php if (!$pastor && !($church && $churchStaff) && !($missionAgcy && $missionAgcyStaff) && !($churchPlant && $churchPlantStaff) && !$otherMinistriesStaff && !($church && $churchMembers->churchMembers) && !($churchPlant && $churchPlantMembers->churchMembers) && !$likeProfiles) {
				echo '<em>No connections found.</em>';
			} ?>
		</div>
	<?php } elseif ($p == 'history') { ?>
		<div class="additional-content">
			<?= $this->render('_history', ['profile' => $profile, 'events' => $events]); ?>
		</div>
	<?php }	?>

</div>