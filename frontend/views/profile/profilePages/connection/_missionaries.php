<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($missionaries as $missionary) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo $missionary->profile->image2 ? Html::img($missionary->profile->image2) : Html::img('@img.profile/profile-logo.png'); 
				echo '<div class="title">';
					echo Html::a($missionary->profile->coupleName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$missionary->profile->type], 'urlLoc' => $missionary->profile->url_loc, 'urlName' => $missionary->profile->url_name, 'id' => $missionary->profile->id]);
					echo Html::tag('span', '<br>' . $missionary->profile->pluralType . ($missionary->field == NULL ? NULL : ' to ' . $missionary->field), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}

?>