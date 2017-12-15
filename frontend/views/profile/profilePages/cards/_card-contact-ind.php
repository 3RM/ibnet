<?php
use common\models\profile\Profile;	
use yii\bootstrap\Html;
?>

		<div class="card contact">
			<div class="address-container group">
				<?php if (($profile->ind_address1 && $profile->ind_city && $profile->ind_st_prov_reg && $profile->ind_country) &&
					(($profile->ind_po_address1 || $profile->ind_po_box) && $profile->ind_po_city && $profile->ind_po_st_prov_reg && $profile->ind_po_country)) { ?>
				<div class="address-left">
					<?= Html::icon('map-marker') ?><br>
					<?= empty($profile->ind_address1) ? NULL : $profile->ind_address1 . '<br>' ?>
					<?= empty($profile->ind_address2) ? NULL : $profile->ind_address2 . '<br>' ?>
					<?= empty($profile->ind_box) ? NULL : ' PO Box ' . $profile->ind_box . '<br>' ?>
					<?php if ($profile->ind_country == 'United States') {
						echo $profile->ind_city . ', ' . $profile->ind_st_prov_reg . ' ' . $profile->ind_zip;
					} else {
						echo $profile->ind_city . '<br>';
						echo $profile->ind_st_prov_reg . '<br>';
						empty($profile->ind_zip) ? NULL : $profile->ind_zip . '<br>';
						echo $profile->ind_country;
					} ?>	
				</div>
				<div class="mailing-right">
					<?= Html::icon('envelope') . ' ' ?><br>
					<?= empty($profile->ind_po_address1) ? NULL : $profile->ind_po_address1 . '<br>' ?>
					<?= empty($profile->ind_po_address2) ? NULL : $profile->ind_po_address2 . '<br>' ?>
					<?= empty($profile->ind_po_box) ? NULL : ' PO Box ' . $profile->ind_po_box . '<br>' ?>
					<?php if ($profile->ind_po_country == 'United States') {
						echo $profile->ind_po_city . ', ' . $profile->ind_po_st_prov_reg . ' ' . $profile->ind_po_zip . '<br>';
						echo $profile->ind_po_country; 
					} else {
						echo $profile->ind_po_city . '<br>';
						echo $profile->ind_po_st_prov_reg . '<br>';
						empty($profile->ind_po_zip) ? NULL : $profile->ind_po_zip . '<br>';
						echo $profile->ind_po_country;
					} ?>	
				</div>
				<?php } elseif ($profile->ind_address1 && $profile->ind_city && $profile->ind_st_prov_reg && $profile->ind_country) { ?>
				<div class="address">
					<?= Html::icon('map-marker') ?><br>
					<?= empty($profile->ind_address1) ? NULL : $profile->ind_address1 . '<br>' ?>
					<?= empty($profile->ind_address2) ? NULL : $profile->ind_address2 . '<br>' ?>
					<?= empty($profile->ind_box) ? NULL : ' PO Box ' . $profile->ind_box . '<br>' ?>
					<?php if ($profile->ind_country == 'United States') {
						echo $profile->ind_city . ', ' . $profile->ind_st_prov_reg . ' ' . $profile->ind_zip;
					} else {
						echo $profile->ind_city . '<br>';
						echo $profile->ind_st_prov_reg . '<br>';
						empty($profile->ind_zip) ? NULL : $profile->ind_zip . '<br>';
						echo $profile->ind_country;
					} ?>	
				</div>
				<?php } elseif (($profile->ind_po_address1 || $profile->ind_po_box) && $profile->ind_po_city && $profile->ind_po_st_prov_reg && $profile->ind_po_country) { ?>
					<div class="address">
					<?= Html::icon('envelope') . ' ' ?><br>
					<?= empty($profile->ind_po_address1) ? NULL : $profile->ind_po_address1 . '<br>' ?>
					<?= empty($profile->ind_po_address2) ? NULL : $profile->ind_po_address2 . '<br>' ?>
					<?= empty($profile->ind_po_box) ? NULL : ' PO Box ' . $profile->ind_po_box . '<br>' ?>
					<?php if ($profile->ind_po_country == 'United States') {
						echo $profile->ind_po_city . ', ' . $profile->ind_po_st_prov_reg . ' ' . $profile->ind_po_zip . '<br>';
						echo $profile->ind_po_country; 
					} else {
						echo $profile->ind_po_city . '<br>';
						echo $profile->ind_po_st_prov_reg . '<br>';
						empty($profile->ind_po_zip) ? NULL : $profile->ind_po_zip . '<br>';
						echo $profile->ind_po_country;
					} ?>	
				</div>
				<?php } ?>
			</div>
			<div class="phone">
				<ul>
					<li><?= Html::icon('phone', ['class' => 'icon-padding']) . ' ' . $profile->phone ?></li>
					<li><?= empty($profile->website) ? NULL : Html::icon('globe', ['class' => 'icon-padding']) . ' ' . HTML::a($profile->website, $profile->website, ['target' => 'blank']) ?></li>
					<li>
						<?php if ($profile->email_pvt && $profile->email_pvt_status != PROFILE::PRIVATE_EMAIL_ACTIVE) {
							echo Html::icon('send', ['class' => 'icon-padding']) . ' <em>Pending</em>';
						} elseif ($profile->email) {
						 	echo Html::icon('send', ['class' => 'icon-padding']) . ' ' . Html::mailto($profile->email, $profile->email);
						} ?>
					</li>
				</ul>
			</div>
		</div>