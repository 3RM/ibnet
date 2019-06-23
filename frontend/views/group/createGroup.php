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
    
    <?= Html::a('Continue', ['group-information'], ['class' => 'btn btn-primary']) ?>

</div>





