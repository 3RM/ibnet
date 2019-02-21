<?php
use common\models\profile\Profile;	
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Levels Offered:</p>
			<div class="right">
				<ul>
					<?php foreach ($schoolLevels as $level) {
						echo '<li>' . $level['school_level'] . '</li>';
					} ?>
				</ul>
			</div>
		</div>