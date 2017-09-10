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
		if ($parentMinistry) {
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($parentMinistry->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($parentMinistry->image2, ['class' => 'img-circle'])) . '</div>'; 
				
				echo Html::a($parentMinistry->org_name, [ProfileController::$profilePageArray[$parentMinistry->type], 'city' => $parentMinistry->url_city, 'name' => $parentMinistry->url_name, 'id' => $parentMinistry->id]);
				echo Html::tag('span', '<br>Parent Ministry', ['class' => 'subTitle']);
			echo '</div>';
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
		if ($missionaryArray) {
			foreach ($missionaryArray as $missionary) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($missionary->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($missionary->image2, ['class' => 'img-circle'])) . '</div>'; 
					if ($missionary->{'profile'}->spouse_first_name != NULL) {
        			    $name = $missionary->{'profile'}->ind_first_name . ' (& ' . $missionary->{'profile'}->spouse_first_name . ') ' . $missionary->{'profile'}->ind_last_name;
        			} else {
        			    $name = $missionary->{'profile'}->ind_first_name . ' ' . $missionary->{'profile'}->ind_last_name;
        			}
					echo '<div class="title">';
						echo Html::a($name, [ProfileController::$profilePageArray[$missionary->{'profile'}->type], 'city' => $missionary->{'profile'}->url_city, 'name' => $missionary->{'profile'}->url_name, 'id' => $missionary->{'profile'}->id]);
						echo Html::tag('span', '<br>' . $missionary->{'profile'}->type . ($missionary->field == NULL ? NULL : ' to ' . $missionary->field), ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if (empty($parentMinistry) && empty($staffArray) && empty($missionaryArray)) {
			echo '<em>No connections found.</em>';
		}
		?>
	</div>

</div>