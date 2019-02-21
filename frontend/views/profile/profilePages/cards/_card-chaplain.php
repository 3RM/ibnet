<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

<div class="card title-list">
	<ul>
		<li><?= $church ? 'Home Church: ' . Html::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/church', 'id' => $church->id, 'urlLoc' => $church->url_loc, 'urlName' => $church->url_name]) : NULL ?></li>
		
		<?php if ($missionAgcyProfile) { ?>
		<li>Mission Agency: <?= $missionAgcyProfile ? Html::a($missionAgcyProfile->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/mission-agency', 'id' => $missionAgcyProfile->id, 'urlLoc' => $missionAgcyProfile->url_loc, 'urlName' => $missionAgcyProfile->url_name]) : NULL ?></li>
		<?php } ?>
	</ul>
</div>