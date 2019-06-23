<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<div id="container">

    <p id="title"><?= $title ?></p>

    <div id="content">
        <p><?= $message ?></p>
        <ul>
            <?php 
                foreach ($posts as $i=>$post) {
                    echo '<li style="list-style:none"><em>' . Html::a($posts[$i]['post_title'] . '</em> by ' . $posts[$i]['author_name'], $posts[$i]['post_url'], ['target' => '_blank', 'rel' => 'noopener noreferrer', 'style' => 'text-decoration:none']) . '</li>'; 
                }
            ?>
        </ul>
    </div>  

    <div id="footer">
        <hr>
        <p>To change your email subscriptions, visit your <?= Html::a('Account Settings', Yii::$app->params['url.loginFirst']) . 'settings/#account-settings' ?>.</p>

        <p><?= Html::a('Unsubscribe', Yii::$app->params['url.unsubscribe'] . $email . '&token' . $unsubTok) ?>.</p>

        <p>For assistance contact <?= Yii::$app->params['email.admin'] ?>.</p>
    </div>

</div>