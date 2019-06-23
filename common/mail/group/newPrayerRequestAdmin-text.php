<?php
/* @var $this yii\web\View */
/* @var $prayer object common\group\GroupPrayer */
/* @var $unsubTok string Unsubscribe email token */
?>

Prayer Request: <?= $prayer->request ?>

Your request has been added to the <?= $prayer->group->name ?> prayer list. Save this email 
and reply with either Update or Answer in the subject line to add an update to your request 
or to mark it as answered.  Add your update or answer description in your reply.

Follow the link below to visit the prayer list:

<?= Yii::$app->params['url.loginFirst'] . 'group/prayer/'  . $prayer->group->id ?>

For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the forum (<?= Yii::$app->params['url.forum'] ?>) 
to ask a question, leave feedback, or make new feature requests.

Unsubscribe if you no longer wish to receive emails from IBNet: <?= Yii::$app->params['url.unsubscribe'] . $prayer->groupUser->email . '&token=' . $unsubTok ?>