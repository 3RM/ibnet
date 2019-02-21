<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['profile-mgmt/transfer-complete', 'id' => $profile->id, 'token' => $profile->transfer_token]);
?>

<div class="">
	<h3><?= $title ?></h3>
    <p><?= $msg ?></p>

    <?= $link ? '<p>' . Html::a(Html::encode($resetLink), $resetLink) . '</p>' : NULL ?>
</div>
