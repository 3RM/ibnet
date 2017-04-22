<?php

use common\models\profile\Profile;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Church Service Times';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(); ?>

        <p><?= HTML::icon('info-sign') ?> A service time will not be saved unless day, hour, minute, and description fields are completed.</p>
        <div class="row">
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'day_1')->widget(Select2::classname(), [
                    'data' => $days,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Day...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Service Time 1') ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'hour_1')->widget(Select2::classname(), [
                    'data' => $hours,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Hour...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'minutes_1')->widget(Select2::classname(), [
                    'data' => $minutes,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Minute...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($serviceTime, 'description_1')->textInput(['maxlength' => true, 'placeholder' => 'Description, e.g. "Morning Worship"'])->label('') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'day_2')->widget(Select2::classname(), [
                    'data' => $days,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Day...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Service Time 2') ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'hour_2')->widget(Select2::classname(), [
                    'data' => $hours,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Hour...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'minutes_2')->widget(Select2::classname(), [
                    'data' => $minutes,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Minute...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($serviceTime, 'description_2')->textInput(['maxlength' => true, 'placeholder' => 'Description, e.g. "Morning Worship"'])->label('') ?>        
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'day_3')->widget(Select2::classname(), [
                    'data' => $days,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Day...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Service Time 3') ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'hour_3')->widget(Select2::classname(), [
                    'data' => $hours,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Hour...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'minutes_3')->widget(Select2::classname(), [
                    'data' => $minutes,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Minute...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($serviceTime, 'description_3')->textInput(['maxlength' => true, 'placeholder' => 'Description, e.g. "Morning Worship"'])->label('') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'day_4')->widget(Select2::classname(), [
                    'data' => $days,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Day...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Service Time 4') ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'hour_4')->widget(Select2::classname(), [
                    'data' => $hours,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Hour...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'minutes_4')->widget(Select2::classname(), [
                    'data' => $minutes,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Minute...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($serviceTime, 'description_4')->textInput(['maxlength' => true, 'placeholder' => 'Description, e.g. "Morning Worship"'])->label('') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'day_5')->widget(Select2::classname(), [
                    'data' => $days,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Day...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Service Time 5') ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'hour_5')->widget(Select2::classname(), [
                    'data' => $hours,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Hour...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'minutes_5')->widget(Select2::classname(), [
                    'data' => $minutes,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Minute...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($serviceTime, 'description_5')->textInput(['maxlength' => true, 'placeholder' => 'Description, e.g. "Morning Worship"'])->label('') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'day_6')->widget(Select2::classname(), [
                    'data' => $days,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Day...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Service Time 6') ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'hour_6')->widget(Select2::classname(), [
                    'data' => $hours,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Hour...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($serviceTime, 'minutes_6')->widget(Select2::classname(), [
                    'data' => $minutes,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Minute...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($serviceTime, 'description_6')->textInput(['maxlength' => true, 'placeholder' => 'Description, e.g. "Morning Worship"'])->label('') ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>
