<?php
use yii\helpers\Html; use common\models\Utility;  

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>

<?php foreach ($notification->children as $reply) { ?>

	<table cellpadding="0" cellspacing="0" style="margin:20px auto 40px auto; width: 100%; max-width:800px; border-bottom-width:2px; border-style:solid; border-color:#eee; border-left:none; border-top:none; border-right:none;">
		<tr>
			<td>
				<p style="color:gray">
					<img src="https://ibnet.org/images/mail/replied.png" style="margin-right:5px; position:relative; top:-4px;">
					<?= Html::a($reply->user->fullName, 'mailto:' . $reply->user->email, ['style' => 'color:gray; text-decoration:underline; text-decoration-style:dashed;']) ?> 
						replied on <?= Yii::$app->formatter->asDateTime($reply->created_at, 'php:F j, Y H:i T') ?>:
				</p>
			</td>
		</tr>	
		<tr>
			<td>
				<p style=""><?= $reply->message ?></p>
			</td>
		</tr>
	</table>

<?php } ?>

<table cellpadding="0" cellspacing="0" style="width:100%; max-width:600px; margin:0 auto 0 auto;">
	<tr>
		<td>
			<p style="color:gray">Sent <?= Yii::$app->formatter->asDateTime($notification->created_at, 'php:F j, Y H:i T') ?></p>
		</td>
	</tr>	
</table>
<table cellpadding="0" cellspacing="0" style="border-collapse:separate; border-width:1px; border-style:solid; border-color:#cbcbcb; border-radius:4px; width:100%; max-width:600px; background-color:#fff; margin:0 auto 0 auto;">
	<tr>
		<td valign="middle" style="background-color:#003169; height:75px; width:65px">
			<img src="https://ibnet.org/images/mail/notification.png" style="width:40px; margin:10px 10px 10px 20px;">
		</td>
		<td valign="middel" style="background-color:#003169; color:#fff;">
			<h2 style="margin:0;"><?= $notification->group->name ?></h2>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p style="margin:10px 20px 20px 20px; color:gray;">
				<em><?= Html::a($notification->user->fullName, 'mailto:' . $notification->user->email, ['style' => 'color:gray; text-decoration:underline dashed;']) ?> wrote:</em>
			</p>
			<p style="margin:20px; font-size:1.6em;"><?= $notification->subject ?></p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p style="margin:0 20px 10px 20px;"><?= $notification->message ?></p>
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
					Note: For best results, please ensure your email client is set to format emails as html <em>and</em> text.
				</p>
				<p>      
					For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the <?= Html::a('forum', Yii::$app->params['url.forum']) ?> 
					to ask a question, leave feedback, or make new feature requests.
				</p>
				<p>
					This is a notification from the <?= $notification->group->name ?> group. You are receiving this message because you are a member of the group. 
				</p>
				<p>
					<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $notification->toEmail . '&token=' . $unsubTok) ?> 
					if you no longer wish to receive emails from IBNet. 
				</p>

			</div>
		</td>
	</tr>
</table>