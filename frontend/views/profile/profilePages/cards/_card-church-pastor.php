<?php
use common\models\profile\Profile;	
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<div class="center">
				Pastor 
				<?= empty($pastor) ? $profile->formattedNames : Html::a($profile->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['pastor', 'id' => $pastor->id, 'urlLoc' => $pastor->url_loc, 'name' => $pastor->url_name]); ?>
				<?= $profile->pastor_interim ? '<br>(Interim)' : NULL ?>
				<?= $profile->cp_pastor ? '<br>(Church Planter)' : NULL ?>
			</div>
		</div>