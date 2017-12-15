<?php
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>School(s):</p>
			<div class="right">
				<ul>
					<?php foreach ($schoolsAttended as $school) {
						if ($s = $school->linkedProfile) {
							print('<li>' . Html::a($s->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['school', 'id' => $s->id, 'urlLoc' => $s->url_loc, 'name' => $s->url_name]) . '</li>');
						} else {
							print('<li>' . $school->school . '</li>');
						}
					} ?>
				</ul>
			</div>
		</div>