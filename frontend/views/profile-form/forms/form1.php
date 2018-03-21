<?php

use common\models\profile\Profile;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Large Picture';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <div class="row">
            <p><?= HTML::icon('info-sign') ?> This picture is ideally suited for a ministry picture or large logo.</p>
            <p><?= HTML::icon('info-sign') ?> For best results, use an image with a minimum size of 800x300 pixels (Max 4MB).</p>

            <h2>Upload:</h2>

            <div class="forms-image1-widget">
                <?= $form->field($profile, 'image1')->widget(\sadovojav\cutter\Cutter::className(), [
                    'cropperOptions' => [
                        'viewMode' => 1,    
                        'aspectRatio' => 2.666667,           // 800px x 300px
                        'movable' => false,
                        'rotatable' => false,
                        'scalable' => false,
                        'zoomable' => false,
                        'zoomOnTouch' => false,
                        'zoomOnWheel' => false,
                    ]
                ]); ?>
            </div>

        </div>
        <div class="row">
            <h2>Or choose an image below:</h2>

            <p><?= HTML::submitbutton(Html::img('@web/images/content/banner1.jpg'), [
                    'method' => 'POST',
                    'class' => 'btn forms-btn-banner',
                    'name' => 'banner1',
                ]) ?></p>
            <p><?= HTML::submitbutton(Html::img('@web/images/content/banner2.jpg'), [
                    'method' => 'POST',
                    'class' => 'btn forms-btn-banner',
                    'name' => 'banner2',
                ]) ?></p>
            <p><?= HTML::submitbutton(Html::img('@web/images/content/banner3.jpg'), [
                    'method' => 'POST',
                    'class' => 'btn forms-btn-banner',
                    'name' => 'banner3',
                ]) ?></p>
            <p><?= HTML::submitbutton(Html::img('@web/images/content/banner4.jpg'), [
                    'method' => 'POST',
                    'class' => 'btn forms-btn-banner',
                    'name' => 'banner4',
                ]) ?></p>
            <p><?= HTML::submitbutton(Html::img('@web/images/content/banner5.jpg'), [
                    'method' => 'POST',
                    'class' => 'btn forms-btn-banner',
                    'name' => 'banner5',
                ]) ?></p>
            <p><?= HTML::submitbutton(Html::img('@web/images/content/banner6.jpg'), [
                    'method' => 'POST',
                    'class' => 'btn forms-btn-banner',
                    'name' => 'banner6',
                ]) ?></p>
        </div>


        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>
        
        <?php ActiveForm::end(); ?>

    </div>

</div>
