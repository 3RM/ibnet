<?php
use common\models\Utility;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

		<div class="update-card">

			<?= $update->title ? '<h1>' . $update->title . '</h1>' : NULL; ?>
			<p class="update-date"><?= Yii::$app->formatter->asDate($update->from_date, 'php:F j, Y'); ?></p>
			<?= $update->description ? '<p>' . $update->description . '</p>' : NULL ?>
			
			<div class="update-links">
				<?= $update->pdf ? '<span class="update-ind-icon">' . Html::a(Html::icon('download-alt'), Url::to($update->pdf), ['target' => '_blank']) . '</span>' : NULL ?>
				<span class="update-ind-icon"><?= Html::a(Html::icon('new-window'), $update->mailchimp_url, ['target' => '_blank']) ?></span>
			</div>

		</div>