<?php

/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\bootstrap\Html;

$this->title = 'Missionary Update Table';
?>

<div class="site-index">

	<?= GridView::widget([
	    'dataProvider' => $dataProvider,
	    'filterModel' => $searchModel,
	    'columns' => $gridColumns,
	]); ?>

</div>