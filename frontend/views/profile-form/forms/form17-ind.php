<?php

use common\models\profile\Fellowship;
use common\models\profile\Profile;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Fellowship';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <p>Do you belong to a fellowship?</p>
                <p><?= HTML::icon('info-sign') ?> If your fellowship or association is not listed, consider listing it!</p>

                <?= $form->field($profile, 'select')->widget(Select2::classname(), [                 // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => ArrayHelper::map(Fellowship::find()->all(), 'id', 'fellowship'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => [
                        'placeholder' => 'Select Fellowship(s) ...', 
                        'multiple' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
    
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($profile, 'name')->textInput(['maxlength' => true])->label('Or enter a new name here') ?>
            </div>
            <div class="col-md-2">    
                <?= $form->field($profile, 'acronym')->textInput(['maxlength' => true])->label('Acronym') ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>
    
        <?php ActiveForm::end(); ?>

    </div>

</div>
