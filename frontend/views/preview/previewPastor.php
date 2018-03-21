<?php

use common\models\profile\Profile;
use common\models\Utility;
use common\widgets\Alert;
use frontend\controllers\ProfileController;
use frontend\controllers\ProfileFormController;
use kartik\markdown\Markdown;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = $profile->formattedNames;
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

		<div class="img1"><?= empty($profile->image1) ? Html::img('@web/images/content/banner6.jpg', ['alt' => 'Header Image']) : Html::img($profile->image1, ['alt' => 'Header Image']) ?></div>
		<?= empty($profile->image2) ? Html::img('@web/images/content/profile-logo.png', ['class' => 'img2', 'alt' => 'Logo Image']) : Html::img($profile->image2, ['class' => 'img2', 'alt' => 'Logo image']) ?>
	
		<div class="header-text-wrap">
			<h1><?= $this->title ?></h1>
			<p class="tagline"><?= empty($profile->tagline) ? NULL : $profile->tagline ?></p>
			<p class="type"><?= Profile::$icon[$profile->type] ?> Pastor <?= empty($church) ? NULL : ' at ' . HTML::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['church', 'id' => $church->id, 'urlLoc' => $church->url_loc, 'name' => $church->url_name]) ?></p>
		</div>

		<div class="description">
			<?= Markdown::convert($profile->description) ?>
		</div>

		<?= empty($otherMinistryArray) ? NULL : $this->render('../profile/profilePages/cards/_card-otherministries', ['otherMinistryArray' => $otherMinistryArray]) ?>
        <?= empty($schoolsAttended) ? NULL : $this->render('../profile/profilePages/cards/_card-school', ['schoolsAttended' => $schoolsAttended]) ?>
		<?= empty($flwshipArray) ? NULL : $this->render('../profile/profilePages/cards/_card-fellowships', ['flwshipArray' => $flwshipArray]) ?>
		<?= $this->render('../profile/profilePages/cards/_card-distinctives', ['profile' => $profile]) ?>
		<?= $this->render('../profile/profilePages/cards/_card-contact-ind', ['profile' => $profile]) ?>
		<?= empty($social) ? NULL : $this->render('../profile/profilePages/cards/_card-social', ['social' => $social]) ?>

		<?= $this->render('../profile/profilePages/_map', ['loc' => $loc]) ?>

	</div>
</div>