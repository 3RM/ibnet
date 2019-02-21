<?php
use common\models\profile\Profile;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Fellowship(s):</p>
			<div class="right">
				<ul>
					<?php foreach ($fellowships as $flwship) {
						echo $flwship->linkedProfile ?
							'<li>' . Html::a($flwship->name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/fellowship', 'id' => $flwship->linkedProfile->id, 'urlLoc' => $flwship->linkedProfile->url_loc, 'urlName' => $flwship->linkedProfile->url_name], ['title' => $flwship->acronym]) . '</li>' :
							'<li>' . Html::tag('span', $flwship->name, ['title' => $flwship->acronym]) . '</li>';
					} ?>
				</ul>
			</div>
		</div>