<?php
use yii\bootstrap\Html;
?>

	<div class="card title-list">
		<?= html::img('@img.flag/' . str_replace(' ', '-', $missionary->field) . '.png', ['alt' => 'Country flag']) ?>
		<div class="right">
			<ul>
				<li>Field: 
					<?php if ($profile->sub_type == 'Furlough Replacement') { ?>
						<?= $missionary->field == 'Furlough Replacement' ? 'Various' : $missionary->field ?>
					<?php } elseif ($profile->sub_type == 'Bible Translator') { ?>
						<?= $missionary->field ?>
					<?php } else { ?>
						<?= $missionary->field ?>
					<?php } ?>
				</li>
				<li>Status: <?= $missionary->status ?></li>
				<li>Sending Church: <?= $church ? HTML::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/church', 'id' => $church->id, 'urlLoc' => $church->url_loc, 'urlName' => $church->url_name]) : NULL ?></li>
				<li>Mission Agency: <?= $missionAgcyProfile ? HTML::a($missionAgcyProfile->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/mission-agency', 'id' => $missionAgcyProfile->id, 'urlLoc' => $missionAgcyProfile->url_loc, 'urlName' => $missionAgcyProfile->url_name]) : $missionAgcy->mission ?></li>
			</ul>
		</div>
	</div>