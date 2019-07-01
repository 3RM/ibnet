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

This is a notification from the <?= $notification->group->name ?> group. You are receiving this message because you are a member of the group. 

Unsubscribe at <?= Yii::$app->params['url.unsubscribe'] . $notification->toEmail . '&token=' . $unsubTok ?> 
if you no longer wish to receive emails from IBNet.