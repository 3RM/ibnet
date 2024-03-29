<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var object $notification common\group\GroupNotification */
/* @var string $unsubTok unsubscribe email token */
?>

<?= $notification->user->fullName ?> wrote:

<?= $notification->subject ?>

<?= $notification->message ?>

To reply to the entire group, reply to this email. To email the sender, click the name above.

Note: For best results, please ensure your email client is set to format emails as html and text.
    
For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the forum at <?= Yii::$app->params['url.forum'] ?> 
to ask a question, leave feedback, or make new feature requests.

This is a notification of the <?= $notification->group->name ?> group. You are receiving this message because you are a member of the group. 

Unsubscribe at <?= Yii::$app->params['url.unsubscribe'] . $notification->toEmail . '&token=' . $unsubTok ?> 
if you no longer wish to receive emails from IBNet.