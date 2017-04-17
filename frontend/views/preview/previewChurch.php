<?php

use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use frontend\controllers\ProfileFormController;
use kartik\markdown\Markdown;
use tugmaks\GoogleMaps\Map;
use yii\base\Controller;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = $profile->org_name;
?>

<div class="my-profiles">
    <div class="row">
        <div class="container">
            <?= $activate ? 
        		'<h1>' . Html::icon('edit') . ' Preview & Activate</h1>' :
            	'<h1>' . Html::icon('edit') . ' Preview & Edit</h1>' ?>
            <div id="open" class="progress-menu"><?= Html::a(Html::icon('menu-hamburger') . ' Open Edit Menu', '#') ?></div>
            <br />
            <br />
            <?php $profile->status == Profile::STATUS_ACTIVE ? 
            	print('<p class="progress-menu">' . Html::a(Url::toRoute(['profile/' . strtolower($profile->type), 'city' => $profile->url_city, 'name' => $profile->url_name, 'id' => $profile->id], 'https') . ' ' . Html::icon('new-window'), ['profile/' . strtolower($profile->type),	'city' => $profile->url_city, 'name' => $profile->url_name, 'id' => $profile->id], ['target' => '_blank']) . '</p>') :
            	NULL; ?>
            <?php $form = ActiveForm::begin(); ?>
            <?= $activate ?
            	Html::submitButton('Activate', [
            		'method' => 'POST',
            		'class' => 'btn btn-preview pull-right',
            		'name' => 'activate',
        		]) :
            	Html::submitButton('Finished', [
            		'method' => 'POST',
            		'class' => 'btn btn-preview pull-right',
            		'name' => 'finished',
        		]) ?>
        	<?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div class="clearprofiles"></div>

<div id="pm_menu" class="pm_close">
	<h2>Edit Menu</h2>
    <ul>  
        <?php $i = 0;
        while ($i < count(ProfileFormController::$form)) {
            if ($typeMask[$i] == 1) {  
            	echo $formList[$i] == 'Skip' ? NULL :
            	'<li>' . Html::a(ProfileFormController::$formList[$i], ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => $i-1, 'id' => $profile->id, 'e' => 1]) . '</li>';
            }
            $i++;
        } ?>
    </ul>
</div>
<br />

