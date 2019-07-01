<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $nid network id */
/* @var $name network name */
/* @var $request prayer request */
?>


Invitation to Join IBNet Group <?= $group->name ?>

<?= $user->fullName ?> cordially invites you to join the IBNet group <?= $group->name ?>.  Joining a group gives you access 
to many ministry tools that are shared among group members.  If you are not registered with IBNet, 
Visit <?= Yii::$app->params['url.register'] ?> to register.  Also, be sure to identify your home church 
from the IBNet directory on your account settings page (<?= Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['site/settings'])) ?>) in order to 
unlock the group feature along with other great features.

<?= $extMessage ?? NULL ?>

Visit <?= Yii::$app->params['url.loginFirst'] . urlencode(Url::to(['group/invite-join', 'token' => $token])) ?> to Join!

This email was sent to you by a user of IBNet. 
Unsubscribe at <?= Yii::$app->params['url.frontend'] . 'unsubscribe?email=' . $email . '&unsubTok=' . $unsubTok ?> 
if you no longer wish to receive emails from IBNet. 

For assistance contact <?= Yii::$app->params['email.admin'] ?>.