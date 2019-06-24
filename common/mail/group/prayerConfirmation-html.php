<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var object $prayer common\group\GroupPrayer */
/* @var object $message string */
/* @var string $unsubTok unsubscribe email token */
?>

<table cellpadding="0" cellspacing="0" style="border-collapse:separate; border-width:1px; border-style:solid; border-color:#cbcbcb; border-radius:4px; border-top-width: 8px; border-top-color:#0066db; max-width:600px; background-color:#fff;">
	<tr>
		<td>
			<p style="margin:20px; color:gray;">Prayer Request: <?= $prayer->request ?></p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p style="margin:0 20px 10px 20px;"><?= $message ?></p>
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
					<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $prayer->groupUser->email . '&token=' . $unsubTok) ?> 
					if you no longer wish to receive emails from IBNet. 
				</p>

			</div>
		</td>
	</tr>
</table>