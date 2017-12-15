<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<ul>
				<li><?= empty($church) ? NULL : 'Home Church: ' . Html::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['church', 'id' => $church->id, 'urlLoc' => $church->url_loc, 'name' => $church->url_name]) ?></li>
				
				<?php if ($mission) { ?>
				<li>Mission Agency: <?= empty($missionLink) ? $mission->mission : Html::a($missionLink->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['mission-agency', 'id' => $missionLink->id, 'urlLoc' => $missionLink->url_loc, 'name' => $missionLink->url_name]) ?></li>
				<?php } ?>
			</ul>
		</div>