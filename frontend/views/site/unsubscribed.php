<?php


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Subscriptions';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= '<i class="fas fa-at"></i> ' . $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container">

    <div class="row">
        <div class="col-md-6">
            <p><b>Email</b>: <?= $sub->email ?></p>
        </div>
    </div>
    
    <p>
        <?= $sub->unsubscribe ?
            'You have successfully unsubscribed from all IBNet emails.' :
            'You\'re subscription has been updated.' ?>
    </p>

</div>