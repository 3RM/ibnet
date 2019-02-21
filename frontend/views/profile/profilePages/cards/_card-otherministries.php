<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;	
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Also serving as:</p>
			<div class="right">
				<ul>
					<?php foreach ($otherMinistries as $min) { ?>
					<li><?= $min->staff_title . ' at ' . HTML::a($min->ministry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/' . ProfileController::$profilePageArray[$min->ministry->type], 'id' => $min->ministry->id, 'urlLoc' => $min->ministry->url_loc, 'urlName' => $min->ministry->url_name]) ?></li>
					<?php } ?>
				</ul>
			</div>
		</div>