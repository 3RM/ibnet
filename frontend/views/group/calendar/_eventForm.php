<?php

use kartik\color\ColorInput;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

if ($event->all_day) {$event->end -= 24*3600;}

?>

<div class="new-event-content-ajax">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">  
        <div class="col-md-9"> 
            <?= DateRangePicker::widget([
                'name' => 'dateRange',
                'value' => ($event->start && $event->end) ? 
                    date('Y-m-d h:i A', $event->start) . ' - ' . date('Y-m-d h:i A', $event->end) : 
                    date('Y-m-d h:i A', time()) . ' - ' . date('Y-m-d h:i A', time()),
                'convertFormat' => true,
                'pluginOptions' => [
                    'timePicker' => true,
                    'timePickerIncrement' => 15,
                    'locale' => ['format' => 'Y-m-d h:i A']
                ]            
            ]); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($event, 'all_day')->checkbox() ?>
        </div>
    </div>
    <div class="row">
            <div class="col-md-9">
                <?= $form->field($event, 'title')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <label class="control-label">Color</label>
                <?= ColorInput::widget([
                    'name' => 'color',
                    'id' => $event->color ? 'edit-color' : 'new-color',
                    'value' => $event->color ? $event->color : 'blue',
                    'showDefaultPalette' => true,
                    'options' => ['class' => 'hidden'],
                    'pluginOptions' => [
                        'showInput' => true,
                        'showInitial' => true,
                        'showPalette' => true,
                        'showPaletteOnly' => true,
                        'showSelectionPalette' => false,
                        'showAlpha' => false,
                        'allowEmpty' => false,
                        'preferredFormat' => 'name',
                        'palette' => [
                            [
                                "white", "black", "grey", "silver", "gold", "brown", 
                            ],
                            [
                                "red", "orange", "yellow", "indigo", "maroon", "pink"
                            ],
                            [
                                "blue", "green", "violet", "cyan", "magenta", "purple", 
                            ],
                        ]
                    ]
                ]); ?>
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($event, 'description')->textarea(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= Html::submitButton('Save', [
                'method' => 'POST',
                'class' => 'btn btn-primary longer',
            ]) ?>
        </div>
    </div>
    <?php $form = ActiveForm::end(); ?>
</div>