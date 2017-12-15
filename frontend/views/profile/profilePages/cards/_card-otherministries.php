<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;	
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Also serving as:</p>
			<div class="right">
				<ul>
					<?php foreach ($otherMinistryArray as $ministry) { ?>
					<li><?= $ministry->titleM . ' at ' . HTML::a($ministry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/' . ProfileController::$profilePageArray[$ministry->type], 'id' => $ministry->id, 'urlLoc' => $ministry->url_loc, 'name' => $ministry->url_name]) ?></li>
					<?php } ?>
				</ul>
			</div>
		</div>