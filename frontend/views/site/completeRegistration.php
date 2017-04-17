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
	    <h2>Almost There! We sent a registration link to your email.</h2>

	    <?= HTML::a('Roger that', ['site/index', 'class' => 'btn-link']) ?>
	</div>

</div>