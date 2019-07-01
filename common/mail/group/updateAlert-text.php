<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $update common\models\missionary\Update */
/* @var $gid integer Group id */
?>

Missionary Update

<?= $update->title ?>

<?= isset($update->description) ? $update->description : NULL; ?>
			
See all missionary updates here: <?= Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/prayer', 'id' => $gid])) ?>.

For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the the forum (<?= Yii::$app->params['url.forum'] ?>) 
to ask a question, leave feedback, or make new feature requests.

You are subscribed to receive missionary updates.  Visit the group missionary updates page (
<?= Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/update', 'id' => $gid])) ?>) to change your subscription. 

Unsubscribe at <?= Yii::$app->params['url.unsubscribe'] . $update->user->email . '&token=' . $token ?> 
if you no longer wish to receive emails from IBNet.