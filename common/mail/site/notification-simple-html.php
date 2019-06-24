<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>

<table cellpadding="0" cellspacing="0" style="border-collapse:separate; border-width:1px; border-style:solid; border-color:#cbcbcb; border-radius:4px; width:100%; max-width:600px;">
	<?php if ($notification->title) { ?>
	<tr>
		<td>
			<p style="margin:20px 20px 0 20px; font-size:1.6em;"><?= $notification->title ?></p>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td>
			<p style="margin:20px 20px 10px 20px;"><?= $notification->message ?></p>
			<?= $notification->link ? '<p style="margin:0 20px 10px 20px;">' . $notification->link . '</p>' : NULL ?>
		</td>
	</tr>
	<tr>
		<td>
			<hr style="margin:10px 20px 0 20px; color:#eee;">
		</td>
	</tr>
	<tr>
		<td>
			<div style="margin:20px; font-size:0.9em; background-color:#f6f6f6; color:gray; padding:15px;">
				<p>      
					For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the <?= Html::a('forum', Yii::$app->params['url.forum']) ?> 
					to ask a question, leave feedback, or make new feature requests.
				</p>
				<p>
					<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $notification->email . '&token=' . $notification->token) ?> if you no longer wish to receive emails from IBNet. 
				</p>

			</div>
		</td>
	</tr>
</table>