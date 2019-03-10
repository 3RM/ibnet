<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<ul>
				<li> <?= $profile->title . empty($parentMinistry) ? NULL : ' at ' . Html::a($parentMinistry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/' . ProfileController::$profilePageArray[$parentMinistry->type], 'id' => $parentMinistry->id, 'urlLoc' => $parentMinistry->url_loc, 'urlName' => $parentMinistry->url_name]) ?></li>
				<li>Home Church: <?= empty($church) ? NULL : Html::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/church', 'id' => $church->id, 'urlLoc' => $church->url_loc, 'urlName' => $church->url_name]) ?></li>
			</ul>
		</div>