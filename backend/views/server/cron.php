<?php

/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\bootstrap\Html;

$this->title = 'Cron Jobs';
?>

<div class="site-index">

	<?= GridView::widget([
	    'dataProvider'=>$dataProvider,
	    // 'filterModel'=>$searchModel,
	    'columns'=>$gridColumns,
	]); ?>

</div>
<script src="https://use.fontawesome.com/1db1e4efa2.js"></script>
