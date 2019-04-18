<?php

/* @var $this yii\web\View */

use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

$this->title = 'Clear Yii Cache';
?>

<div class="site-index">

	<?php $form = ActiveForm::begin(); ?>
        <div class="row top-margin">
            <div class="col-md-8">
                <?= Html::submitButton('Flush /backend Cache', [
                    'method' => 'POST',
                    'class' => 'btn btn-main',
                    'name' => 'backendClear'
                ]) ?>
            </div>
        </div>
        <div class="row top-margin">
            <div class="col-md-8">
            	<?= Html::submitButton('Flush /frontend Cache', [
                    'method' => 'POST',
                    'class' => 'btn btn-main',
                    'name' => 'frontendClear'
                ]) ?>
            </div>
        </div>
    <?php $form = ActiveForm::end(); ?>

</div>
