<?php

use frontend\controllers\ProfileFormController;
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

	<?php 
		$missingArray = json_decode($missing);
		foreach ($missingArray as $key => $value) {
		if ($key != ProfileFormController::$form['mh']) {		// Don't check for missions housing
			echo '<p>' . Html::a('Review &#187', ['form-route', 'type' => $profile->type, 'fmNum' => $key-1, 'id' => $profile->id], ['class' => 'btn btn-sm btn-danger']) . ' ' . ProfileFormController::$formList[$key] . '</p>';
		}
	} ?>

</div>