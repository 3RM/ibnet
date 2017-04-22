<?php

use common\models\profile\MissionAgcy;
use common\models\profile\Profile;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Missions';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <p>The information on this page is viewable only by missionaries registered with ibnet.org and will not be available on the public directory.</p>
    
        <h3>Approved Mission Agencies</h3>
        <p><?= HTML::icon('info-sign') ?> The appearance of any organization in this list does not imply approval or endorsement as an Independent Baptist organization by IBNet.</p>

        <div class="row">
            <div class="col-md-8">    
                <?= $form->field($profile, 'select')->widget(Select2::classname(), [                 // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => $list,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => [
                        'placeholder' => 'Select Approved Mission Agencies ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ])->label(''); ?>
            </div>
        </div>

        <br>
        <br>

        <h3>Mission Policy and/or Mission Questionnaire</h3>
        <p><?= HTML::icon('info-sign') ?> Upload one PDF file (max size 6MB).  If you have two documents, combine them into one PDF file.</p>
        <div class="row">
            <div class="col-md-8">
                <?= $form->field($profile, 'packet')->fileInput()->label('') ?>

                <?php if (isset($profile->packet)) { ?>
                    <p>You have one uploaded PDF file.
                    <?= Html::a(HTML::icon('download-alt'), ['profile-form/download', 'path' => $profile->packet], ['class' => 'btn btn-form btn-sm', 'target' => '_blank', 'alt' => 'Download']) ?>
                    <?= Html::submitButton(HTML::icon('remove'), [
                        'method' => 'POST',
                        'class' => 'btn btn-form btn-sm',
                        'name' => 'remove',
                        'alt' => 'Remove',
                    ]) ?></p>
                <?php } ?> 
            </div>
        </div>
    
        <br>
        <br>

        <h3>Mission Housing</h3>
        <p><?= HTML::icon('info-sign') ?> If you select yes, on the next page you will be able to add a description and contact information, and specify who will have access to the housing.</p>
        <div class="row">
            <div class="col-md-8">
                <?= $form->field($profile, 'missHousing')->radioList([ 'N' => 'No', 'Y' => 'Yes', ]) ?>
            </div>
        </div>
            
        <br>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>