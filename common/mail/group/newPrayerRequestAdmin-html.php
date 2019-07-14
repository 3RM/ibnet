<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $prayer object common\group\GroupPrayer */
/* @var $unsubTok string unsubscribe email token */
?>

<table cellpadding="0" cellspacing="0" style="border-collapse:separate; border-width:1px; border-style:solid; border-color:#cbcbcb; border-radius:4px; max-width:600px; background-color:#fff; margin:0 auto 0 auto;">
	<tr>
		<td valign="middle" style="background-color:#0066db; height:75px; width:65px">
			<img src="https://ibnet.org/images/mail/prayer.png" style="width:35px; margin:10px 10px 10px 20px;">
		</td>
		<td valign="middle" style="background-color:#0066db; color:#fff;">
			<h2 style="margin:0;">Your Prayer Request</h2>
		</td>
	</tr>	
	<tr>
		<td colspan="2">
			<p style="margin:20px; font-size:1.6em;">Request: <?= $prayer->request ?></p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p style="margin:0 20px 10px 20px;">
				Your request has been added to the <?= $prayer->group->name ?> prayer list. Save this email and simply reply to add an 
				update to your request. Include the word "Answer" in the subject line to mark the prayer as answered.  Add a description 
				in your reply (required).
			</p>
			<p style="margin:0 20px 10px 20px;">
				Visit the <?= Html::a('prayer list here', Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/prayer', 'id' => $prayer->group->id]))) ?>.
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