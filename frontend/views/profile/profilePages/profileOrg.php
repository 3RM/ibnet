<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use frontend\controllers\ProfileController;
use kartik\markdown\Markdown;
use tugmaks\GoogleMaps\Map;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
rmrevin\yii\fontawesome\AssetBundle::register($this);
$this->title = $profile->org_name;
$connection = [
	'Camp' => '_camp',
	'Mission Agency' => '_missAgcy',
	'Music Ministry' => '_music',
	'Special Ministry' => '_special',
	'Print Ministry' => '_print',
];
?>

<div class="site-index profile-page">

    <div class="profile-header">
        <div class="container">
        	<div class="row">
        		<div class="col-lg-1 icon-lg top-margin">
	    			<?= Profile::$icon[$profile->type] ?>
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
					<?= Html::icon('map-marker') . ' ' ?>
					<?= empty($profile->org_address1) ? NULL : $profile->org_address1 . ', ' ?>
					<?= empty($profile->ind_address2) ? NULL : $profile->org_address2 . ', ' ?>
					<?= empty($profile->org_box) ? NULL : ' PO Box ' . $profile->org_box . ', ' ?>
					<?= $profile->org_city . ', ' ?>
					<?= empty($profile->org_zip) ? $profile->org_st_prov_reg . ', ' : $profile->org_st_prov_reg . ' ' ?>
					<?= $profile->org_zip ?>
					<?= $profile->org_country == 'United States' ? NULL : $profile->org_country ?>
					<?= '<br>' ?>
				<?php } ?>
				<?php if (($profile->org_po_address1 || $profile->org_po_box) && $profile->org_po_city && $profile->org_po_st_prov_reg && $profile->org_po_country) { ?>
					<?= Html::icon('envelope') . ' ' ?>
					<?= empty($profile->org_po_address1) ? NULL : $profile->org_po_address1 . ', ' ?>
					<?= empty($profile->org_po_address2) ? NULL : $profile->org_po_address2 . ', ' ?>
					<?= empty($profile->org_po_box) ? NULL : ' PO Box ' . $profile->org_po_box . ', ' ?>
					<?= $profile->org_po_city . ', ' ?>
					<?= empty($profile->org_po_zip) ? $profile->org_po_st_prov_reg . ', ' : $profile->org_po_st_prov_reg . ' ' ?>
					<?= $profile->org_po_zip ?>
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
				<!-- Begin Social (Box 2) -->
				<strong>Social Media: </strong><br>
				<?php if (isset($social)) { ?>
					<?= empty($social->sermonaudio) ? NULL : HTML::a('SermonAudio', $social->sermonaudio, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->facebook) ? NULL : HTML::a('Facebook', $social->facebook, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->twitter) ? NULL : HTML::a('Twitter', $social->twitter, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->linkedin) ? NULL : HTML::a('LinkedIn', $social->linkedin, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->google) ? NULL : HTML::a('Google+', $social->google, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->rss) ? NULL : HTML::a('RSS', $social->rss, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->youtube) ? NULL : HTML::a('YouTube', $social->youtube, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->vimeo) ? NULL : HTML::a('Vimeo', $social->vimeo, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->pinterest) ? NULL : HTML::a('Pinterest', $social->pinterest, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->tumblr) ? NULL : HTML::a('Tumblr', $social->tumblr, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->soundcloud) ? NULL : HTML::a('SoundCloud', $social->soundcloud, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->instagram) ? NULL : HTML::a('Instagram', $social->instagram, ['target' => '_blank']) . '<br>' ?>
					<?= empty($social->flickr) ? NULL : HTML::a('Flickr', $social->flickr, ['target' => '_blank']) . '<br>' ?>	
				<?php } ?>
				<!-- Εnd Social -->
			</div>
			<div class="col-md-4 profile-thirds">
				<!-- Begin linked Church (Box 3) -->
				<?= (empty($parentMinistry) || empty($parentMinistryLink)) ? NULL : $profile->org_name . ' is a ministry of ' . HTML::a($parentMinistryLink, ['church', 'id' => $parentMinistry->id, 'city' => $parentMinistry->url_city, 'name' => $parentMinistry->url_name]) . '<br><br>' ?>
				<!-- End linked Church -->
				<!-- Begin profile tags (Box 3) -->
				<?php if (!empty($tags = $profile->tag)) {
					echo '<strong>Tags:</strong><br>';
					foreach ($tags as $tag) {
						echo $tag->tag . '<br>';
					}
					echo '<br>';
				} ?>
			<!-- End profile tags -->

				<!-- Last Update -->
				<p><br /><strong>Last Update: </strong><?= Yii::$app->formatter->asDate($profile->last_update) ?></p>
			</div>
        </div>
        <div id="p">
        	<?= $this->render('_profileFooter', ['id' => $profile->id]) ?>
    	</div>
        
        <div class="add-content center">
        	<?= Html::a('Show Comments', Url::current(['p' => 'comments', '#' => 'p']), ['class' => 'btn btn-primary']); ?>
        	<?= Html::a('Show Connections', Url::current(['p' => 'connections', '#' => 'p']), ['class' => 'btn btn-primary']); ?>
        	<?= Html::a('Show History', Url::current(['p' => 'history', '#' => 'p']), ['class' => 'btn btn-primary']); ?>
    	</div>
        
    </div>
        
    <?php
	if ($p == 'comments') {
		echo $this->render('comment/_comments', ['profile' => $profile]);
	} elseif ($p == 'connections') {
		echo $this->render('connection/_' . ProfileController::$profilePageArray[$profile->type] . 'Connections', ['profile' => $profile, 'parentMinistry' => $parentMinistry, 'staffArray' => $staffArray, 'missionaryArray' => $missionaryArray, 'programChurchArray' => $programChurchArray, 'parentMinistry' => $parentMinistry]);
	} elseif ($p == 'history') {
		echo $this->render('_history', ['profile' => $profile, 'events' => $events]);
	}
	?>

	<div class="top-margin-60"></div>
</div>