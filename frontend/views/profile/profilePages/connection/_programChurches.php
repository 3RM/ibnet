<?php
use common\models\Utility;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

foreach ($programChurches as $programChurch) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($programChurch->image2) ? Html::img('@img.profile/profile-logo.png'): Html::img($programChurch->image2)); 
			echo '<div class="title">';
				echo Html::a($programChurch->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$programChurch->type], 'urlLoc' => $programChurch->url_loc, 'urlName' => $programChurch->url_name, 'id' => $programChurch->id]);
				echo Html::tag('span', '<br>Program Chapter/Affiliate', ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
?>