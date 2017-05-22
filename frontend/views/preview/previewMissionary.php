<?php

use common\models\profile\Profile;
use frontend\controllers\ProfileFormController;
use kartik\markdown\Markdown;
use tugmaks\GoogleMaps\Map;
use yii\bootstrap\Alert;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = isset($profile->spouse_first_name) ?
	($profile->ind_first_name . ' & ' . $profile->spouse_first_name . ' ' . $profile->ind_last_name) :
	($profile->ind_first_name . ' ' . $profile->ind_last_name);
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
            	'<li>' . Html::a(ProfileFormController::$formList[$i], ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => $i-1, 'id' => $profile->id]) . '</li>';
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
           		<div class="col-lg-1 icon-lg top-margin">
	    			<?= Profile::$icon[$profile->type] ?>
	    		</div>
           		<div class="col-md-10">
               		<h1><?= $this->title ?></h1>
               		<span class="tagline"><?= empty($profile->tagline) ? NULL : $profile->tagline ?></span> <?= '<span  class="edit">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['nd']-1, 'id' => $profile->id]) . '</span>' ?>
               	</div>
            </div>
        </div>
    </div>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?= '<span  class="edit" style="padding:10px; position:absolute;">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['i1']-1, 'id' => $profile->id]) . '</span>' ?><?= empty($profile->image1) ? Html::img('@web/images/Profile_Image_3.jpg', ['alt' => 'My logo']) : Html::img($profile->image1) ?>
			</div>
		</div>
		<div class="row description">
			<div class="col-md-4">
				<?= '<span  class="edit" style="padding:15px; position:absolute;">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['i2']-1, 'id' => $profile->id]) . '</span>' ?><?= empty($profile->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['alt' => 'My logo']) : Html::img($profile->image2) ?>
			</div>
			<div class="col-md-8">
					<h2>About <?= $this->title ?></h2>
					<h4><?= '<span  class="edit">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['fi']-1, 'id' => $profile->id]) . '</span>' ?> 
						<?php if ($profile->sub_type == 'Furlough Replacement') { ?>
							<?= $profile->ind_first_name == NULL ? 'Furlough Replacement Missionary</h4>' : 'Furlough Replacement Missionaries' ?>
						<?php } elseif ($profile->sub_type == 'Bible Translator') { ?>
							<?= $profile->ind_first_name == NULL ? 'Bible Translator</h4>' : 'Bible Translators' ?>
						<?php } else { ?>
							<?= $profile->ind_first_name == NULL ? 'Missionary to ' : 'Missionaries to ' ?><?= $missionary->field ?></h4>
						<?php } ?>
					</h4>
					<!-- Begin Image & Description -->
					<p><?= Markdown::convert($profile->description) . '<span  class="edit">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['nd']-1, 'id' => $profile->id]) . '</span>' ?></p>							
				 	<!-- End Image & Description -->
			</div>
		</div>
		<div class="row">
            <div class="col-md-4 profile-thirds">
            	<!-- Begin Contact Information (Box 1) -->
            	<span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['lo']-1, 'id' => $profile->id]) ?></span>
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
				<?= Html::icon('phone') . ' ' . $profile->phone ?><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['co']-1, 'id' => $profile->id]) ?></span><br>
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
				<!-- Begin Distinctives (Box 2) -->
					<strong>Bible: </strong><?= $profile->bible ?><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['di']-1, 'id' => $profile->id]) ?></span><br>
					<strong>Worship: </strong><?= $profile->worship_style ?><br>
					<strong>Government: </strong><?= $profile->polity ?><br>
				<!-- Εnd Distinctives -->
				<br />
				<!-- Begin Schools Attended (Box 2) -->
				<strong>Schools Attended: </strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['sa']-1, 'id' => $profile->id]) ?></span><br />
					<?php if (isset($schoolsAttended)) {
					foreach ($schoolsAttended as $school) {
						if ($s = $school->linkedProfile) {
							print(HTML::a($s->org_name, ['profile/school', 'id' => $s->id, 'city' => $s->url_city, 'name' => $s->url_name], ['target' => '_blank']) . '<br />');
						} else {
							print($school->school . '<br />');
						}
					}
				} ?>
				<br />
				<!-- Begin Social (Box 2) -->
				<strong>Social Media: </strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['co']-1, 'id' => $profile->id]) ?></span><br />
				<?php if (isset($social)) { ?>
					<p><?= empty($social->sermonaudio) ? NULL : HTML::a('SermonAudio', $social->sermonaudio, ['target' => '_blank']) . '<br>' ?>
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
					<?= empty($social->flickr) ? NULL : HTML::a('Flickr', $social->flickr, ['target' => '_blank']) . '<br>' ?></p>		
				<?php } ?>
				<!-- Εnd Social -->

			</div>
			<div class="col-md-4 profile-thirds">
				<!-- Begin Missionary Field Information (Box 3) -->
				<strong>Field: </strong>
				<?php if ($profile->sub_type == 'Furlough Replacement') { ?>
					<?= $missionary->field == 'Furlough Replacement' ? 'Various' : $missionary->field ?><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['/profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['fi']-1, 'id' => $profile->id]) ?></span>
				<?php } elseif ($profile->sub_type == 'Bible Translator') { ?>
					<?= $missionary->field ?><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['/profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['fi']-1, 'id' => $profile->id]) ?></span>
				<?php } else { ?>
					<?= $missionary->field ?><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['/profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['fi']-1, 'id' => $profile->id]) ?></span>
				<?php } ?><br>
				<strong>Status: </strong><?= $missionary->status ?><br>
				<!-- End Missionary Field Information -->
				<br>
				<!-- Sending Church (Box 3) -->
				<strong>Sending Church: </strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['/profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['hc']-1, 'id' => $profile->id]) ?></span><br>
				<?php echo $churchLink ?
					HTML::a($churchLink, ['profile/church', 'id' => $church->id, 'city' => $church->url_city, 'name' => $church->url_name], ['target' => '_blank']) . '<br>' :
					'<span style="color:red">' . HTML::icon('flag') . ' Update your sending church </span><br>' ?>
				<br>
				<!-- Begin Linked Mission Agency (Box 3) -->
				<strong>Mission Agency: </strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['/profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['ma']-1, 'id' => $profile->id]) ?></span><br>
				<?= empty($missionLink) ?
			    	$mission->mission : 
			    	HTML::a($missionLink->org_name, ['profile/mission-agency', 'id' => $missionLink->id, 'city' => $missionLink->url_city, 'name' => $missionLink->url_name], ['target' => '_blank']) ?><br>
				<!-- End Linked Mission Agency -->
				<br>
				<!-- Church Plant (Box 3) -->
				<?= empty($missionary->cp_pastor_at) ?
					NULL :
					'Church-Planting Pastor at ' . HTML::a($churchPlantLink, ['profile/church', 'id' => $churchPlant->id, 'city' => $churchPlant->url_city, 'name' => $churchPlant->url_name], ['target' => '_blank']) ?>
				<span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['cp']-1, 'id' => $profile->id]) ?></span>
				<br>
				<!-- Last Update -->
				<p><br><strong>Last Update: </strong><?= Yii::$app->formatter->asDate($profile->last_update) ?></p>
			</div>
        </div>
	</div>
	<p>&nbsp;</p>
</div>