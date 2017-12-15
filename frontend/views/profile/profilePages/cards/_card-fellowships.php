<?php
use common\models\profile\Profile;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Fellowship(s):</p>
			<div class="right">
				<ul>
					<?php foreach ($flwshipArray as $fellowship) {
						if ($flwshipLink = Profile::findOne($fellowship->profile_id)) {
							echo '<li>' . Html::a($fellowship->fellowship . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/fellowship', 'id' => $flwshipLink->id, 'urlLoc' => $flwshipLink->url_loc, 'name' => $flwshipLink->url_name], ['title' => $fellowship->fellowship_acronym]) . '</li>';
						} else {
							echo '<li>' . Html::tag('span', $fellowship->fellowship, ['title' => $fellowship->fellowship_acronym]) . '</li>';
						}
					} ?>
				</ul>
			</div>
		</div>