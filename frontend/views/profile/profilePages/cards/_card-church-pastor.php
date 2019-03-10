<?php
use common\models\profile\Profile;	
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<div class="center">
				Pastor 
				<?= (!Yii::$app->user->isGuest && $pastor) ? 
					Html::a($pastor->mainName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/' . ProfileController::$profilePageArray[$pastor->type], 'urlLoc' => $pastor->url_loc, 'urlName' => $pastor->url_name, 'id' => $pastor->id]) : 
					$profile->mainName 
				?>
				<?= $profile->pastor_interim ? '<br>(Interim)' : NULL ?>
				<?= $profile->cp_pastor ? '<br>(Church Planter)' : NULL ?>
			</div>
		</div>