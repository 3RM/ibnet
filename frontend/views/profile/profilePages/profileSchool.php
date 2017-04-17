<?php

use kartik\markdown\Markdown;
use tugmaks\GoogleMaps\Map;
use yii\bootstrap\Alert;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = $profile->org_name;
?>

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
					
					<!-- Begin Image & Description -->
					<p><?= Markdown::convert($profile->description) ?></p>							
				 	<!-- End Image & Description -->
			</div>
		</div>
		<div class="row">
            <div class="col-md-4 profile-thirds">
            	<!-- Begin Contact Information (Box 1) -->
				<?php if ($profile->org_address1 && $profile->org_city && $profile->org_st_prov_reg && $profile->org_country) { ?>
					<?= Html::icon('map-marker') . ' ' . $profile->org_address1 . ', ' ?>
					<?= empty($profile->org_address2) ? NULL : $profile->org_address2 . ', ' ?>
					<?= $profile->org_city . ', ' . $profile->org_st_prov_reg ?>
					<?= empty($profile->org_zip) ? NULL : ' ' . $profile->org_zip ?>
					<?= $profile->org_country == 'United States' ? NULL : ', ' . $profile->org_country ?>
					<?= '<br>' ?>
				<?php } ?>
				<?php if (($profile->org_po_address1 || $profile->org_po_box) && $profile->org_po_city && $profile->org_po_st_prov_reg && $profile->org_po_country) { ?>
					<?= Html::icon('envelope') . ' ' ?>
					<?= empty($profile->org_po_address1) ? NULL : $profile->org_po_address1 . ', ' ?>
					<?= empty($profile->org_po_address2) ? NULL : $profile->org_po_address2 . ', ' ?>
					<?= empty($profile->org_po_box) ? NULL : ' PO Box ' . $profile->org_po_box . ', ' ?>
					<?= $profile->org_po_city . ', ' . $profile->org_po_st_prov_reg . ', ' ?>
					<?= empty($profile->org_po_zip) ? NULL : ' ' . $profile->org_po_zip ?>
					<?= $profile->org_po_country == 'United States' ? NULL : $profile->org_po_country ?>
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
				<!-- Begin School Levels (Box 3) -->
				<strong>School Levels Offered:</strong><br>
				<?php foreach ($schoolLevel as $level) {
					echo $level['school_level'] . '<br>';
				} ?>
				<!-- End School Levels -->
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
				<!-- Begin "Ministry of ... Baptist Church" (Box 2) -->
				<?= (empty($church) || empty($churchLink)) ? NULL :
				'<b>Ministry of:</b><br>' . HTML::a($churchLink, ['church', 'id' => $church->id, 'city' => $church->url_city, 'name' => $church->url_name]) . '<br>' ?>
				<!-- End "Ministry of ... Baptist Church" -->
				<br>
				<!-- Begin Accreditation/Association (Box 3) -->
				<strong>Accreditations:</strong><br>
				<?php if ($accreditations) {
					foreach ($accreditations as $accreditation) {
						echo HTML::a($accreditation->association, $accreditation->website, ['target' => '_blank']) . '<br>';
					}
				} ?>
				<!-- End Association -->
				<br>
				<!-- Last Update -->
				<p><strong>Last Update: </strong><?= Yii::$app->formatter->asDate($profile->last_update) ?></p>
			</div>
        </div>
        <?= $this->render('_profileFooter', ['id' => $profile->id]) ?>
	</div>
</div>