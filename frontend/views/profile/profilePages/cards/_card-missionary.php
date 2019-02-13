<?php
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<?= html::img('@web/images/content/flag/' . str_replace(' ', '-', $missionary->field) . '.png', ['alt' => 'Country flag']) ?>
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
					<li>Sending Church: <?= $church ? HTML::a($church->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/church', 'id' => $church->id, 'urlLoc' => $church->url_loc, 'name' => $church->url_name]) : NULL ?></li>
					<li>Mission Agency: <?= empty($missionLink) ? $mission->mission : HTML::a($missionLink->org_name . '&nbsp' . Html::icon('link', ['class' => 'internal-link']), ['profile/mission-agency', 'id' => $missionLink->id, 'urlLoc' => $missionLink->url_loc, 'name' => $missionLink->url_name]) ?></li>
				</ul>
			</div>
		</div>