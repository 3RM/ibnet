<?php

use common\widgets\Alert;
use kartik\checkbox\CheckboxX;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Profile */

$this->title = 'Registration Complete';
?>
<?= Alert::widget() ?>

<div class="profile-create">
	<div class="container registration-complete">
	    <h2>Success! Thank You for Registering.</h2>
	    <?= HTML::a('My Account', Url::to(['/site/settings']), ['class' => 'btn-link']) ?>
	</div>
</div>