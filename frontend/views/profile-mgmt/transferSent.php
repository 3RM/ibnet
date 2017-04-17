<?php

use common\widgets\Alert;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Request Sent';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= Html::icon('transfer') . ' ' . $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container">

    <h1>Goodbye old friend...</h1>

    <p>&nbsp;</p>
    <p>Your request has been sent.  You will receive an email when the transfer is complete.</p>

</div>