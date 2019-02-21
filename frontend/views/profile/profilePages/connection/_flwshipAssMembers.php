<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($members->profiles as $member) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo (empty($member->image2) ? Html::img('@img.profile/profile-logo.png'): Html::img($member->image2)); 
				echo '<div class="title">';
					echo Html::a((($member->category == Profile::CATEGORY_IND) ? $member->mainName : $member->org_name) . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), [ProfileController::$profilePageArray[$member->type], 'urlLoc' => $member->url_loc, 'urlName' => $member->url_name, 'id' => $member->id]);
					echo Html::tag('span', '<br>' . (($member->category == Profile::CATEGORY_IND) ? 'Member' : 'Member Church'), ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
?>