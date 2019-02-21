<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($churchMembers as $member) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo $member->usr_image ? Html::img($member->usr_image) : Html::img('@img.site/user.png'); 
				echo '<div class="title">';
					echo $member->fullName;
					echo Html::tag('span', '<br>Fellow member at ' . Html::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$church->type], 'urlLoc' => $church->url_loc, 'urlName' => $church->url_name, 'id' => $church->id]), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
?>