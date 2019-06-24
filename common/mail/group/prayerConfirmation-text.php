<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var object $prayer common\group\GroupPrayer */
/* @var object $message string */
/* @var string $unsubTok unsubscribe email token */
?>

Prayer Request: <?= $prayer->request ?>

<?= $message ?>

For assistance, contact <?= Yii::$app->params['email.admin'] ?> or visit the forum (<?= Yii::$app->params['url.forum'] ?>) 
to ask a question, leave feedback, or make new feature requests.

Unsubscribe if you no longer wish to receive emails from IBNet: <?= Yii::$app->params['url.unsubscribe'] . $prayer->groupUser->email . '&token=' . $unsubTok ?>