<?php

/* @var $this yii\web\View */

use yii\widgets\DetailView;

$this->title = 'View User';
?>

<div class="site-index">

    <?= DetailView::widget([
    	'model' => $model,
    	'attributes' => $attributes,
	]) ?>
</div>
