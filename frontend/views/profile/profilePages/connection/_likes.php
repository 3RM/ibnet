<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($likeProfiles as $like) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				if ($like instanceof Profile) {
					echo $like->image2 ? Html::img($like->image2) : Html::img('@img.profile/profile-logo.png'); 
					echo '<div class="title">';
						echo Html::a($like->coupleName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$like->type], 'urlLoc' => $like->url_loc, 'urlName' => $like->url_name, 'id' => $like->id]);
						echo Html::tag('span', '<br>Friend of the ministry', ['class' => 'subTitle']);
					echo '</div>';
				} else {
					echo $like->usr_image ? Html::img($like->usr_image) : Html::img('@img.site/user.png'); 
					echo '<div class="title">';
						echo $like->fullName;
						echo Html::tag('span', '<br>Friend of the ministry', ['class' => 'subTitle']);
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';
	}
?>