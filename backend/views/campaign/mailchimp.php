<?php

/* @var $this yii\web\View */

use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

$this->title = 'Manage Mailchimp';
?>

<div class="site-index">

	<?php $form = ActiveForm::begin(); ?>
        <div class="row top-margin">
            <div class="col-md-8">
                <?= Html::submitButton('Refresh Feature Mailing List', [
                    'method' => 'POST',
                    'class' => 'btn btn-primary',
                    'name' => 'feature'
                ]) ?>
            </div>
        </div>
        <?php $form = ActiveForm::end(); ?>

</div>
