<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

<div class="card title-list">
	<?= html::img('@img.flag/' . str_replace(' ', '-', $missionary->field) . '.png', ['alt' => 'Country flag']) ?>
	<div class="right">
		<ul>
			<li><?= $profile->sub_type ?></li>
			<li>Field: <?= $missionary->field ?></li>
			<li>Status: <?= $missionary->status ?></li>
			<li><?= $church ? 'Home Church: ' . Html::a($church->org_name . '&nbsp' . 
				Html::icon('link', ['class' => 'internal-link']), ['profile/church', 
					'id' => $church->id, 
					'urlLoc' => $church->url_loc, 
					'urlName' => $church->url_name
				]) : NULL ?>
			</li>
			<li>Mission Agency: <?= $missionAgcyProfile ? Html::a($missionAgcyProfile->org_name . '&nbsp' . 
				Html::icon('link', ['class' => 'internal-link']), ['profile/mission-agency', 
					'id' => $missionAgcyProfile->id, 
					'urlLoc' => $missionAgcyProfile->url_loc, 
					'urlName' => $missionAgcyProfile->url_name
				]) : $missionAgcy->mission ?>
			</li>
		</ul>
	</div>
</div>