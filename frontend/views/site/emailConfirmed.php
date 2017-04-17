<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Email Confirmed';
?>
<?= Alert::widget() ?>

<div class="site-reset-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Your email is confirmed.</p>

    <?= HTML::a('OK', Yii::$app->homeUrl); ?>

</div>
