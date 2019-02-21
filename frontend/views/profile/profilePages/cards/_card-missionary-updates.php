<?php
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Update(s):</p>
			<div class="right">
				<ul>
					<?php foreach ($updates as $update) {
						if ($update->mailchimp_url) {
							print('<li>' . Html::a($update->title . ' (' . Yii::$app->formatter->asDate($update->from_date, 'php:F j, Y') . ') ' . Html::icon('new-window', ['class' => 'internal-link']), $update->mailchimp_url, ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '</li>');
						} elseif ($update->pdf) {
							print('<li>' . Html::a($update->title . ' (' . Yii::$app->formatter->asDate($update->from_date, 'php:F j, Y') . ') ' . Html::icon('new-window', ['class' => 'internal-link']), [$update->pdf], ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '</li>');
						} elseif ($update->vimeo_url || $update->youtube_url) {
							$update->vimeo_url ? 
								print('<li>' . Html::a($update->title . ' (' . Yii::$app->formatter->asDate($update->from_date, 'php:F j, Y') . ') ' . Html::icon('new-window', ['class' => 'internal-link']), $update->vimeo_url, ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '</li>') :
								print('<li>' . Html::a($update->title . ' (' . Yii::$app->formatter->asDate($update->from_date, 'php:F j, Y') . ') ' . Html::icon('new-window', ['class' => 'internal-link']), $update->youtube_url, ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '</li>');
						}
					} ?>
				</ul>
			</div>
		</div>