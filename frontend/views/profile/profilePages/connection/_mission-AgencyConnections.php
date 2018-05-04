<?php
use common\models\Utility;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

<h3>Connections</h3>
<hr>
	
<?php
if ($parentMinistry) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($parentMinistry->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($parentMinistry->image2)); 
			echo '<div class="title">';
				echo Html::a($parentMinistry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$parentMinistry->type], 'urlLoc' => $parentMinistry->url_loc, 'name' => $parentMinistry->url_name, 'id' => $parentMinistry->id]);
				echo Html::tag('span', '<br>Parent Ministry', ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
if ($staffArray) {
	foreach ($staffArray as $staff) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($staff->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($staff->image2)); 
				echo '<div class="title">';
					echo Html::a($staff->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$staff->type], 'urlLoc' => $staff->url_loc, 'name' => $staff->url_name, 'id' => $staff->id]);
					echo Html::tag('span', '<br>Staff: ' . $staff->titleM, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($missionaryArray) {
	foreach ($missionaryArray as $missionary) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($missionary->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($missionary->image2)); 
				if ($missionary->{'profile'}->spouse_first_name != NULL) {
      				    $name = $missionary->{'profile'}->ind_first_name . ' (& ' . $missionary->{'profile'}->spouse_first_name . ') ' . $missionary->{'profile'}->ind_last_name;
      				} else {
      				    $name = $missionary->{'profile'}->ind_first_name . ' ' . $missionary->{'profile'}->ind_last_name;
      				}
				echo '<div class="title">';
					echo Html::a($name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$missionary->{'profile'}->type], 'urlLoc' => $missionary->{'profile'}->url_loc, 'name' => $missionary->{'profile'}->url_name, 'id' => $missionary->{'profile'}->id]);
					echo Html::tag('span', '<br>' . $missionary->{'profile'}->type . ($missionary->field == NULL ? NULL : ' to ' . $missionary->field), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($likeArray) {
	foreach ($likeArray as $like) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				if ($like instanceof Profile) {
					echo (empty($like->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($like->image2)); 
				echo '<div class="title">';
					echo Html::a($like->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$like->type], 'urlLoc' => $like->url_loc, 'name' => $like->url_name, 'id' => $like->id]);
					echo Html::tag('span', '<br>Friend of the ministry', ['class' => 'subTitle']);
				echo '</div>';
				} else {
					echo (empty($like->usr_image) ? Html::img('@web/images/content/user.png') : Html::img($like->usr_image)); 
					echo '<div class="title">';
						echo $like->screen_name;
						echo Html::tag('span', '<br>Friend of the ministry', ['class' => 'subTitle']);
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';
	}
}
if (empty($parentMinistry) && empty($staffArray) && empty($missionaryArray) && empty($likeArray)) {
	echo '<em>No connections found.</em>';
}
?>