<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>

<?= $notification->user->fullName ?> wrote:

<?= $notification->subject ?>

<?= $notification->message ?>
    
For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the forum at <?= Yii::$app->params['url.forum'] ?> 
to ask a question, leave feedback, or make new feature requests.

Unsubscribe at <?= Yii::$app->params['url.unsubscribe'] . $notification->toEmail . '&token=' . $unsubTok ?> 
if you no longer wish to receive emails from IBNet.