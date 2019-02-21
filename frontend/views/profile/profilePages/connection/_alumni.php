<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($alumni->profiles as $alumnus) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo $alumnus->image2 ? Html::img($alumnus->image2) : Html::img('@img.profile/profile-logo.png'); 
				echo '<div class="title">';
					echo Html::a($alumnus->fullName . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$alumnus->type], 'urlLoc' => $alumnus->url_loc, 'urlName' => $alumnus->url_name, 'id' => $alumnus->id]);
					echo Html::tag('span', '<br>Alumnus/Alumna ', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}

?>