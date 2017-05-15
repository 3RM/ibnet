<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use kartik\markdown\Markdown;
use tugmaks\GoogleMaps\Map;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = isset($profile->spouse_first_name) ?
	($profile->ind_first_name . ' & ' . $profile->spouse_first_name . ' ' . $profile->ind_last_name) :
	($profile->ind_first_name . ' ' . $profile->ind_last_name);
?>
<?= Alert::widget() ?>

<div class="site-index profile-page">
    
    <div class="profile-header">
        <div class="container">
        	<div class="row">
        		<div class="col-md-1">
        			<div class="icon-lg"><?= Html::img('@web/images/' . $profile->type . '-lg.png') ?></div>
        		</div>
        		<div class="col-md-10">
            		<h1><?= $this->title ?></h1>
            		<span class="tagline"><?= empty($profile->tagline) ? NULL : $profile->tagline ?></span>
            	</div>
            </div>
        </div>
    </div>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?= empty($profile->image1) ? Html::img('@web/images/Profile_Image_3.jpg', ['alt' => 'My logo']) : Html::img($profile->image1) ?>
			</div>
		</div>
		<div class="row description">
			<div class="col-md-4">
				<?= empty($profile->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['alt' => 'My logo']) : Html::img($profile->image2) ?>
			</div>
			<div class="col-md-8">
					<h2>About <?= $this->title ?></h2>
					<h4><?= $profile->type ?></h4>					
					<!-- Begin Image & Description -->
					<p><?= Markdown::convert($profile->description) ?></p>							
				 	<!-- End Image & Description -->
			</div>
		</div>
		<div class="row">
            <div class="col-md-4 profile-thirds">
            	<!-- Begin Contact Information (Box 1) -->
				<?php if ($profile->ind_address1 && $profile->ind_city && $profile->ind_st_prov_reg && $profile->ind_country) { ?>
					<?= Html::icon('map-marker') . ' ' . $profile->ind_address1 . ', ' ?>
					<?= empty($profile->ind_address2) ? NULL : $profile->ind_address2 . ', ' ?>
					<?= $profile->ind_city . ', ' . $profile->ind_st_prov_reg ?>
					<?= empty($profile->ind_zip) ? NULL : ' ' . $profile->ind_zip ?>
					<?= $profile->ind_country == 'United States' ? NULL : ', ' . $profile->ind_country ?>
					<?= '<br>' ?>
				<?php } ?>
				<?php if (($profile->ind_po_address1 || $profile->ind_po_box) && $profile->ind_po_city && $profile->ind_po_st_prov_reg && $profile->ind_po_country) { ?>
					<?= Html::icon('envelope') . ' ' ?>
					<?= empty($profile->ind_po_address1) ? NULL : $profile->ind_po_address1 . ', ' ?>
					<?= empty($profile->ind_po_address2) ? NULL : $profile->ind_po_address2 . ', ' ?>
					<?= empty($profile->ind_po_box) ? NULL : ' PO Box ' . $profile->ind_po_box . ', ' ?>
					<?= $profile->ind_po_city . ', ' . $profile->ind_po_st_prov_reg . ', ' ?>
					<?= empty($profile->ind_po_zip) ? NULL : ' ' . $profile->ind_po_zip ?>
					<?= $profile->ind_po_country == 'United States' ? NULL : $profile->ind_po_country ?>
					<?= '<br>' ?>
				<?php } ?>
				<?= Html::icon('phone') . ' ' . $profile->phone ?><br>
				<?= empty($profile->website) ? NULL : Html::icon('globe') . ' ' . HTML::a($profile->website, $profile->website, ['target' => 'blank']) . '<br>' ?>
				<?php if ($profile->email_pvt && $profile->email_pvt_status != PROFILE::PRIVATE_EMAIL_ACTIVE) {
					echo Html::icon('send') . ' <em>Pending</em><br><br>';
				} elseif ($profile->email) {
				 	echo Html::icon('send') . ' ' . Html::mailto($profile->email, $profile->email) . '<br><br>';
				} ?>
				<!-- End Contact Information -->
				<!-- Begin Google Map (Box 1) -->
				<?php if(!empty($loc)) {
					echo Map::widget([
				  		'zoom' => 16,
				  		'center' => $loc,
				  		'width' => 90,
				  		'height' => 250,
				  		'widthUnits' => 'UNITS_PERCENT',
				  		'markers' => [['position' => $loc],],
				  	]);
				} ?>
				<!-- End Google Map -->
			</div>
			<div class="col-md-4 profile-thirds">
				<?php if ($profile->type == 'Evangelist') { ?>
					<!-- Begin Distinctives (Box 2) -->
					<strong>Bible: </strong><?= $profile->bible ?><br>
					<strong>Worship: </strong><?= $profile->worship_style ?><br>
					<strong>Government: </strong><?= $profile->polity ?><br>
					<!-- Εnd Distinctives -->
					<br>
				<?php } ?>
				<!-- Begin Schools Attended (Box 2) -->
				<p><strong>Schools Attended: </strong><br>
				<?php if (isset($schoolsAttended)) {
					foreach ($schoolsAttended as $school) {
						if ($s = $school->linkedProfile) {
							echo HTML::a($s->org_name, ['school', 'id' => $s->id, 'city' => $s->url_city, 'name' => $s->url_name]) . '<br>';
						} else {
							echo $school->school . '<br>';
						}
					}
				} ?></p>
				<!-- End Schools Attended -->
				<br>
				<!-- Begin Social (Box 2) -->
				<strong>Social Media: </strong><br>
				<?php if (isset($social)) { ?>
					<?= empty($social->sermonaudio) ? NULL : HTML::a('SermonAudio', $social->sermonaudio, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->facebook) ? NULL : HTML::a('Facebook', $social->facebook, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->twitter) ? NULL : HTML::a('Twitter', $social->twitter, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->linkedin) ? NULL : HTML::a('LinkedIn', $social->linkedin, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->google) ? NULL : HTML::a('Google+', $social->google, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->rss) ? NULL : HTML::a('RSS', $social->rss, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->youtube) ? NULL : HTML::a('YouTube', $social->youtube, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->vimeo) ? NULL : HTML::a('Vimeo', $social->vimeo, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->pinterest) ? NULL : HTML::a('Pinterest', $social->pinterest, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->tumblr) ? NULL : HTML::a('Tumblr', $social->tumblr, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->soundcloud) ? NULL : HTML::a('SoundCloud', $social->soundcloud, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->instagram) ? NULL : HTML::a('Instagram', $social->instagram, ['target' => 'blank']) . '<br>' ?>
					<?= empty($social->flickr) ? NULL : HTML::a('Flickr', $social->flickr, ['target' => 'blank']) . '<br>' ?>	
				<?php } ?>
				<!-- Εnd Social -->
			</div>
			<div class="col-md-4 profile-thirds">
				<!-- Begin Home Church (Box 3) -->
				<?= $churchLink ? '<strong>Home Church: </strong><br>' . HTML::a($churchLink, ['church', 'id' => $church->id, 'city' => $church->url_city, 'name' => $church->url_name]) . '<br><br>' : '<br>' ?>
				<!-- End Home Church -->
				<!-- Begin Parent Ministry (Box 3) -->
				<?= $ministryLink ? '<strong>Serving with: </strong><br>' . HTML::a($ministryLink, ['church', 'id' => $ministry->id, 'city' => $ministry->url_city, 'name' => $ministry->url_name]) . '<br><br>' : '<br>' ?>
				<!-- End Parent Ministry -->
				<!-- Begin Fellowship (Box 3) -->
				<strong>Fellowship:</strong><br>
				<?php if ($fellowships) {
					foreach ($fellowships as $fellowship) {
						if ($flwshipLink = ProfileController::findFellowship($fellowship->profile_id)) {
							echo HTML::a($fellowship->fellowship, ['profile/fellowship', 'id' => $flwshipLink->id, 'city' => $flwshipLink->url_city, 'name' => $flwshipLink->url_name], ['title' => $fellowship->fellowship_acronym, 'target' => '_blank']) . '<br>';
						} else {
							echo Html::tag('span', $fellowship->fellowship, ['title' => $fellowship->fellowship_acronym]) . '<br>';
						}
					}
				} ?>
				<!-- End Fellowship -->
				<!-- Last Update -->
				<p><strong>Last Update: </strong><?= Yii::$app->formatter->asDate($profile->last_update) ?></p>
			</div>
        </div>
        <?= $this->render('_profileFooter', ['id' => $profile->id]) ?>
	</div>
</div>