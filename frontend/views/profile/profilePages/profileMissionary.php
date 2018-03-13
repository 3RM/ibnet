<?php

use common\models\profile\Profile;
use common\models\Utility;
use common\widgets\Alert;
use frontend\controllers\ProfileController;
use kartik\markdown\Markdown;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = $profile->formattedNames;
?>
<?= Alert::widget() ?>

<div class="profile">
	<div class="profile-main">

		<div class="img1"><?= empty($profile->image1) ? Html::img('@web/images/content/banner6.jpg', ['alt' => 'Header Image']) : Html::img($profile->image1, ['alt' => 'Header Image']) ?></div>
		<?= empty($profile->image2) ? Html::img('@web/images/content/profile-logo.png', ['class' => 'img2', 'alt' => 'Logo Image']) : Html::img($profile->image2, ['class' => 'img2', 'alt' => 'Logo image']) ?>
	
		<div class="header-text-wrap">
			<h1><?= $this->title ?></h1>
			<p class="tagline"><?= empty($profile->tagline) ? NULL : $profile->tagline ?></p>

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

		<?= empty($missionary) ? NULL : $this->render('cards/_card-missionary', ['profile' => $profile, 'missionary' => $missionary, 'church' => $church, 'churchLink' => $churchLink, 'mission' => $mission]) ?>
		<?= empty($churchPlant) ? NULL : $this->render('cards/_card-churchplant', ['churchPlant' => $churchPlant]) ?>
		<?= empty($updates) ? NULL : $this->render('cards/_card-missionary-updates', ['updates' => $updates]) ?>
		<?= empty($otherMinistryArray) ? NULL : $this->render('cards/_card-otherministries', ['otherMinistryArray' => $otherMinistryArray]) ?>
		<?= empty($schoolsAttended) ? NULL : $this->render('cards/_card-school', ['schoolsAttended' => $schoolsAttended]) ?>
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
			<?= $this->render('connection/_' . ProfileController::$profilePageArray[$profile->type] . 'Connections', ['profile' => $profile, 'church' => $church, 'missionLink' => $missionLink, 'churchPlant' => $churchPlant, 'pastor' => $pastor, 'otherMinistryArray' => $otherMinistryArray, 'sCPArray' => $sCPArray, 'sChurchArray' => $sChurchArray, 'sOtherArray' => $sOtherArray, 'memberArray' => $memberArray, 'likeArray' => $likeArray]); ?>
		</div>
	<?php } elseif ($p == 'history') { ?>
		<div class="additional-content">
			<?= $this->render('_history', ['profile' => $profile, 'events' => $events]); ?>
		</div>
	<?php }	?>

</div>