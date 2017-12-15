<?php
use common\models\Utility;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

<h3>Connections</h3>
<hr>
	
<?php
if ($parentMinistry) { 					// Primary ministry
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($parentMinistry->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($parentMinistry->image2)); 
			echo '<div class="title">';
				echo Html::a($parentMinistry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$parentMinistry->type], 'urlLoc' => $parentMinistry->url_loc, 'name' => $parentMinistry->url_name, 'id' => $parentMinistry->id]);
				echo Html::tag('span', '<br>Serving on staff as ' . $profile->title, ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
if ($church) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($church->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($church->image2)); 
			echo '<div class="title">';
				echo Html::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$church->type], 'urlLoc' => $church->url_loc, 'name' => $church->url_name, 'id' => $church->id]);
				echo Html::tag('span', '<br>Home Church', ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
if ($pastor) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($pastor->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($pastor->image2)); 
			echo '<div class="title">';
				echo Html::a($pastor->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$pastor->type], 'urlLoc' => $pastor->url_loc, 'name' => $pastor->url_name, 'id' => $pastor->id]);
				echo Html::tag('span', '<br>' . $pastor->sub_type . (isset($church) ? ' at ' . $church->org_name : NULL), ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
if ($otherMinistryArray) {						// Other ministries
	foreach ($otherMinistryArray as $otherMinistry) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($otherMinistry->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($otherMinistry->image2)); 
				echo '<div class="title">';
					echo Html::a($otherMinistry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$staff->type], 'urlLoc' => $otherMinistry->url_loc, 'name' => $otherMinistry->url_name, 'id' => $otherMinistry->id]);
					echo Html::tag('span', '<br>Serving as ' . $otherMinistry->titleM, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($sChurchArray) {					// Church staff partners
	foreach ($sChurchArray as $sChurch) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($sChurch->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($sChurch->image2)); 
				echo '<div class="title">';
					echo Html::a($sChurch->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$sChurch->type], 'urlLoc' => $sChurch->url_loc, 'name' => $sChurch->url_name, 'id' => $sChurch->id]);
					echo Html::tag('span', '<br>Ministry Partner ' . (empty($church->org_name) ? NULL : ' at ' . $church->org_name), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($sMinistryArray) {					// Primary ministry staff partners
	foreach ($sMinistryArray as $sMinistry) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($sMinistry->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($sMinistry->image2)); 
				echo '<div class="title">';
					echo Html::a($sMinistry->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$sMinistry->type], 'urlLoc' => $sMinistry->url_loc, 'name' => $sMinistry->url_name, 'id' => $sMinistry->id]);
					echo Html::tag('span', '<br>Ministry Partner at ' . $sMinistry->titleM, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($sOtherArray) {					// Other ministry staff partners
	foreach ($sOtherArray as $sOther) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($sOther->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($sOther->image2)); 
				echo '<div class="title">';
					echo Html::a($sOther->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$sOther->type], 'urlLoc' => $sOther->url_loc, 'name' => $sOther->url_name, 'id' => $sOther->id]);
					echo Html::tag('span', '<br>Ministry Partner at ' . $sOther->titleM, ['class' => 'subTitle']);
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
if (empty($parentMinistry) && empty($church) && empty($pastor) && empty($otherMinistryArray) && empty($sChurchArray) && empty($sMinistryArray) && empty($sOtherArray) && empty($likeArray)) {
	echo '<em>No connections found.</em>';
}
?>