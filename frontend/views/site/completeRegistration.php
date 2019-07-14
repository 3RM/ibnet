<?php

use common\widgets\Alert;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */

$this->title = 'Complete Registration';
?>
<?= Alert::widget() ?>

<div class="profile-create">

	<div class="container registration-complete">
	    <h2>Almost There! We sent a registration link to your email. <br><span class="small" style="color:white; font-size:.6em;">Look for an email from "<?= Yii::$app->params['email.admin'] ?>" with the subject line "<?= Yii::$app->params['email.systemSubject'] ?>".  <br>Be sure to check your spam folder.</span></h2>

	    <?= HTML::a('Got it!', 'site/login', ['class' => 'btn-link']) ?>
	</div>

</div>