<div class="site-index profile-page">

    <div class="profile-header">
    	<div class="container">
           	<div class="row">
           		<div class="col-md-1">
           			<div class="icon-lg"><?= Html::img('@web/images/' . $profile->type . '-lg.png') ?></div>
           		</div>
           		<div class="col-md-10">
               		<h1><?= $this->title ?></h1>
               		<span class="tagline"><?= empty($profile->tagline) ? NULL : $profile->tagline ?></span> <?= '<span  class="edit">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['nd']-1, 'id' => $profile->id, 'e' => 1]) . '</span>' ?>
               	</div>
            </div>
        </div>
    </div>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?= '<span  class="edit" style="padding:10px; position:absolute;">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['i1']-1, 'id' => $profile->id, 'e' => 1]) . '</span>' ?><?= empty($profile->image1) ? Html::img('@web/images/Profile_Image_3.jpg', ['alt' => 'My logo']) : Html::img($profile->image1) ?>
			</div>
		</div>
		<div class="row description">
			<div class="col-md-4">
				<?= '<span  class="edit" style="padding:15px; position:absolute;">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['i2']-1, 'id' => $profile->id, 'e' => 1]) . '</span>' ?><?= empty($profile->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['alt' => 'My logo']) : Html::img($profile->image2) ?>
			</div>
			<div class="col-xs-8">
				<!-- Begin Image & Description -->
				<h2>About <?= $this->title ?></h2>
				<h4><?= '<span  class="edit">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => 6-1, 'id' => $profile->id, 'e' => 1]) . '</span>' ?> 
					Pastor <?= empty($pastorLink) ? $profile->formattedNames :
						HTML::a($profile->formattedNames, ['/profile/pastor', 'id' => $pastorLink->id, 'city' => $pastorLink->url_city, 'name' => $pastorLink->url_name], ['target' => '_blank']); ?>
					<?= $profile->pastor_interim ? ' (Interim)' : NULL ?>
					<?= $profile->cp_pastor ? ' (Church Planter)' : NULL ?></h4>
				<p><?= Markdown::convert($profile->description) . '<span  class="edit">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['nd']-1, 'id' => $profile->id, 'e' => 1]) . '</span>' ?></p>
				 <!-- End Image & Description -->
			</div>
		</div>
		<div class="row">
            <div class="col-md-4 profile-thirds">
            	<!-- Begin Contact Information (Box 1) -->
            	<span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['lo']-1, 'id' => $profile->id, 'e' => 1]) ?></span>
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
				<?= Html::icon('phone') . ' ' . $profile->phone ?><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['co']-1, 'id' => $profile->id, 'e' => 1]) ?></span><br>
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
				<strong>Service Times:</strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['st']-1, 'id' => $profile->id, 'e' => 1]) ?></span><br>
				<?php $serviceTimes = $profile->serviceTime; ?>
				<?= empty($serviceTimes->day_1) ? NULL : $serviceTimes->day_1 . ' ' . $serviceTimes->time_1 . ' ' . $serviceTimes->description_1 . '<br>' ?>
				<?= empty($serviceTimes->day_2) ? NULL : $serviceTimes->day_2 . ' ' . $serviceTimes->time_2 . ' ' . $serviceTimes->description_2 . '<br>' ?>
				<?= empty($serviceTimes->day_3) ? NULL : $serviceTimes->day_3 . ' ' . $serviceTimes->time_3 . ' ' . $serviceTimes->description_3 . '<br>' ?>
				<?= empty($serviceTimes->day_4) ? NULL : $serviceTimes->day_4 . ' ' . $serviceTimes->time_4 . ' ' . $serviceTimes->description_4 . '<br>' ?>
				<?= empty($serviceTimes->day_5) ? NULL : $serviceTimes->day_5 . ' ' . $serviceTimes->time_5 . ' ' . $serviceTimes->description_5 . '<br>' ?>
				<?= empty($serviceTimes->day_6) ? NULL : $serviceTimes->day_6 . ' ' . $serviceTimes->time_6 . ' ' . $serviceTimes->description_6 . '<br>' ?>
				<!-- End Service Times -->
				<br>
				<!-- Begin Distinctives (Box 2) -->
				<strong>Bible: </strong><?= $profile->bible ?><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['sl']-1, 'id' => $profile->id, 'e' => 1]) ?></span><br>
				<strong>Worship Style: </strong><?= $profile->worship_style ?><br>
				<strong>Government: </strong><?= $profile->polity ?><br>
				<!-- Εnd Distinctives -->
				<br>
				<!-- Begin Social (Box 2) -->
				<strong>Social Media: </strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['co']-1, 'id' => $profile->id, 'e' => 1]) ?></span><br>
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
				<!-- Begin Ministries (Box 3) -->
				<strong>Ministries: </strong><br>
				<?php if ($ministries) {
					foreach ($ministries as $ministry) {
						echo HTML::a($ministry->org_name, ['profile/' . ProfileController::$profilePageArray[$ministry->type], 'id' => $ministry->id, 'city' => $ministry->url_city, 'name' => $ministry->url_name], ['target' => '_blank']) . '<br>';
					}
				} ?>
				<br>
				<!-- End Ministries -->
				<!-- Begin Programs (Box 3) -->
				<strong>Programs: </strong> <span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['pg']-1, 'id' => $profile->id, 'e' => 1]) ?></span><br>
				<?php if ($programs) {
					foreach ($programs as $program) {
						echo HTML::a($program->org_name, ['profile/special-ministry', 'id' => $program->id, 'city' => $program->url_city, 'name' => $program->url_name], ['target' => '_blank']) . '<br>';
					}
				} ?>
				<br>
				<!-- End Programs -->
				<!-- Begin Associations/Fellowships (Box 3) -->
				<strong>Association: </strong>
				<span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['as']-1, 'id' => $profile->id, 'e' => 1]) ?></span><br>
				<?php if ($profile->ass_id) {
					if (empty($assLink)) {
						echo $association->association;
						empty($association->association_acronym) ? NULL :
							print(' (' . $association->association_acronym . ')</p>');
					} else {
						echo HTML::a($association->association, ['profile/association', 'id' => $assLink->id, 'city' => $assLink->url_city, 'name' => $assLink->url_name], ['target' => '_blank']) . '<br>';
					}
				} ?>
				<br>
				<strong>Fellowship: </strong>
				<?php echo '<span  class="pull-right edit">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['as']-1, 'id' => $profile->id, 'e' => 1]) . '</span><br />';
				if ($profile->flwship_id) {
					if (empty($flwshipLink)) {
						echo $fellowship->fellowship;
						empty($fellowship->fellowship_acronym) ? NULL :
							print(' (' . $fellowship->fellowship_acronym . ')<br />');
					} else {
						echo HTML::a($fellowship->fellowship, ['profile/fellowship', 'id' => $flwshipLink->id,  'city' => $flwshipLink->url_city, 'name' => $flwshipLink->url_name], ['target' => '_blank']) . '<br>';
					}
				} ?>
				<!-- End Associations/Fellowships -->
				<br>
				<!-- Last Update -->
				<p><br><strong>Last Update: </strong><?= Yii::$app->formatter->asDate($profile->last_update) ?></p>
			</div>
        </div>
	</div>
	<p>&nbsp;</p>
</div>