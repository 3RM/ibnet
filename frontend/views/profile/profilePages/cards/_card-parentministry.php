<?php
use common\models\profile\Profile;	
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<div class="center">
				<?= $profile->org_name . ' is a ministry of ' . HTML::a($parentMinistry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['church', 'id' => $parentMinistry->id, 'urlLoc' => $parentMinistry->url_loc, 'name' => $parentMinistry->url_name]) ?>
			</div>
		</div>