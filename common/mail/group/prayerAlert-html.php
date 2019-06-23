<?php
use common\models\group\PrayerAlertQueue;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $prayer common\models\group\Prayer */
/* @var $status string prayer status (new|update|answer) */
?>

<table cellpadding="0" cellspacing="0" style="border-collapse:separate; border-width:1px; border-style:solid; border-color:#cbcbcb; border-radius:4px; max-width:600px; background-color:#fff; margin:0 auto 0 auto;">
	<tr>
		<td valign="middle" style="background-color:#003169; height:75px; width:65px">
			<img src="https://ibnet.org/images/mail/prayer.png" style="width:35px; margin:10px 10px 10px 20px;">
		</td>
		<td valign="middle" style="background-color:#003169; color:#fff;">
			<h2 style="margin:0;">Prayer Alert</h2>
		</td>
	</tr>	
	<tr>
		<td colspan="2">
			<p style="color:gray; margin:20px;">Requested by <?= Html::a($prayer->fullName, 'mailto:' . $prayer->email, ['style' => 'color:gray; text-decoration:underline; text-decoration-style:dashed;']) ?></p>
			<?php if ($status == PrayerAlertQueue::STATUS_NEW) { ?>
				<p style="margin:20px; font-size:1.6em;">New: <?= $prayer->request ?></p>
			<?php } elseif ($status == PrayerAlertQueue::STATUS_UPDATE) { ?>
				<p style="margin:20px; font-size:1.6em;">Update: <?= $prayer->request ?></p>
			<?php } elseif ($status == PrayerAlertQueue::STATUS_ANSWER) { ?>
				<p style="margin:20px; font-size:1.6em;">Answered: <?= $prayer->request ?></p>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php if($status == PrayerAlertQueue::STATUS_ANSWER) { ?>
				<p style="margin:0 20px 10px 20px;"><?= $prayer->answer_description ?></p>
				<p style="margin:40px 20px 20px 20px; color:gray; font-size:1.2em"><em>Original Request:</em></p>
			<?php } ?>
			<?= isset($prayer->description) ? '<p style="margin:0 20px 10px 20px;">' . $prayer->description . '</p>' : NULL; ?>
			<?php if (isset($updates)) { ?>
				<table cellpadding="0" cellspacing="0" style="width: 93.33333%; max-width:560px; background-color:#fff; margin:20px;">
					<?php foreach ($updates as $update) { ?>
						<tr>
							<td style="margin-left:20px; padding: 10px 0;">
								<p style="color:gray; margin-bottom:5px;">
									<?= Html::img('https://ibnet.org/images/mail/sticky-note.png', ['style' => 'margin-right:10px;']) .  Yii::$app->formatter->asDateTime($update->created_at, 'php:F j, Y H:i T') ?>
								</p>
								<p><?= $update->update ?></p>
							</td>
						</tr>	
					<?php } ?>
				</table>
			<?php } ?>
			<p style="margin:0 20px 10px 20px;">
				Visit the <?= Html::a('prayer list here', Yii::$app->params['url.loginFirst'] . 'group/prayer/'  . $prayer->group->id) ?>.
			</p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr style="margin:10px 20px 0 20px; color:#eee;">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div style="margin:20px; font-size:0.9em; background-color:#f6f6f6; color:gray; padding:15px;">
				<p>      
					For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the <?= Html::a('forum', Yii::$app->params['url.forum']) ?> 
					to ask a question, leave feedback, or make new feature requests.
				</p>
				<p>
					<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $prayer->groupUser->email . '&token=' . $unsubTok) ?> 
					if you no longer wish to receive emails from IBNet. 
				</p>

			</div>
		</td>
	</tr>
</table>