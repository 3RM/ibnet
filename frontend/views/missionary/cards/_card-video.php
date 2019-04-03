<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

		<div class="update-card">

			<?= $update->title ? '<h1>' . $update->title . '</h1>' : NULL; ?>
			<p class="update-date"><?= Yii::$app->formatter->asDate($update->from_date, 'php:F j, Y'); ?></p>
			<?= $update->description ? '<p>' . $update->description . '</p>' : NULL ?>

			<div class="update-video-wrapper">
				<?= $update->drive_url ?
					'<iframe src="' . $update->drive_url . '" width="100%" height="405"></iframe>' :
					$update->getVideo(true); 
				?>
			</div>
		</div>