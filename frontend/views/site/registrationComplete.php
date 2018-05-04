<?php

use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Profile */

$this->title = 'Registration Complete';
?>
<?= Alert::widget() ?>

<div class="profile-create">

	<div class="container registration-complete">
	    <h2>Success! Thank You for Registering.</h2>

	    <?= HTML::a('My Account', Url::to(['/site/dashboard']), ['class' => 'btn-link']) ?>
	</div>

</div>