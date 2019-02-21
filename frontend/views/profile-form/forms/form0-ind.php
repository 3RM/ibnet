<?php

use common\models\profile\Country;
use common\models\profile\Profile;
use kartik\markdown\MarkdownEditor;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Name & Description';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <?php if ($profile->type == Profile::TYPE_STAFF) { ?>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($profile, 'ind_first_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($profile, 'spouse_first_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($profile, 'ind_last_name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($profile, 'title')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-5">
                    <?= $form->field($profile, 'tagline')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9">
                    <?= $form->field($profile, 'description')->widget(MarkdownEditor::classname(), [
                        'height' => 200,
                        'options' => ['smarty' => true],
                        'showExport' => false,
                        'footerMessage' => false,
                        'toolbar' => $toolbar
                    ]); ?>
                </div>
            </div>

        <?php } else { ?>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($profile, 'ind_first_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($profile, 'ind_last_name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($profile, 'spouse_first_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($profile, 'tagline')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($profile, 'description')->widget(MarkdownEditor::classname(), [
                        'height' => 200,
                        'options' => ['smarty' => true],
                        'footerMessage' => '',
                        'showExport' => false,
                        'showPreview' => true,
                        'toolbar' => $toolbar,
                    ]); ?>
                </div>
            </div>

        <?php } ?>


        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>
        
        <?php ActiveForm::end(); ?>

    </div>
    
</div>
