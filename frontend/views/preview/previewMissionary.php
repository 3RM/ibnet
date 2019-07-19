<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use frontend\controllers\ProfileController;
use frontend\controllers\ProfileFormController;
use kartik\markdown\Markdown;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = $profile->coupleName;
?>

<?= $this->render('_previewHeader', ['profile' => $profile, 'activate' => $activate]) ?>

<div id="pm_menu" class="pm_close">
	<h2>Edit Menu</h2>
    <ul>  
        <?php $i = 0;
        while ($i < count(ProfileFormController::$form)) {
            if ($typeMask[$i] == 1) {  
            	echo $formList[$i] == 'Skip' ? NULL :
            	'<li>' . Html::a(ProfileFormController::$formList[$i], ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => $i-1, 'id' => $profile->id]) . '</li>';
            }
            $i++;
        } ?>
    </ul>
</div>
<br />

<div class="profile preview-profile">
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

		<?= $missionary ? $this->render('../profile/profilePages/cards/_card-missionary', ['profile' => $profile, 'missionary' => $missionary, 'church' => $church, 'missionAgcy' => $missionAgcy, 'missionAgcyProfile' => $missionAgcyProfile]) : NULL ?>
		<?= $churchPlant ? $this->render('../profile/profilePages/cards/_card-churchplant', ['churchPlant' => $churchPlant]) : NULL ?>
		<?= $updates ? $this->render('../profile/profilePages/cards/_card-missionary-updates', ['updates' => $updates]) : NULL ?>
        <?= $otherMinistries ? $this->render('../profile/profilePages/cards/_card-otherministries', ['otherMinistries' => $otherMinistries]) : NULL ?>
        <?= $schoolsAttended ? $this->render('../profile/profilePages/cards/_card-school', ['schoolsAttended' => $schoolsAttended]) : NULL ?>
		<?= $this->render('../profile/profilePages/cards/_card-contact-ind', ['profile' => $profile]) ?>
		<?= $social ? $this->render('../profile/profilePages/cards/_card-social', ['social' => $social]) : NULL ?>

		<?= $this->render('../profile/profilePages/_map', ['loc' => $loc]) ?>

	</div>
</div>