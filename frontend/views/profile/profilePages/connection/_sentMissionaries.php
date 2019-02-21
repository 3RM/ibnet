<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($sentMissionaries as $profile) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo $profile->image2 ? Html::img($profile->image2) : Html::img('@img.profile/profile-logo.png'); 
				echo '<div class="title">';
					echo Html::a($profile->coupleName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name, 'id' => $profile->id]);
					echo Html::tag('span', '<br>' . $profile->pluralType . ($profile->missionary->field == NULL ? NULL : ' sent to ' . $profile->missionary->field), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}

?>