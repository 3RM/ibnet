<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'IBNet Search';
?>

<div class="search">
    <div class="header-container">
        <div class="header-img">
            <?= Html::img('@img.site/ibnet-large.png') ?>
        </div>
    </div>
</div>
<div class="clearsearch"></div> 
<div class="wrap">
    <div class="container">
        <div class="site-index">       
    
            <?= yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'showOnEmpty' => false,
                'emptyText' => '
                    <p>Your search - <b>' . $term . '</b> - did not return any results.</p>
                    <ul>
                        <li>Make sure all words are spelled correctly.</li>
                        <li>Try different keywords.</li>
                        <li>Try more general keywords.</li>
                    </ul>',
                'itemView' => '_searchResults',
                'itemOptions' => ['class' => 'item-bordered'],
                'layout' => '<div class="summary-row hidden-print clearfix">{summary}</div>{items}{pager}',
            ]); ?>
        </div>
    </div>
</div>