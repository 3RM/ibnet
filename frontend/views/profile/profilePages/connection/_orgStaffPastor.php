<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	echo '<div class="connection-container">'; 
		echo '<div class="connection">';
			echo $pastor->image2 ? Html::img($pastor->image2) : Html::img('@img.profile/profile-logo.png'); 
			echo '<div class="title">';
				echo Html::a($pastor->mainName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$pastor->type], 'urlLoc' => $pastor->url_loc, 'urlName' => $pastor->url_name, 'id' => $pastor->id]);
				echo Html::tag('span', '<br>Pastor at ' . 
					Html::a($parentMinistry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$parentMinistry->type], 'urlLoc' => $parentMinistry->url_loc, 'urlName' => $parentMinistry->url_name, 'id' => $parentMinistry->id]), 
					['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
?>