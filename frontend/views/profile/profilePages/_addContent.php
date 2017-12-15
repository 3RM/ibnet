<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

	<div class="add-content center">
       	<?= Html::a('Show Comments', Url::current(['p' => 'comments', '#' => 'p']), ['class' => 'btn btn-primary']); ?>
       	<?= Html::a('Show Connections', Url::current(['p' => 'connections', '#' => 'p']), ['class' => 'btn btn-primary']); ?>
       	<?= Html::a('Show History', Url::current(['p' => 'history', '#' => 'p']), ['class' => 'btn btn-primary']); ?>
    </div>