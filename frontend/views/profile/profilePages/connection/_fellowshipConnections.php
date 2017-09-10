<?php
use common\models\Utility;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

<div class="container">
	
	<h3>Connections</h3>
	<hr>
	
	<div class="connection-container">
		<?php
		if ($staffArray) {
			foreach ($staffArray as $staff) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($staff->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($staff->image2, ['class' => 'img-circle'])) . '</div>'; 
					echo '<div class="title">';
						echo Html::a($staff->getFormattedNames()->formattedNames, [ProfileController::$profilePageArray[$staff->type], 'city' => $staff->url_city, 'name' => $staff->url_name, 'id' => $staff->id]);
						echo Html::tag('span', '<br>Staff: ' . $staff->titleM, ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if ($indvArray) {
			foreach ($indvArray as $indv) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($indv->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($indv->image2, ['class' => 'img-circle'])) . '</div>'; 
					echo '<div class="title">';
						echo Html::a($indv->getFormattedNames()->formattedNames, [ProfileController::$profilePageArray[$indv->type], 'city' => $indv->url_city, 'name' => $indv->url_name, 'id' => $indv->id]);
						echo Html::tag('span', '<br>Member', ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if ($churchArray) {
			foreach ($churchArray as $church) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($church->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($church->image2, ['class' => 'img-circle'])) . '</div>'; 
					echo '<div class="title">';
						echo Html::a($church->org_name, [ProfileController::$profilePageArray[$church->type], 'city' => $church->url_city, 'name' => $church->url_name, 'id' => $church->id]);
						echo Html::tag('span', '<br>Member Church', ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if (empty($staffArray) && empty($indvArray) && empty($churchArray)) {
			echo '<em>No connections found.</em>';
		}
		?>
	</div>

</div>