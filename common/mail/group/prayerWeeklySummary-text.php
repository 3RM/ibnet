<?php
use common\models\group\GroupAlertQueue;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $prayer common\models\group\Prayer */
/* @var $status string prayer status (new|update|answer) */
?>

Weekly Summary

<?php foreach ($prayers as $i => $p) { ?>
	<?php if ($p->status == GroupAlertQueue::PRAYER_STATUS_NEW) { ?>
		New: <?= $p->prayer->request ?>
	<?php } elseif ($p->status == GroupAlertQueue::PRAYER_STATUS_UPDATE) { ?>
		Update: <?= $p->prayer->request ?>
	<?php } elseif ($p->status == GroupAlertQueue::PRAYER_STATUS_ANSWER) { ?>
		Answered: <?= $p->prayer->request ?>
	<?php } ?>
	<?= Yii::$app->formatter->asDateTime($p->prayer->created_at, 'php:F j, Y H:i T') ?>, <?= $p->prayer->fullName ?>
	
	<?php if($p->status == GroupAlertQueue::PRAYER_STATUS_ANSWER) { ?>
		<?= $p->prayer->answer_description ?>
	
		Original Request:
	<?php } ?>

	<?= !empty($p->prayer->description) ? $p->prayer->description : NULL; ?>

	<?php if (isset($p->prayer->prayerUpdates)) { ?>
		<?php foreach ($p->prayer->prayerUpdates as $update) { ?>

			<?= Yii::$app->formatter->asDateTime($update->created_at, 'php:F j, Y H:i T') ?>	

			<?= $update->update ?>

		<?php } ?>
	<?php } ?>
<?php } ?>
	
Visit the prayer list here: <?= Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/prayer', 'id' => $gid])) ?>.

For assistance, contact <?= Yii::$app->params['email.admin'] ?>.  
Visit the <?= Html::a('forum', Yii::$app->params['url.forum']) ?> to ask a question, leave feedback, or make new feature requests.

You are subscribed to receive weekly prayer list summaries.  Visit the group prayer list
(<?= Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/prayer', 'id' => $gid])) ?>) to change your subscription. 

<?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $to . '&token=' . $token) ?> if you no longer wish to receive emails from IBNet. 