<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
$this->title = 'Profile Expired';
?>
<?= Alert::widget() ?>

<div class="site-index profile-page">
    <div class="container">
    	<div class="row top-margin-100">
    		<div class="col-md-3">
        		<h1><?= $this->title ?>:</h1>
        	</div>
        </div>
        <div class="row">
			<div class="col-md-8">
				<h3>The page you are looking for has recently expired. :-(</h3>
				<br>
				<p><?= Html::a(Html::icon('search') . ' Return to Search', ['site/index']) ?></p>
			</div>
		</div>
	</div>
</div>