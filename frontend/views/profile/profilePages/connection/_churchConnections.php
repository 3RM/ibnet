<?php
use common\models\Utility;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

<h3>Connections</h3>
<hr>

<?php
if ($pastor) {
	echo '<div class="connection-container">';
		echo '<div class="connection">';
			echo (empty($pastor->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($pastor->image2)); 
			echo '<div class="title">';
				echo Html::a($pastor->getFormattedNames()->formattedNames . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$pastor->type], 'urlLoc' => $pastor->url_loc, 'name' => $pastor->url_name, 'id' => $pastor->id]);
				echo Html::tag('span', '<br>' . $pastor->sub_type, ['class' => 'subTitle']);
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
					echo Html::tag('span', '<br>' . $staff->titleM, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($ministryArray) {
	foreach ($ministryArray as $ministry) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($ministry->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($ministry->image2)); 
				echo '<div class="title">';
					echo Html::a($ministry->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$ministry->type], 'urlLoc' => $ministry->url_loc, 'name' => $ministry->url_name, 'id' => $ministry->id]);
					echo Html::tag('span', '<br>Ministry of ' . $profile->org_name, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($programArray) {
	foreach ($programArray as $program) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($program->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($program->image2)); 
				echo '<div class="title">';
					echo Html::a($program->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$program->type], 'urlLoc' => $program->url_loc, 'name' => $program->url_name, 'id' => $program->id]);
					echo Html::tag('span', '<br>Program of ' . $profile->org_name, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($fArray) {
	foreach ($fArray as $f) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($f->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($f->image2)); 
				echo '<div class="title">';
					echo Html::a($f->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$f->type], 'urlLoc' => $f->url_loc, 'name' => $f->url_name, 'id' => $f->id]);
					echo Html::tag('span', '<br>Fellowship', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($aArray) {
	foreach ($aArray as $a) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($a->image2) ? Html::img('@web/images/content/profile-logo.png'): Html::img($a->image2)); 
				echo '<div class="title">';
					echo Html::a($a->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$a->type], 'urlLoc' => $a->url_loc, 'name' => $a->url_name, 'id' => $a->id]);
					echo Html::tag('span', '<br>Association', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}
if ($memberArray) {
	foreach ($memberArray as $member) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($member['usr_image']) ? Html::img('@web/images/content/user.png'): Html::img($member['usr_image'])); 
				echo '<div class="title">';
					echo $member->screen_name;
					echo Html::tag('span', '<br>Church member', ['class' => 'subTitle']);
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
if (empty($pastor) && empty($staffArray) && empty($ministryArray) && empty($programArray) && empty($fArray) && empty($aArray) && empty($memberArray) && empty($likeArray)) {
	echo '<em>No connections found.</em>';
}
?>