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
	if ($parentMinistry->type == 'Church' && $pastor) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($pastor->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($pastor->image2)); 
				echo '<div class="title">';
					echo Html::a($pastor->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$pastor->type], 'urlLoc' => $pastor->url_loc, 'name' => $pastor->url_name, 'id' => $pastor->id]);
					echo Html::tag('span', '<br>' . $pastor->sub_type . ' of ' . $church->org_name, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
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
						echo $like->fullName;
						echo Html::tag('span', '<br>Friend of the ministry', ['class' => 'subTitle']);
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';
	}
}
if (empty($parentMinistry) && empty($staffArray) && empty($likeArray)) {
	echo '<em>No connections found.</em>';
}
?>