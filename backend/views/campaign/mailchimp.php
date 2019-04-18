<?php

/* @var $this yii\web\View */

use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

$this->title = 'Manage Mailchimp';
?>

<div class="site-index">

    <p>Sync the "New Feature" Mailchimp mailing list with current user email preferences.  Run this prior to sending a new feature campaign.</p>

	<?php $form = ActiveForm::begin(); ?>
        <div class="row top-margin">
            <div class="col-md-8">
                <?= Html::submitButton('Sync', [
                    'method' => 'POST',
                    'class' => 'btn btn-main',
                    'name' => 'feature'
                ]) ?>
            </div>
        </div>
    <?php $form = ActiveForm::end(); ?>

</div>
