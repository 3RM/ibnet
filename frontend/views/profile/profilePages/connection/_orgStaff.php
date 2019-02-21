<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($staff as $stf) {
		echo '<div class="connection-container">'; 
			echo '<div class="connection">';
				echo (empty($stf->profile->image2) ? Html::img('@img.profile/profile-logo.png'): Html::img($stf->profile->image2)); 
				echo '<div class="title">';
					echo Html::a($stf->profile->mainName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$stf->profile->type], 'urlLoc' => $stf->profile->url_loc, 'urlName' => $stf->profile->url_name, 'id' => $stf->profile->id]);
					echo Html::tag('span', '<br>' . $stf->staff_title, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
?>