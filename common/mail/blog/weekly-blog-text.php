<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<?= $title ?>

<?= $message ?>

<?php 
    foreach ($posts as $i=>$post) {
        echo $posts[$i]['post_title'] . ' by ' . $posts[$i]['author_name'] . $posts[$i]['post_url'] . PHP_EOL; 
    }
?>

To change your email subscriptions, visit your account settings at <?= Yii::$app->params['url.loginFirst']) . urlencode(Url::to(['site/#account-settings'])) ?>.

Unsubscribe at <?= Yii::$app->params['url.unsubscribe'] . $email . '&token' . $unsubTok) ?>.

For assistance contact <?= Yii::$app->params['email.admin'] ?>.