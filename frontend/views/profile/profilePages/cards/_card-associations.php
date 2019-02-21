<?php
use common\models\profile\Profile;
use yii\bootstrap\Html;
?>

	<div class="card title-list">
		<p>Association(s):</p>
		<div class="right">
			<ul>
				<?php foreach ($associations as $ass) {
					echo $ass->linkedProfile ?
						'<li>' . Html::a($ass->name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/association', 'id' => $ass->linkedProfile->id, 'urlLoc' => $ass->linkedProfile->url_loc, 'urlName' => $ass->linkedProfile->url_name], ['title' => $ass->acronym]) . '</li>' :
						'<li>' . Html::tag('span', $ass->name, ['title' => $ass->acronym]) . '</li>';
				} ?>
			</ul>
		</div>
	</div>