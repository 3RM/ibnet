<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $update common\models\missionary\Update */
/* @var $gid integer Group id */
?>

<table cellpadding="0" cellspacing="0" style="border-collapse:separate; border-width:1px; border-style:solid; border-color:#cbcbcb; border-radius:4px; max-width:600px; background-color:#fff; margin:0 auto 0 auto;">
	<tr>
		<td valign="middle" style="background-color:#003169; height:75px; width:65px">
			<img src="https://ibnet.org/images/mail/update.png" style="width:35px; margin:10px 10px 10px 20px;">
		</td>
		<td valign="middle" style="background-color:#003169; color:#fff;">
			<h2 style="margin:0;">Missionary Update</h2>
		</td>
	</tr>	
	<tr>
		<td colspan="2" style="text-align:center;">
			<?= html::img('https://ibnet.org/images/flag/' . str_replace(' ', '-', $update->missionary->field) . '.png', ['style' => 'margin-top:20px;']) ?>
			<p style="margin:20px; color:gray; font-size: 1.4em;"><?= $update->missionary->profile->coupleName . ' &middot ' . $update->missionary->field ?></p> 
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p style="margin:20px; font-size:1.6em;"><?= $update->title ?></p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p style="font-size:1.2em; margin:0 0 0 20px">
			<?php if (!empty($update->youtube_url) || !empty($update->vimeo_url) || !empty($update->drive_url)) { ?>
				<img src="https://ibnet.org/images/mail/video.png" style="margin:0 5px 0 0;">
				<?= Html::a('Watch Video', Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['missionary/watch', 'gid' => $gid, 'uid' => $update->id]))) ?>
			<?php } elseif (!empty($update->pdf)) { ?>
				<img src="https://ibnet.org/images/mail/pdf.png" style="margin:0 5px 0 0;">
				<?= Html::a('Open PDF', 'https://ibnet.org' . $update->pdf) ?>	
			<?php } elseif (!empty($update->mailchimp_url)) { ?>
				<img src="https://ibnet.org/images/mail/message.png" style="margin:0 5px 0 0;">
				<?= Html::a('Open Update', 'https://ibnet.org' . $update->mailchimp_url) ?>
			<?php } ?>
			</p>
		</td>
	</tr>
	<?php if (isset($update->description)) { ?>
	<tr>
		<td colspan="2">
			<p style="margin:20px 20px 10px 20px;"><?= $update->description ?></p>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="2">
			<p style="margin:20px 20px 10px 20px;">
				See all <?= Html::a('missionary updates here', Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/update', 'id' => $gid]))) ?>.
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
					You are subscribed to receive missionary updates.  Visit the group 
					<?= Html::a('missionary updates page', Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/update', 'id' => $gid]))) ?> to change your subscription. 
				</p>
				<p>
					<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $update->user->email . '&token=' . $token) ?> 
					if you no longer wish to receive emails from IBNet. 
				</p>

			</div>
		</td>
	</tr>
</table>