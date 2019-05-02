<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
?>
	
	<?= $update->title ? '<h3>' . $update->title . '</h3>' : NULL; ?>
	<?= $update->description ? '<p>' . $update->description . '</p>' : NULL ?>
	
	<div class="update-links">
		<?= $update->pdf ? '<span class="update-ind-icon">' . Html::a(Html::icon('download-alt'), Url::to($update->pdf), ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '</span>' : NULL ?>
		<span class="update-ind-icon"><?= Html::a(Html::icon('new-window'), $update->mailchimp_url, ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?></span>
	</div>