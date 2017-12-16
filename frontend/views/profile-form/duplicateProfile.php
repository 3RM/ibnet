<?php

use common\models\profile\Profile;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'Duplicate Profile?';
?>

<div class="site-index">

	<h1><?= Html::icon('duplicate') . ' ' . $this->title ?></h1>

	<p>Your new profile appears to be a duplicate of an existing profile:</p>

	<div class="row">
		<div class="bg-info col-md-4" style="padding:10px; margin:0 0 10pt 20pt">
			<?php if ($profile->category == Profile::CATEGORY_IND) { ?>
				<p>
					<?= Html::a('<h4>' . $duplicate->ind_first_name . ' ' . $duplicate->ind_last_name . '</h4>', [
						'/profile/view-profile',
			   			'id' => $duplicate->id,
			   			'name' => $duplicate->url_name,
			   			'urlLoc' => $duplicate->url_loc,
			   		],
			   		['target' => '_blank']) ?>
					<?= $duplicate->ind_address1 . ', '
					. ($duplicate->ind_address2 != NULL ? ($duplicate->ind_address2 . ', ') : '')
					. ($duplicate->ind_po_box != NULL ? ('PO Box ' . $duplicate->ind_po_box . ', ') : '')
					. $duplicate->ind_city . ', ' . $duplicate->ind_st_prov_reg . ' ' . $duplicate->ind_zip ?>
				</p>
			<?php } else { ?>
				<p>
					<?= Html::a('<h4>' . $duplicate->org_name . '</h4>', [
						'/profile/view-profile',
			   			'id' => $duplicate->id,
			   			'name' => $duplicate->url_name,
			   			'urlLoc' => $duplicate->url_loc,
			   		],
			   		['target' => '_blank']) ?>
					<?= $duplicate->org_address1 . ', '
					. ($duplicate->org_address2 != NULL ? ($duplicate->org_address2 . ', ') : '')
					. ($duplicate->org_po_box != NULL ? ('PO Box ' . $duplicate->org_po_box . ', ') : '') 
					. $duplicate->org_city . ', ' . $duplicate->org_st_prov_reg . ' ' . $duplicate->org_zip ?>
				</p>
			<?php } ?>
		</div>
	</div>

	<p>If this profile is different from the one above,	please 
		<?= HTML::a('edit', Url::to(['form2', 'profileId' => $profile->id])) ?>
		the name and address to make it unique.  If it is a duplicate entry, return
		to <?= HTML::a('My Profiles', Url::to(['my-profiles', 'profileId' => $profile->id])) ?>
		where you can delete this entry. If you feel that this message is in error, 
		please use the <?= HTML::a('Contact Form', Url::to(['site/contact'])) ?> and let us know 
		so that we can resolve this issue.
	</p>

	<p>&nbsp;</p>

</div>