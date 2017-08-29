<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use frontend\controllers\ProfileFormController;
use kartik\markdown\Markdown;
use tugmaks\GoogleMaps\Map;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
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
            	'<li>' . Html::a(ProfileFormController::$formList[$i], ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => $i-1, 'id' => $profile->id]) . '</li>';
            }
            $i++;
        } ?>
    </ul>
</div>
<br />
<?= Alert::widget() ?>

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
					<!-- Begin Image & Description -->
					<p><?= Markdown::convert($profile->description) . '<span  class="edit">' . Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['nd']-1, 'id' => $profile->id]) . '</span>' ?></p>							
				 	<!-- End Image & Description -->
			</div>
		</div>
		<div class="row">
            <div class="col-md-4 profile-thirds">
            	<!-- Begin Contact Information (Box 1) -->
            	<span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['lo']-1, 'id' => $profile->id]) ?></span>
				<?php if ($profile->org_address1 && $profile->org_city && $profile->org_st_prov_reg && $profile->org_country) { ?>
					<?= Html::icon('map-marker') . ' ' ?>
					<?= empty($profile->org_address1) ? NULL : $profile->org_address1 . ', ' ?>
					<?= empty($profile->org_address2) ? NULL : $profile->org_address2 . ', ' ?>
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
				<!-- Begin School Levels (Box 2) -->
				<strong>School Levels Offered:</strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['sl']-1, 'id' => $profile->id]) ?></span><br>
				<?php foreach ($schoolLevel as $level) {
					echo $level['school_level'] . '<br>';
				} ?>
				<!-- End School Levels -->
				<br>
				<!-- Begin Social (Box 2) -->
				<strong>Social Media: </strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['co']-1, 'id' => $profile->id]) ?></span><br>
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
				<!-- Begin "Ministry of ... Baptist Church" (Box 3) -->
				<strong>Ministry of:</strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['pm']-1, 'id' => $profile->id]) ?></span><br>
				<?= (empty($church) || empty($churchLink)) ? '<br>' :
					HTML::a($churchLink, ['profile/church', 'id' => $church->id, 'city' => $church->url_city, 'name' => $church->url_name], ['target' => '_blank']) . '<br><br>' ?>
				<!-- End "Ministry of ... Baptist Church" -->
				<!-- Begin Accreditation/Association (Box 3) -->
				<strong>Accreditations:</strong><span  class="pull-right edit"><?= Html::a(Html::icon('edit'), ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['as']-1, 'id' => $profile->id]) ?></span><br>
				<?php if ($accreditations) {
					foreach ($accreditations as $accreditation) {
						echo HTML::a($accreditation->association, $accreditation->website, ['target' => '_blank']);
						echo '<br>';
					}
				} ?>
				<!-- End Association -->
				<br>
				<!-- Last Update -->
				<p><strong>Last Update: </strong><?= Yii::$app->formatter->asDate($profile->last_update) ?></p>
			</div>
        </div>
	</div>
	<p>&nbsp;</p>
</div>