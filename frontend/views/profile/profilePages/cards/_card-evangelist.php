<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<ul>
				<li><?= $church ? 'Home Church: ' . Html::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/church', 'id' => $church->id, 'urlLoc' => $church->url_loc, 'urlName' => $church->url_name]) : NULL ?></li>
				<li><?= $parentMinistry ? 'Serving with: ' . Html::a($parentMinistry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/' . ProfileController::$profilePageArray[$parentMinistry->type], 'id' => $parentMinistry->id, 'urlLoc' => $parentMinistry->url_loc, 'urlName' => $parentMinistry->url_name]) : NULL ?></li>
			</ul>
		</div>