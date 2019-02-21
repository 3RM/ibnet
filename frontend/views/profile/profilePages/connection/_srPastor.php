<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	if ($pastor) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo $pastor->image2 ? Html::img($pastor->image2) : Html::img('@img.profile/profile-logo.png'); 
				echo '<div class="title">';
					echo Html::a($pastor->mainName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$pastor->type], 'urlLoc' => $pastor->url_loc, 'urlName' => $pastor->url_name, 'id' => $pastor->id]);
					echo (($type == Profile::TYPE_MISSIONARY) || ($type == Profile::TYPE_CHAPLAIN) || ($type == Profile::TYPE_EVANGELIST)) ?
						Html::tag('span', '<br>Sending Pastor', ['class' => 'subTitle']) :
						Html::tag('span', '<br>Pastor at ' . Html::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$church->type], 'urlLoc' => $church->url_loc, 'urlName' => $church->url_name, 'id' => $church->id]), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
?>