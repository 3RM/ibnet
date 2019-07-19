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
$this->title = $profile->mainName;
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
			<p class="type"><?= Profile::$icon[$profile->type] ?> Pastor <?= empty($church) ? NULL : ' at ' . HTML::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['church', 'id' => $church->id, 'urlLoc' => $church->url_loc, 'urlName' => $church->url_name]) ?></p>
		</div>

		<div class="description">
			<?= Markdown::convert($profile->description) ?>
		</div>

		<?= $otherMinistries ? $this->render('../profile/profilePages/cards/_card-otherministries', ['otherMinistries' => $otherMinistries]) : NULL ?>
        <?= $schoolsAttended ? $this->render('../profile/profilePages/cards/_card-school', ['schoolsAttended' => $schoolsAttended]) : NULL ?>
		<?= $fellowships ? $this->render('../profile/profilePages/cards/_card-fellowships', ['fellowships' => $fellowships]) : NULL ?>
		<?= $this->render('../profile/profilePages/cards/_card-contact-ind', ['profile' => $profile]) ?>
		<?= $social ? $this->render('../profile/profilePages/cards/_card-social', ['social' => $social]) : NULL ?>

		<?= $this->render('../profile/profilePages/_map', ['loc' => $loc]) ?>

	</div>
</div>