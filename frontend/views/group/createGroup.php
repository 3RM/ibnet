<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Create a Group';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container-form">

    <p>
    	Create a group and invite your friends and ministry partners to join. Groups have privately shared access to one or more features that
    	are designed to aid you in ministry: Prayer list, Calendar, Discussion forum, Missionary updates, Email notifications, and more.
    </p>
    <p>
        Once your new group is activated, two email addresses will be created within 48 hours.  You will be notified when they are ready.
        Don't worry about maintaining these as they will run in the background.  Just take note of the addresses as you will will need them to
        send out group notificaitons and add new prayer requests via email.
    </p>
    
    <?= Html::a('Continue', ['group-information'], ['class' => 'btn btn-primary']) ?>

</div>





