<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>

<table cellpadding="0" cellspacing="0" style="border-collapse:separate; border-width:1px; border-style:solid; border-color:#cbcbcb; border-radius:4px; width:100%; max-width:600px; background-color:#fff; margin:0 auto 0 auto;">
	<tr>
		<td valign="middle" style="background-color:<?= $notification->headerColor ?>; height:75px; width:65px; ">
			<a href="<?= Yii::$app->params['url.frontend'] ?>"><img src="<?= $notification->headerImage ?>" style="height:55px; margin:10px 20px 10px 20px;"></a>
		</td>
		<td valign="middle" style="background-color:<?= $notification->headerColor ?>; color:#fff;">
			<?= $notification->headerText ? '<h2 style="margin:0;">' . $notification->headerText . '</h2>' : NULL; ?>
		</td>
	</tr>
	
	<?php if ($notification->title) { ?>
	<tr>
		<td colspan="2">
			<p style="margin:20px; font-size:1.6em;"><?= $notification->title ?></p>
		</td>
	</tr>
	<?php } ?>

	<tr>
		<td colspan="2">
			<?= $notification->title ? 
				'<p style="margin:0 20px 10px 20px;">' . $notification->message . '</p>' :
				'<p style="margin:20px 20px 10px 20px;">' . $notification->message . '</p>';
			<?= $notification->extMessage ? '<p style="margin:0 20px 10px 20px;">' . $notification->extMessage . '</p>' : NULL ?>
			<?= $notification->link ? '<p style="margin:0 20px 10px 20px;">' . $notification->link . '</p>' : NULL ?>
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
					<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $notification->to . '&token=' . $notification->token) ?> if you no longer wish to receive emails from IBNet. 
				</p>

			</div>
		</td>
	</tr>
</table>