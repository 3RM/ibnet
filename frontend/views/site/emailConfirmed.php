<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$confirmed ?
	$this->title = 'Email Confirmed' :
	$this->title = 'Email Confirmation Failed';
?>
<?= Alert::widget() ?>

<div class="site-reset-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($confirmed) { ?>
    
    	<p>Your email is confirmed.</p>
    	<div class="top-margin-40"><?= HTML::a('Login', Url::to(['login']), ['class' => 'btn btn-primary']) ?></div>

    <?php } else { ?>

    	<p>Your email token is expired or otherwise invalid.</p>
    	<p>Go to the login page and attempt to login.  You will see an error message with a link to resend your confirmation email.</p>
    	<div class="top-margin-40"><?= HTML::a('Login', Url::to(['login']), ['class' => 'btn btn-primary']) ?></div>

    <?php } ?>

</div>
