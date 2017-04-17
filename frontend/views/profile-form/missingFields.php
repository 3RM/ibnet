<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'Review Information';
?>

<div class="site-index">

	<h1><?= Html::icon('alert') . ' ' . $this->title ?></h1>

	<p>Oops! It appears that you haven't reviewed all of the data entry forms for your profile.  You may have missed some required fields.</p>

	<br />
	<?= Html::a('Review and Update &#187', ['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $profile->id], ['class' => 'btn btn-danger']) ?>

	<p>&nbsp;</p>

</div>