<?php

/* @var $this yii\web\View */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$this->title = 'User Accounts';
?>

<div class="site-index">

	<?= GridView::widget([
	    'dataProvider'=>$dataProvider,
	    'filterModel'=>$searchModel,
	    'columns'=>$gridColumns,
	    'headerRowOptions'=>['class'=>'kartik-sheet-style'],
	    'filterRowOptions'=>['class'=>'kartik-sheet-style'],
	    'toolbar'=> false,
	    'bordered'=>true,
	    'striped'=>false,
	    'condensed'=>true,
	    'responsive'=> true,
	    'hover'=>true,
	    'panel'=>[
	        'type'=>GridView::TYPE_WARNING,
	        'heading'=>'<i class="fa fa-address-card"></i>',
	    ],
	    'persistResize'=>true,
	]); ?>

</div>
