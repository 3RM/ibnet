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
		if ($church) {
			echo '<div class="connection">';
				echo '<div class="image">' . (empty($church->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($church->image2, ['class' => 'img-circle'])) . '</div>'; 
				echo '<div class="title">';
					echo Html::a($church->org_name, [ProfileController::$profilePageArray[$church->type], 'city' => $church->url_city, 'name' => $church->url_name, 'id' => $church->id]);
					echo Html::tag('span', '<br>Home Church', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		}
		if ($flwshipArray) {
			foreach ($flwshipArray as $fellowship) {
				if ($flwshipLink = Profile::findOne($fellowship->profile_id)) {
					echo '<div class="connection">';
						echo '<div class="image">' . (empty($flwshipLink->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($flwshipLink->image2, ['class' => 'img-circle'])) . '</div>'; 
						echo '<div class="title">';
							echo Html::a($flwshipLink->org_name, [ProfileController::$profilePageArray[$flwshipLink->type], 'city' => $flwshipLink->url_city, 'name' => $flwshipLink->url_name, 'id' => $flwshipLink->id]);
							echo Html::tag('span', '<br>Member of', ['class' => 'subTitle']);
						echo '</div>';
					echo '</div>';
				}
			}
		}
		if ($sChurchArray) {					// Church staff
			foreach ($sChurchArray as $sChurch) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($sChurch->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($sChurch->image2, ['class' => 'img-circle'])) . '</div>'; 
					echo '<div class="title">';
						echo Html::a($sChurch->getFormattedNames()->formattedNames, [ProfileController::$profilePageArray[$sChurch->type], 'city' => $sChurch->url_city, 'name' => $sChurch->url_name, 'id' => $sChurch->id]);
						echo Html::tag('span', '<br>Ministry Partner ' . (empty($church->org_name) ? NULL : ' at ' . $church->org_name), ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if ($otherMinistryArray) {				// Other ministries
			foreach ($otherMinistryArray as $otherMinistry) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($otherMinistry->image2) ? Html::img('@web/images/Profile_Image_4.jpg', ['class' => 'img-circle']): Html::img($otherMinistry->image2, ['class' => 'img-circle'])) . '</div>'; 
					echo '<div class="title">';
						echo Html::a($otherMinistry->org_name, [ProfileController::$profilePageArray[$otherMinistry->type], 'city' => $otherMinistry->url_city, 'name' => $otherMinistry->url_name, 'id' => $otherMinistry->id]);
						echo Html::tag('span', '<br>Serving as ' . $otherMinistry->titleM, ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if ($sOtherArray) {						// Other ministry staff partners
			foreach ($sOtherArray as $sOther) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($sOther->image2) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($sOther->image2, ['class' => 'img-circle'])) . '</div>'; 
					echo '<div class="title">';
						echo Html::a($sOther->getFormattedNames()->formattedNames, [ProfileController::$profilePageArray[$sOther->type], 'city' => $sOther->url_city, 'name' => $sOther->url_name, 'id' => $sOther->id]);
						echo Html::tag('span', '<br>Ministry Partner at ' . $sOther->titleM, ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if ($memberArray) {
			foreach ($memberArray as $member) {
				echo '<div class="connection">';
					echo '<div class="image">' . (empty($member['usr_image']) ? Html::img('@web/images/user.png', ['class' => 'img-circle']): Html::img($member['usr_image'], ['class' => 'img-circle'])) . '</div>'; 
					echo '<div class="title">';
						echo $member['screen_name'];
						echo Html::tag('span', '<br>Fellow church member', ['class' => 'subTitle']);
					echo '</div>';
				echo '</div>';
			}
		}
		if (empty($church) && empty($flwshipArray) && empty($sChurchArray) && empty($otherMinistryArray) && empty($sOtherArray) && empty($memberArray)) {
			echo '<em>No connections found.</em>';
		}
		?>
	</div>

</div>