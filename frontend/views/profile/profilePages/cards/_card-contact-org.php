<?php
use common\models\profile\Profile;	
use yii\bootstrap\Html;
?>

		<div class="card contact">
			<div class="address-container group">
				<?php if (($profile->org_address1 && $profile->org_city && $profile->org_st_prov_reg && $profile->org_country) &&
					(($profile->org_po_address1 || $profile->org_po_box) && $profile->org_po_city && $profile->org_po_st_prov_reg && $profile->org_po_country)) { ?>
				<div class="address-left">
					<?= Html::icon('map-marker') ?><br>
					<?= empty($profile->org_address1) ? NULL : $profile->org_address1 . '<br>' ?>
					<?= empty($profile->org_address2) ? NULL : $profile->org_address2 . '<br>' ?>
					<?= empty($profile->org_box) ? NULL : ' PO Box ' . $profile->org_box . '<br>' ?>
					<?php if ($profile->org_country == 'United States') {
						echo $profile->org_city . ', ' . $profile->org_st_prov_reg . ' ' . $profile->org_zip;
					} else {
						echo $profile->org_city . '<br>';
						echo $profile->org_st_prov_reg . '<br>';
						empty($profile->org_zip) ? NULL : $profile->org_zip . '<br>';
						echo $profile->org_country;
					} ?>	
				</div>
				<div class="mailing-right">
					<?= Html::icon('envelope') . ' ' ?><br>
					<?= empty($profile->org_po_address1) ? NULL : $profile->org_po_address1 . '<br>' ?>
					<?= empty($profile->org_po_address2) ? NULL : $profile->org_po_address2 . '<br>' ?>
					<?= empty($profile->org_po_box) ? NULL : ' PO Box ' . $profile->org_po_box . '<br>' ?>
					<?php if ($profile->org_po_country == 'United States') {
						echo $profile->org_po_city . ', ' . $profile->org_po_st_prov_reg . ' ' . $profile->org_po_zip . '<br>';
						echo $profile->org_po_country; 
					} else {
						echo $profile->org_po_city . '<br>';
						echo $profile->org_po_st_prov_reg . '<br>';
						empty($profile->org_po_zip) ? NULL : $profile->org_po_zip . '<br>';
						echo $profile->org_po_country;
					} ?>	
				</div>
				<?php } elseif ($profile->org_address1 && $profile->org_city && $profile->org_st_prov_reg && $profile->org_country) { ?>
				<div class="address">
					<?= Html::icon('map-marker') ?><br>
					<?= empty($profile->org_address1) ? NULL : $profile->org_address1 . '<br>' ?>
					<?= empty($profile->org_address2) ? NULL : $profile->org_address2 . '<br>' ?>
					<?= empty($profile->org_box) ? NULL : ' PO Box ' . $profile->org_box . '<br>' ?>
					<?php if ($profile->org_country == 'United States') {
						echo $profile->org_city . ', ' . $profile->org_st_prov_reg . ' ' . $profile->org_zip;
					} else {
						echo $profile->org_city . '<br>';
						echo $profile->org_st_prov_reg . '<br>';
						empty($profile->org_zip) ? NULL : $profile->org_zip . '<br>';
						echo $profile->org_country;
					} ?>	
				</div>
				<?php } elseif (($profile->org_po_address1 || $profile->org_po_box) && $profile->org_po_city && $profile->org_po_st_prov_reg && $profile->org_po_country) { ?>
					<div class="address">
					<?= Html::icon('envelope') . ' ' ?><br>
					<?= empty($profile->org_po_address1) ? NULL : $profile->org_po_address1 . '<br>' ?>
					<?= empty($profile->org_po_address2) ? NULL : $profile->org_po_address2 . '<br>' ?>
					<?= empty($profile->org_po_box) ? NULL : ' PO Box ' . $profile->org_po_box . '<br>' ?>
					<?php if ($profile->org_po_country == 'United States') {
						echo $profile->org_po_city . ', ' . $profile->org_po_st_prov_reg . ' ' . $profile->org_po_zip . '<br>';
						echo $profile->org_po_country; 
					} else {
						echo $profile->org_po_city . '<br>';
						echo $profile->org_po_st_prov_reg . '<br>';
						empty($profile->org_po_zip) ? NULL : $profile->org_po_zip . '<br>';
						echo $profile->org_po_country;
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