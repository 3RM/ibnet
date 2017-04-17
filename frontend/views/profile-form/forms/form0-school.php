<?php

use common\models\profile\Profile;
use kartik\markdown\MarkdownEditor;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'School Name and Description';
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
                        'options' => ['placeholder' => 'Select a school ...'],
                        'pluginOptions' => ['allowClear' => true],
                    ]); ?>
                    <?= $profile->select == NULL ? NULL : Html::activeHiddenInput($profile, 'name'); ?>
                </div>
            </div>
        <?php } ?>

        <?php if ($profile->select == NULL) { ?>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($profile, 'name')->textInput(['maxlength' => true])->label($profile->org_name == NULL ? 'Or enter a School Name here' : 'School Name') ?>
                    <?= $profile->org_name == NULL ? NULL : Html::activeHiddenInput($profile, 'select'); ?>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($profile, 'tagline')->textInput(['maxlength' => true]) ?>
                <?= $form->field($profile, 'description')->widget(
                    MarkdownEditor::classname(), [
                        'height' => 200, 
                        'options' => ['smarty' => true],
                        'toolbar' => $toolbar
                    ]
                ); ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile, 'e' => $e]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>