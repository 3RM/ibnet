<?php
use common\models\Utility;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>
	
<h3>Connections</h3>
<hr>

<?php
if ($church) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($church->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($church->image2)); 
			echo '<div class="title">';
				echo Html::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$church->type], 'urlLoc' => $church->url_loc, 'name' => $church->url_name, 'id' => $church->id]);
				echo Html::tag('span', '<br>Sending Church', ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
if ($missionLink) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($missionLink->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($missionLink->image2)); 
			echo '<div class="title">';
				echo Html::a($missionLink->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$missionLink->type], 'urlLoc' => $missionLink->url_loc, 'name' => $missionLink->url_name, 'id' => $missionLink->id]);
				echo Html::tag('span', '<br>Mission Agency', ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
if ($pastor) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($pastor->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($pastor->image2)); 
			echo '<div class="title">';
				echo Html::a($pastor->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$pastor->type], 'urlLoc' => $pastor->url_loc, 'name' => $pastor->url_name, 'id' => $pastor->id]);
				echo Html::tag('span', '<br> Sending Pastor', ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
if ($churchPlant) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($churchPlant->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($churchPlant->image2)); 
			echo '<div class="title">';
				echo Html::a($churchPlant->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$churchPlant->type], 'urlLoc' => $churchPlant->url_loc, 'name' => $churchPlant->url_name, 'id' => $churchPlant->id]);
				echo Html::tag('span', '<br>Church Plant', ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
if ($otherMinistryArray) {						// Other Ministries
	foreach ($otherMinistryArray as $otherMinistry) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($otherMinistry->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($otherMinistry->image2)); 
				echo '<div class="title">';
					echo Html::a($otherMinistry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$otherMinistry->type], 'urlLoc' => $otherMinistry->url_loc, 'name' => $otherMinistry->url_name, 'id' => $otherMinistry->id]);
					echo Html::tag('span', '<br>Serving as ' . $otherMinistry->titleM, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($sCPArray) {							// Fellow church plant staff
	foreach ($sCPArray as $sCP) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($sCP->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($sCP->image2)); 
				echo '<div class="title">';
					echo Html::a($sCP->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$sCP->type], 'urlLoc' => $sCP->url_loc, 'name' => $sCP->url_name, 'id' => $sCP->id]);
					echo Html::tag('span', '<br>Ministry Partner ' . (empty($churchPlant->org_name) ? NULL : ' at ' . $churchPlant->org_name), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($sChurchArray) {						// Fellow sending church staff
	foreach ($sChurchArray as $sChurch) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($sChurch->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($sChurch->image2)); 
				echo '<div class="title">';
					echo Html::a($sChurch->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$sChurch->type], 'urlLoc' => $sChurch->url_loc, 'name' => $sChurch->url_name, 'id' => $sChurch->id]);
					echo Html::tag('span', '<br>Ministry Partner ' . (empty($church->org_name) ? NULL : ' at ' . $church->org_name), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($sOtherArray) {							// Other ministry staff partners
	foreach ($sOtherArray as $sOther) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($sOther->image2) ? Html::img('@web/images/content/profile-logo.png') : Html::img($sOther->image2)); 
				echo '<div class="title">';
					echo Html::a($sOther->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$sOther->type], 'urlLoc' => $sOther->url_loc, 'name' => $sOther->url_name, 'id' => $sOther->id]);
					echo Html::tag('span', '<br>Ministry Partner at ' . $sOther->titleM, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($memberArray) {
	foreach ($memberArray as $member) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($member['usr_image']) ? Html::img('@web/images/content/user.png') : Html::img($member['usr_image'])); 
				echo '<div class="title">';
					echo $member->fullName;
					echo Html::tag('span', '<br>Fellow church member', ['class' => 'subTitle']);
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
if (empty($church) && empty($missionLink) && empty($pastor) && empty($churchPlant) && empty($otherMinistryArray) && empty($sCPArray) && empty($sChurchArray) && empty($sOtherArray) && empty($memberArray) && empty($likeArray)) {
	echo '<em>No connections found.</em>';
}
?>