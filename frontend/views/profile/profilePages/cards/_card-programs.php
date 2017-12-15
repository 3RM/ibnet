<?php
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Programs:</p>
			<div class="right">
				<ul>
				<?php foreach ($programArray as $program) {
					echo '<li>' . Html::a($program->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/' . ProfileController::$profilePageArray[$program->type], 'id' => $program->id, 'urlLoc' => $program->url_loc, 'name' => $program->url_name]) . '</li>';
				} ?>
				</ul>
			</div>
		</div>