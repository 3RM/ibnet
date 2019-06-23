<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $nid network id */
/* @var $name network name */
/* @var $request prayer request */

?>

<table cellpadding="0" cellspacing="0" style="border-collapse:separate; border-width:1px; border-style:solid; border-color:#cbcbcb; border-radius:4px; max-width:600px; background-color:#fff; margin:0 auto 0 auto;">
	<tr>
		<td valign="middle" style="background-color:#003169; height:75px; width:65px">
			<img src="https://ibnet.org/images/mail/cluster.png" style="width:40px; margin:10px 10px 10px 20px;">
		</td>
		<td valign="middel" style="background-color:#003169; color:#fff;">
			<h2 style="margin:0;">Invitation to Join</h2>
		</td>
	</tr>	
	<tr>
		<td colspan="2">
			<p style="margin:20px; font-size:1.6em;">Invitation to Join IBNet Group <?= $group->name ?></p>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<p style="margin:0 20px 10px 20px;">
				<?= $user->fullName ?> cordially invites you to join the IBNet group <?= $group->name ?>.  Joining a group gives you access 
				to many ministry tools that are shared among group members.  If you are not registered with IBNet, 
				<?= Html::a('click here', Yii::$app->params['url.register']) ?> to register.  Also, be sure to identify your home church 
				from the IBNet directory on your <?= Html::a('account settings', Yii::$app->params['url.loginFirst'] . 'settings') ?> page in order to 
				unlock the group feature along with other great features.
			</p>
			<?= isset($extMessage) ? '<p style="margin-left:20px;">' . $extMessage . '</p>' : NULL ?>
			<h3 style="margin-left:20px;"><?= Html::a('Click Here to Join!', Yii::$app->params['url.loginFirst'] . 'group/invite-join?token=' . $token) ?></h3>
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
					<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $email . '&token=' . $unsubTok) ?> 
					if you no longer wish to receive emails from IBNet. 
				</p>

			</div>
		</td>
	</tr>
</table>