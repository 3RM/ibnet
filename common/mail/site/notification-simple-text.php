<?php
/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<?= $notification->title ?? NULL ?>

<?= $notification->message ?>

<?= $notification->link ?? NULL ?>
    
Unsubscribe at <?= Yii::$app->params['url.unsubscribe'] . $notification->to . '&token=' . $notification->token ?> you no longer wish to receive emails from IBNet. 

For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the <?= Yii::$app->params['url.forum'] ?> 
to ask a question, leave feedback, or make new feature requests.