<?php
use common\models\profile\Profile;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Association(s):</p>
			<div class="right">
				<ul>
					<?php foreach ($assArray as $association) {
						if ($assLink = Profile::findOne($association->profile_id)) {
							echo '<li>' . Html::a($association->association . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/fellowship', 'id' => $assLink->id, 'urlLoc' => $assLink->url_loc, 'name' => $assLink->url_name], ['title' => $association->association_acronym]) . '</li>';
						} else {
							echo '<li>' . Html::tag('span', $association->association, ['title' => $association->association_acronym]) . '</li>';
						}
					} ?>
				</ul>
			</div>
		</div>