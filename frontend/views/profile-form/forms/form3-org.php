<?php

use common\models\profile\Country;
use common\models\profile\Profile;
use kartik\checkbox\CheckboxX;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = $title;
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">
        
        <?php $form = ActiveForm::begin(); ?>

       <p><?= HTML::icon('info-sign') ?> Physical Address or Mailing Address is required.</p>

        <h3>Physical Address</h3>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($profile, 'org_address1')->textInput(['maxlength' => true]) ?>
                <?= $form->field($profile, 'org_address2')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($profile, 'org_city')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($profile, 'org_st_prov_reg')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($profile, 'org_zip')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($profile, 'org_country')->widget(Select2::classname(), [
                    'data' => $list,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => ['placeholder' => 'Select a country ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-11">
                <p><?php if ($profile->show_map == Profile::MAP_CHURCH) {
                        echo Html::icon('map-marker') . ' Your profile currently shows a map of your home church address.';
                    } elseif ($profile->show_map == Profile::MAP_MINISTRY) {
                        echo Html::icon('map-marker') . ' Your profile currently shows a map of your ministry address.';
                    } elseif ($profile->show_map == Profile::MAP_CHURCH_PLANT) {
                        echo Html::icon('map-marker') . ' Your profile currently shows a map of your church plant address.';
                    } elseif ($profile->show_map == NULL) {
                        echo Html::icon('map-marker') . ' Your profile is not currently showing a map.';
                    } ?>
                    <?= $form->field($profile, 'map')->widget(CheckboxX::classname(), [
                        'initInputType' => CheckboxX::INPUT_CHECKBOX,
                        'autoLabel' => true,
                        'pluginOptions'=>[
                            'theme' => 'krajee-flatblue',
                            'enclosedLabel' => true,
                            'threeState'=>false, 
                        ]
                    ])->label(false) ?>
                </p>
            </div>
        </div>

        <h3>Mailing Address (if different)</h3>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($profile, 'org_po_address1')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($profile, 'org_po_address2')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($profile, 'org_po_box')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($profile, 'org_po_city')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($profile, 'org_po_st_prov_reg')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($profile, 'org_po_zip')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($profile, 'org_po_country')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Country::find()->all(), 'printable_name', 'printable_name'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => ['placeholder' => 'Select a country ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile, 'e' => $e]) ?>
    
        <?php ActiveForm::end(); ?>
        
    </div>

</div>
