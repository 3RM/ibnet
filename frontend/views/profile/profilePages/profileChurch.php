<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use kartik\markdown\Markdown;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = $profile->org_name;
?>
<?= Alert::widget() ?>

<div class="profile">
	<div class="profile-main">

		<div class="img1"><?= $profile->image1 ? Html::img($profile->image1, ['alt' => 'Header Image']) : Html::img('@img.profile/banner6.jpg', ['alt' => 'Header Image']) ?></div>
		<?= $profile->image2 ? Html::img($profile->image2, ['class' => 'img2', 'alt' => 'Logo image']) : Html::img('@img.profile/profile-logo.png', ['class' => 'img2', 'alt' => 'Logo Image']) ?>
	
		<div class="header-text-wrap">
			<h1><?= $this->title ?></h1>
			<p class="tagline"><?= $profile->tagline ? $profile->tagline : NULL ?></p>
			<p class="type"><?= Profile::$icon[$profile->type] ?> Independent Baptist Church</p>
		</div>

		<div class="description">
			<?= Markdown::convert($profile->description) ?>
		</div>

		<?= $this->render('cards/_card-church-pastor', ['profile' => $profile, 'pastor' => $pastor]) ?>
		<?= $this->render('cards/_card-servicetimes', ['profile' => $profile]) ?>
		<?= !empty($ministries) ? $this->render('cards/_card-ministries', ['ministries' => $ministries]) : NULL ?>
		<?= !empty($programs) ? $this->render('cards/_card-programs', ['programs' => $programs]) : NULL ?>
		<?= !empty($associations) ? $this->render('cards/_card-associations', ['associations' => $associations]) : NULL ?>
		<?= !empty($fellowships) ? $this->render('cards/_card-fellowships', ['fellowships' => $fellowships]) : NULL ?>
		<?= $this->render('cards/_card-distinctives', ['profile' => $profile]) ?>
		<?= $this->render('cards/_card-contact-org', ['profile' => $profile]) ?>
		<?= !empty($social) ? $this->render('cards/_card-social', ['social' => $social]) : NULL ?>

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
			<?= !empty($staff) ? $this->render('connection/_orgStaff', ['staff' => $staff]) : NULL ?>
			<?= !empty($sentMissionaries) ? $this->render('connection/_sentMissionaries', ['sentMissionaries' => $sentMissionaries]) : NULL ?>
			<?= !empty($churchMembers) ? $this->render('connection/_churchMembers', ['church' => $profile, 'churchMembers' => $churchMembers]) : NULL ?>
			<?= !empty($likeProfiles) ? $this->render('connection/_likes', ['likeProfiles' => $likeProfiles]) : NULL ?>
			<?php if (empty($staff) && empty($sentMissionaries) && empty($churchMembers) && empty($likeProfiles)) {
				echo '<em>No connections found.</em>';
			} ?>
		</div>
	<?php } elseif ($p == 'history') { ?>
		<div class="additional-content">
			<?= $this->render('_history', ['profile' => $profile, 'events' => $events]); ?>
		</div>
	<?php } ?>

</div>