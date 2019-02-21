<?php
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;

	foreach ($churchMembers as $member) {
		echo '<div class="connection-container">';
			echo '<div class="connection">';
				echo $member->usr_image ? Html::img($member->usr_image) : Html::img('@img.site/user.png'); 
				echo '<div class="title">';
					echo $member->fullName;
					echo Html::tag('span', '<br>Church Member', ['class' => 'subTitle']);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
?>