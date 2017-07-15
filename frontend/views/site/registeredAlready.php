<?php

use common\widgets\Alert;
use yii\bootstrap\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Profile */

$this->title = 'Invalid Token';
?>

<div class="container top-margin">

	<h1><?= Html::icon('alert') ?> Invalid Token</h1>

	<div class="row top-margin">
		<div class="col-md-8">
			<p>Your token is invalid.  This could be caused by:</p>
			<ul>
				<li><b>Registration is already complete:</b>  Go to the <?= Html::a('login', ['login']) ?> page and attempt to login.  If your token has already been used to verify your registration, you will be successfully logged in.</li>
				<li><b>Expired token:</b> Go the <?= Html::a('login', ['login']) ?> page and attempt to login. If your token is expired, you will see a link to resend your verificaiton email with a new token.</li>
				<li><b>Invalid token:</b> The token you supplied is not a valid token.</li>
			</ul>
		</div>
	</div>

</div>