<?php
use common\models\Utility;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

<div class="container">
	
	<h3>Connections</h3>
	<hr>
	
	<?php
	if ($pastor) {
		echo '<div class="connection">';
			echo '<div class="image">' . (empty($pastor->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($pastor->image2, ['class' => 'img-circle'])) . '</div>'; 
			echo '<div class="title">';
				echo Html::a($pastor->getFormattedNames()->formattedNames, [ProfileController::$profilePageArray[$pastor->type], 'city' => $pastor->url_city, 'name' => $pastor->url_name, 'id' => $pastor->id]);
				echo Html::tag('span', '<br>' . $pastor->sub_type, ['class' => 'subTitle']);
			echo '</div>';
		echo '</div>';
	}
	if ($staffArray) {
		foreach ($staffArray as $staff) {
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($staff->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($staff->image2, ['class' => 'img-circle'])) . '</div>'; 
				echo '<div class="title">';
					echo Html::a($staff->getFormattedNames()->formattedNames, [ProfileController::$profilePageArray[$staff->type], 'city' => $staff->url_city, 'name' => $staff->url_name, 'id' => $staff->id]);
					echo Html::tag('span', '<br>' . $staff->titleM, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		}
	}
	if ($otherMinistryArray) {
		foreach ($otherMinistryArray as $otherMinistry) {
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($otherMinistry->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($otherMinistry->image2, ['class' => 'img-circle'])) . '</div>'; 
				echo '<div class="title">';
					echo Html::a($otherMinistry->org_name, [ProfileController::$profilePageArray[$otherMinistry->type], 'city' => $otherMinistry->url_city, 'name' => $otherMinistry->url_name, 'id' => $otherMinistry->id]);
					echo Html::tag('span', '<br>Ministry of ' . $profile->org_name, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		}
	}
	if ($programArray) {
		foreach ($programArray as $program) {
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($program->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($program->image2, ['class' => 'img-circle'])) . '</div>'; 
				echo '<div class="title">';
					echo Html::a($program->org_name, [ProfileController::$profilePageArray[$program->type], 'city' => $program->url_city, 'name' => $program->url_name, 'id' => $program->id]);
					echo Html::tag('span', '<br>Program of ' . $profile->org_name, ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		}
	}
	if ($fArray) {
		foreach ($fArray as $f) {
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($f->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($f->image2, ['class' => 'img-circle'])) . '</div>'; 
				echo '<div class="title">';
					echo Html::a($f->org_name, [ProfileController::$profilePageArray[$f->type], 'city' => $f->url_city, 'name' => $f->url_name, 'id' => $f->id]);
					echo Html::tag('span', '<br>Fellowship', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		}
	}
	if ($aArray) {
		foreach ($aArray as $a) {
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($a->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($a->image2, ['class' => 'img-circle'])) . '</div>'; 
				echo '<div class="title">';
					echo Html::a($a->org_name, [ProfileController::$profilePageArray[$a->type], 'city' => $a->url_city, 'name' => $a->url_name, 'id' => $a->id]);
					echo Html::tag('span', '<br>Association', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		}
	}
	if ($memberArray) {
		foreach ($memberArray as $member) {
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($member['usr_image']) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($member['usr_image'], ['class' => 'img-circle'])) . '</div>'; 
				echo '<div class="title">';
					echo $member->screen_name;
					echo Html::tag('span', '<br>Church member', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		}
	}
	if (empty($pastor) && empty($staffArray) && empty($otherMinistryArray) && empty($programArray) && empty($fArray) && empty($aArray) && empty($memberArray)) {
		echo '<em>No connections found.</em>';
	}
	?>

</div>