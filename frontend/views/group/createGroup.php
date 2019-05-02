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

    <p>Information about groups here</p>
    
    <?= Html::a('Continue', ['group-information'], ['class' => 'btn btn-primary']) ?>

</div>





