<?php
use common\models\profile\Profile;	
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Distinctives:</p>
			<div class="right">
				<ul>
					<li>Bible: <?= $profile->bible ?></li>
					<li>Worship: <?= $profile->worship_style ?></li>
					<li>Government: <?= $profile->polity ?></li>
				</ul>
			</div>
		</div>