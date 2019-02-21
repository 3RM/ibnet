<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Ministries:</p>
			<div class="right">
				<ul>
				<?php foreach ($ministries as $ministry) {
					echo '<li>' . Html::a($ministry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/' . ProfileController::$profilePageArray[$ministry->type], 'id' => $ministry->id, 'urlLoc' => $ministry->url_loc, 'urlName' => $ministry->url_name]) . '</li>';
				} ?>
				</ul>
			</div>
		</div>