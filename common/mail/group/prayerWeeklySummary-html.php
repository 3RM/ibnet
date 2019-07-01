<?php
use common\models\group\GroupAlertQueue;
use yii\helpers\Html;
use yii\helpers\Url;

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
			<h2 style="margin:0;">Weekly Summary</h2>
		</td>
	</tr>
	<?php foreach ($prayers as $i => $p) { ?>
		<tr>
			<td colspan="2">
				<p style="margin:20px 20px 0 20px; font-size:1.2em;">
					<?php if ($p->status == GroupAlertQueue::PRAYER_STATUS_NEW) { ?>
						<b>New: <?= $p->prayer->request ?></b>
					<?php } elseif ($p->status == GroupAlertQueue::PRAYER_STATUS_UPDATE) { ?>
						<b>Update: <?= $p->prayer->request ?></b>
					<?php } elseif ($p->status == GroupAlertQueue::PRAYER_STATUS_ANSWER) { ?>
						<b>Answered: <?= $p->prayer->request ?></b>
					<?php } ?>
				</p>
				<p style="color:gray; margin:5px 20px 10px 20px;">
					<?= Yii::$app->formatter->asDateTime($p->prayer->created_at, 'php:F j, Y H:i T') ?>, 
					<?= Html::a($p->prayer->fullName, 'mailto:' . $p->prayer->email, ['style' => 'color:gray; text-decoration:underline; text-decoration-style:dashed;']) ?>
				</p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php if($p->status == GroupAlertQueue::PRAYER_STATUS_ANSWER) { ?>
					<p style="margin:0 20px 10px 20px;"><?= $p->prayer->answer_description ?></p>
					<p style="margin:40px 20px 20px 20px; color:gray; font-size:1.2em"><em>Original Request:</em></p>
				<?php } ?>
				<?= !empty($p->prayer->description) ? '<p style="margin:0 20px 10px 20px;">' . $p->prayer->description . '</p>' : NULL; ?>
				<?php if (isset($p->prayer->prayerUpdates)) { ?>
					<table cellpadding="0" cellspacing="0" style="width: 90%; max-width:560px; background-color:#fff; margin:0px 20px 20px 50px;">
						<?php foreach ($p->prayer->prayerUpdates as $update) { ?>
							<tr>
								<td style="margin-left:20px; padding: 10px 0;">
									<p style="color:gray; margin-bottom:5px;">
										<?= Html::img('https://ibnet.org/images/mail/sticky-note.png', ['style' => 'margin-right:10px;']) .  
											Yii::$app->formatter->asDateTime($update->created_at, 'php:F j, Y H:i T') ?>
									</p>
									<p><?= $update->update ?></p>
								</td>
							</tr>	
						<?php } ?>
					</table>
				<?php } ?>
				<?php if ($i+1 < count($prayers)) { ?>
					<hr style="margin:10px 20px 0 20px; color:#eee;">
				<?php } ?>
			</td>
		</tr>
	<?php } ?>
	<tr>
		<p style="margin:0 20px 10px 20px;">
			Visit the <?= Html::a('prayer list here', Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/prayer', 'id' => $gid]))) ?>.
		</p>
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
					For assistance, contact <?= Yii::$app->params['email.admin'] ?>.  
					Visit the <?= Html::a('forum', Yii::$app->params['url.forum']) ?> to ask a question, leave feedback, or make new feature requests.
				</p>
				<p>
					You are subscribed to receive weekly prayer list summaries.  Visit the group 
					<?= Html::a('prayer list', Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/prayer', 'id' => $gid]))) ?> to change your subscription. 
				</p>
				<p>
					<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $to . '&token=' . $token) ?> 
					if you no longer wish to receive emails from IBNet. 
				</p>

			</div>
		</td>
	</tr>
</table>