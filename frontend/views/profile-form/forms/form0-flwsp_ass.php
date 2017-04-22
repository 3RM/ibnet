<?php

use common\models\profile\Association;
use common\models\profile\Fellowship;
use common\models\profile\Profile;
use kartik\markdown\MarkdownEditor;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Name and Description';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(); ?>
          
             <?php if ($profile->org_name == NULL || $toggle) { ?>

            <div class="row">
                <div class="col-md-8">        
                    <?= $form->field($profile, 'select')->widget(Select2::classname(), [
                        'data' => $list,
                        'language' => 'en',
                        'theme' => 'krajee',
                        'options' => ['placeholder' => 'Select ...'],
                        'pluginOptions' => ['allowClear' => true],
                    ]); ?>
                    <?= $profile->select == NULL ? NULL : Html::activeHiddenInput($profile, 'name'); ?>
                    <?= $profile->select == NULL ? NULL : Html::activeHiddenInput($profile, 'acronym'); ?>
                </div>
            </div>

        <?php } ?>

        <?php if ($profile->select == NULL) { ?>

            <div class="row">
                <div class="col-md-5">
                    <?= $form->field($profile, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $profile->org_name == NULL ? NULL : Html::activeHiddenInput($profile, 'select'); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($profile, 'acronym')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

        <?php } ?>

        <div class="row">
            <div class="col-md-5">
                <?= $form->field($profile, 'tagline')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($profile, 'flwsp_ass_level')->widget(Select2::classname(), [
                    'data' => [
                        'Regional' => 'Regional', 
                        'State' => 'State', 
                        'National' => 'National',
                        'International' => 'International',
                    ],
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Select Level ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($profile, 'description')->widget(
                    MarkdownEditor::classname(), [
                        'height' => 200, 
                        'options' => ['smarty' => true],
                        'toolbar' => $toolbar
                    ]
                ); ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>
    
</div>