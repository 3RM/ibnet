<?php

/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\bootstrap\Html;

$this->title = 'Prayer Table';
?>

<div class="site-index">

	<?= GridView::widget([
	    'dataProvider'=>$dataProvider,
	    'filterModel'=>$searchModel,
	    'columns'=>$gridColumns,
	    'options' => [ 'style' => 'table-layout:fixed;' ],
	]); ?>

</div>