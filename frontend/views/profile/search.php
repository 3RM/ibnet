<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
Url::remember();
$this->title = 'IBNet Search';
?>

<div class="wrap search">
    <div class="header-container">
        <div class="header-img">
            <?= Html::img('@web/images/content/ibnet-large.png') ?>
            <div id="search-box" class="input-group">
                <?php $form = ActiveForm::begin(['id' => 'search-form']); ?>
                <?= $form->field($searchModel, 'term')->textInput([
                    'maxlength' => true, 
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                ])->label('') ?>
                <?= Html::submitButton('', [
                    'method' => 'POST',
                    'class' => 'btn btn-default search-icon',
                    'name' => 'search',
                ]) ?>
                <?php $form = ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<div class="clearsearch"></div> 
<div class="wrap">
    <div class="container">
        <div class="site-index">       
	
        	<?php if ($dataProvider->totalCount == 0) { ?>
                <p>Your search - <b><?= $searchModel->term ?></b> - did not return any results.</p>
                <ul>
                	<li>Make sure all words are spelled correctly.</li>
                	<li>Try different keywords.</li>
                	<li>Try more general keywords.</li>
                </ul>
            <?php } ?>
    
            <?php 
            echo yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'showOnEmpty' => false,
                'emptyText' => '',
                'itemView' => '_searchResults',
                'itemOptions' => ['class' => 'item-bordered'],
                'layout' => '<div class="summary-row hidden-print clearfix">{summary}</div>{items}{pager}',
            ]); ?>
        </div>
    </div>
</div>