<?php

use common\models\profile\Profile;
use kartik\checkbox\CheckboxX;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Missionary Housing';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-8">
                <h4>Housing</h4>
                <?= $form->field($missHousing, 'description')->textArea(['maxlength' => true, 'rows' => 2]) ?>
                <?= $form->field($missHousing, 'contact')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row top-margin">
            <div class="col-md-8">
                <h4>Motorhome and Trailer</h4>
                <?= $form->field($missHousing, 'trailer')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($missHousing, 'water')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($missHousing, 'electric')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($missHousing, 'sewage')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
            </div>
        </div>
    
        <br>

        <h4>Determine who can have access</h4>
        <p>
            Missionary housing is only visible to missionaries who have registered with IBNet.
            It will not be accessible through the public directory.  You may further limit which missionaries will have
            access.  Choose one or more acceptable distincitves from the list below.  Only missionaries with the matching 
            distinctives that you choose will be able to view the listing.
        </p>
        <div class="row">
            <div class="col-md-8">
                <?= $form->field($missHousing, 'select')->widget(Select2::classname(), [                 // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => $list,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => [
                        'placeholder' => 'Select criteria to match against missionary profiles ...',
                        ['Bible', 'Worship Style', 'Church Government'],
                        'multiple' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ])->label(''); ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>