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
		if ($parentMinistry) {					// Parent ministry
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($parentMinistry->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($parentMinistry->image2, ['class' => 'img-circle'])) . '</div>'; 
				echo '<div class="title">';
					echo Html::a($parentMinistry->org_name, [ProfileController::$profilePageArray[$parentMinistry->type], 'city' => $parentMinistry->url_city, 'name' => $parentMinistry->url_name, 'id' => $parentMinistry->id]);
					echo Html::tag('span', '<br>Parent Ministry', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';

			if ($parentMinistry->type == 'Church' && $pastor) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($pastor->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($pastor->image2, ['class' => 'img-circle'])) . '</div>';
					echo '<div class="title">';
						echo Html::a($pastor->getFormattedNames()->formattedNames, [ProfileController::$profilePageArray[$pastor->type], 'city' => $pastor->url_city, 'name' => $pastor->url_name, 'id' => $pastor->id]);
						echo Html::tag('span', '<br>' . $pastor->sub_type . ' of ' . $parentMinistry->org_name, ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
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
		if ($programChurchArray) {
			foreach ($programChurchArray as $programChurch) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($programChurch->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($programChurch->image2, ['class' => 'img-circle'])) . '</div>'; 
					echo '<div class="title">';
						echo Html::a($programChurch->org_name, [ProfileController::$profilePageArray[$programChurch->type], 'city' => $programChurch->url_city, 'name' => $programChurch->url_name, 'id' => $programChurch->id]);
						echo Html::tag('span', '<br>Program Chapter/Affiliate', ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if (empty($parentMinistry) && empty($staffArray) && empty($programChurchArray)) {
			echo '<em>No connections found.</em>';
		}
		?>
	</div>

</div>