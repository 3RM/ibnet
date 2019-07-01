<?php
use common\models\group\GroupAlertQueue;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

Prayer Alert

Requested by <?= $prayer->fullName ?>a

<?php if ($status == 'new') { ?>
	New: <?= $prayer->request ?>
<?php } elseif ($status == 'update') { ?>
	Update: <?= $prayer->request ?>
<?php } else { ?>
	Answered: <?= $prayer->request ?>
<?php } ?>

<?php if($status == GroupAlertQueue::PRAYER_STATUS_ANSWER) { ?>
	<?= $prayer->answer_description ?></p>
	
	Original Request:
<?php } ?>

<?= $prayer->description ?? NULL; ?>

<?php if (isset($updates)) { ?>
	<?php foreach ($updates as $update) { ?>
		<?= 'Update ' . Yii::$app->formatter->asDateTime($update->created_at, 'php:F j, Y H:i T') ?>
		<?= $update->update ?>
	<?php } ?>
<?php } ?>
			
Visit the prayer list: <?= Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/prayer', 'id' => $prayer->group->id])) ?>
			
For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the <?= Html::a('forum', Yii::$app->params['url.forum']) ?> 
to ask a question, leave feedback, or make new feature requests.

You are subscribed to receive weekly prayer list summaries.  Visit the group prayer list to change your subscription:
<?= Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/prayer', 'id' => $gid]))) ?>.
				
<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $prayer->groupUser->email . '&token=' . $unsubTok) ?> 
if you no longer wish to receive emails from IBNet. 