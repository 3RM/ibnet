<?php
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Church Plant:</p>
			<div class="right">
				<?= HTML::a($churchPlant->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/church', 'id' => $churchPlant->id, 'urlLoc' => $churchPlant->url_loc, 'urlName' => $churchPlant->url_name]) ?>
			</div>
		</div>