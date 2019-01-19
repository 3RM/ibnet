<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use common\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Already Registered';
?>
<?= Alert::widget() ?>

<div class="profile-create">
    <div class="container registration-complete">
        <h2>Hey, you're already registered! ;-)</h2>
    </div>
</div>