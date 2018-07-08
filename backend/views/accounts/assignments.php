<?php

/* @var $this yii\web\View */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$this->title = 'Assignments';
?>

<div class="site-index">

	<?= GridView::widget([
	    'dataProvider'=>$dataProvider,
	    'filterModel'=>$searchModel,
	    'columns'=>$gridColumns,
	    'headerRowOptions'=>['class'=>'kartik-sheet-style'],
	    'filterRowOptions'=>['class'=>'kartik-sheet-style'],
	    'bootstrap' => true,
	    'filterPosition' => GridView::FILTER_POS_BODY,
	    'toolbar'=> false,
	    'bordered'=>true,
	    'striped'=>false,
	    'condensed'=>true,
	    'responsive'=> true,
	    'hover'=>true,
	    'panel'=>[
	        'type'=>GridView::TYPE_WARNING,
	        'heading'=>'<i class="fa fa-sitemap"></i>',
	    ],
	    'persistResize'=>true,
	]); ?>

</div>
