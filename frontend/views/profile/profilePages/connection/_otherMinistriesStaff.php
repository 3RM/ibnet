<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($otherMinistriesStaff as $staff) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo $staff->profile->image2 ? Html::img($staff->profile->image2) : Html::img('@img.profile/profile-logo.png'); 
				echo '<div class="title">';
					echo Html::a($staff->profile->mainName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$staff->profile->type], 'urlLoc' => $staff->profile->url_loc, 'urlName' => $staff->profile->url_name, 'id' => $staff->profile->id]);
					echo Html::tag('span', 
						'<br>' . $staff->staff_title . ' at ' .
						Html::a($staff->name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$staff->type], 'urlLoc' => $staff->urlLoc, 'urlName' => $staff->urlName, 'id' => $staff->ministry_id]),
					['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
?>