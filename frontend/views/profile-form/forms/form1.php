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

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <div class="row">
            <p><?= HTML::icon('info-sign') ?> This picture is ideally suited for a ministry picture or large logo.</p>
            <p><?= HTML::icon('info-sign') ?> For best results, use an image with a minimum size of 1200x315 pixels (Max 4MB).</p>

            <div style="width:1200px">
                <?= $form->field($profile, 'image1')->widget(\sadovojav\cutter\Cutter::className(), [
                    'cropperOptions' => [
                        'viewMode' => 1,    
                        'aspectRatio' => 3.84615,           // 1000px x 260px
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

        <?= $this->render('_profileFormFooter', ['profile' => $profile, 'e' => $e]) ?>
        
        <?php ActiveForm::end(); ?>

    </div>

</div>
