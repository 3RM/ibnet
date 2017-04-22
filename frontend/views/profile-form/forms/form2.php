<?php

use common\models\profile\Profile;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Small Picture';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>
    
    <div class="container-form">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <p><?= HTML::icon('info-sign') ?> This picture is ideally suited for individuals, such a pastor or ministry leader, or a small ministry logo.</p>
        <p><?= HTML::icon('info-sign') ?> For best results, use an image with a minimum size of 370x280 pixels (Max 4MB).</p>

        <?php if ($profile->type == 'Church' && $imageLink != NULL) { ?>

            <?php if ($imageLink == $profile->image2) { ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-warning" role="alert">
                            This profile is currently using an image from Pastor <?= $profile->ind_last_name ?>'s profile.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <p><?= Html::submitButton(HTML::icon('remove') . ' Remove', [
                        'method' => 'POST',
                        'class' => 'btn btn-form btn-sm',
                        'name' => 'remove',
                    ]) ?></p>
                    <p><?= Html::img($imageLink) ?></p>
                </div>

            <?php } else { ?>

                <div class="row">
                    <div class="col-md-9">
                        <div class="alert alert-warning" role="alert">
                            <?= HTML::icon('question-sign') ?> Pastor <?= $profile->ind_last_name ?> has posted a 
                                picture to his profile.  Would you like to use it for this church profile?
                        </div>
                    </div>
                </div>

                <div class="row">
                    <p><?= Html::submitButton(HTML::icon('ok') . ' Use', [
                        'method' => 'POST',
                        'class' => 'btn btn-form btn-sm',
                        'name' => 'use',
                        'value' => $imageLink,
                    ]) ?></p>
                    <p><?= Html::img($imageLink) ?></p>
                </div>

                <br>
                <br>

                <div class="row">
                    <h4>Upload a new picture:</h4>
                    <div style="width:370px;">
                        <?= $form->field($profile, 'image2')->widget(\sadovojav\cutter\Cutter::className(), [
                            'cropperOptions' => [
                                'viewMode' => 1,
                                'aspectRatio' => 1.325,         // 318px x 240px (370 x 279 for larger displays)
                                'movable' => false,
                                'rotatable' => true,
                                'scalable' => false,
                                'zoomable' => false,
                                'zoomOnTouch' => false,
                                'zoomOnWheel' => false,
                            ],
                        ]) ?>
                    </div>
                </div>

            <?php } ?>

            <br>
            <br>

        <?php } else { ?>
            
            <div style="width:318px;">
                <?= $form->field($profile, 'image2')->widget(\sadovojav\cutter\Cutter::className(), [
                    'cropperOptions' => [
                        'viewMode' => 1,
                        'aspectRatio' => 1.325,
                        'movable' => false,
                        'rotatable' => true,
                        'scalable' => false,
                        'zoomable' => false,
                        'zoomOnTouch' => false,
                        'zoomOnWheel' => false,
                    ],
                ]); ?>
            </div>

        <?php } ?>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>    

        <?php ActiveForm::end(); ?>

    </div>

</div>
