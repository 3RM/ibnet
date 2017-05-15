<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use frontend\controllers\ProfileController;
use kartik\markdown\Markdown;
use tugmaks\GoogleMaps\Map;
use yii\base\Controller;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->title = $profile->org_name;
?>
<?= Alert::widget() ?>

<div class="site-index profile-page">
	
	<div class="profile-header">
	    <div class="container">
	    	<div class="row">
	    		<div class="col-lg-1">
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
					<h4>Pastor <?= empty($pastorLink) ? $profile->formattedNames :
						HTML::a($profile->formattedNames, ['pastor', 'id' => $pastorLink->id, 'city' => $pastorLink->url_city, 'name' => $pastorLink->url_name]); ?>
						<?= $profile->pastor_interim ? ' (Interim)' : NULL ?>
						<?= $profile->cp_pastor ? ' (Church Planter)' : NULL ?></h4>	
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
				<!-- Begin Service Times (Box 2) -->
				<strong>Service Times:</strong><br>
				<?php $serviceTimes = $profile->serviceTime; ?>
				<?= empty($serviceTimes->day_1) ? NULL : $serviceTimes->day_1 . ' ' . $serviceTimes->time_1 . ' ' . $serviceTimes->description_1 . '<br>' ?>
				<?= empty($serviceTimes->day_2) ? NULL : $serviceTimes->day_2 . ' ' . $serviceTimes->time_2 . ' ' . $serviceTimes->description_2 . '<br>' ?>
				<?= empty($serviceTimes->day_3) ? NULL : $serviceTimes->day_3 . ' ' . $serviceTimes->time_3 . ' ' . $serviceTimes->description_3 . '<br>' ?>
				<?= empty($serviceTimes->day_4) ? NULL : $serviceTimes->day_4 . ' ' . $serviceTimes->time_4 . ' ' . $serviceTimes->description_4 . '<br>' ?>
				<?= empty($serviceTimes->day_5) ? NULL : $serviceTimes->day_5 . ' ' . $serviceTimes->time_5 . ' ' . $serviceTimes->description_5 . '<br>' ?>
				<?= empty($serviceTimes->day_6) ? NULL : $serviceTimes->day_6 . ' ' . $serviceTimes->time_6 . ' ' . $serviceTimes->description_6 . '<br>' ?>
				<!-- End Service Times -->
				<br>
				<!-- Begin Distinctives (Box 1) -->
				<strong>Bible: </strong><?= $profile->bible ?><br>
				<strong>Worship: </strong><?= $profile->worship_style ?><br>
				<strong>Government: </strong><?= $profile->polity ?><br>
				<!-- Εnd Distinctives -->
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
				<!-- Begin Ministries (Box 3) -->
				<?php if ($ministries) {
					echo '<strong>Ministries: </strong><br>';
					foreach ($ministries as $ministry) {
						echo HTML::a($ministry->org_name, ['profile/' . ProfileController::$profilePageArray[$ministry->type], 'id' => $ministry->id, 'city' => $ministry->url_city, 'name' => $ministry->url_name]) . '<br>';
					}
					echo '<br>';
				} ?>
				<!-- End Ministries -->
				<!-- Begin Programs (Box 3) -->
				<?php if ($programs) {
					echo '<strong>Programs: </strong><br>';
					foreach ($programs as $program) {
						echo HTML::a($program->org_name, ['special-ministry', 'id' => $program->id, 'city' => $program->url_city, 'name' => $program->url_name]) . '<br>';
					}
					echo '<br>';
				} ?>
				<!-- End Programs -->
				 <!-- Begin Associations/Fellowships (Box 3) -->
				<?php if ($fellowships) {
					echo '<strong>Fellowship:</strong><br>';
					foreach ($fellowships as $fellowship) {
						if ($flwshipLink = ProfileController::findFellowship($fellowship->profile_id)) {
							echo HTML::a($fellowship->fellowship, ['profile/fellowship', 'id' => $flwshipLink->id, 'city' => $flwshipLink->url_city, 'name' => $flwshipLink->url_name], ['title' => $fellowship->fellowship_acronym, 'target' => '_blank']) . '<br>';
						} else {
							echo Html::tag('span', $fellowship->fellowship, ['title' => $fellowship->fellowship_acronym]) . '<br>';
						}
					}
					echo '<br>';
				} ?>
				<?php if ($associations) {
					echo '<strong>Association:</strong><br>';
					foreach ($associations as $association) {
						if ($assLink = ProfileController::findAssociation($association->profile_id)) {
							echo HTML::a($association->association, ['profile/association', 'id' => $assLink->id, 'city' => $assLink->url_city, 'name' => $assLink->url_name], ['title' => $association->association_acronym, 'target' => '_blank']) . '<br>';
						} else {
							echo Html::tag('span', $association->association, ['title' => $association->association_acronym]) . '<br>';
						}
					}
					echo '<br>';
				} ?>
				<!-- End Associations/Fellowships -->

				<!-- Last Update -->
				<p><br><strong>Last Update: </strong><?= Yii::$app->formatter->asDate($profile->last_update) ?></p>
			</div>
        </div>

        <?= $this->render('_profileFooter', ['id' => $profile->id]) ?>
     
	</div>
</div>