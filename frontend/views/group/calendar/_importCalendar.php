<?php

use kartik\color\ColorInput;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

?>

<div>
    <p>
        Import any calendar in the iCal format.
    </p>
    <p>
        To import a Google calendar, go to your Google calendar settings and scroll down to "Integrate Calendar."  Copy the "Secret address in iCal format" url and paste it here.
    </p>
    <p>
        To add holidays for any country, for example, use a url from here: <?= Html::a('https://www.officeholidays.com/ics/', 'https://www.officeholidays.com/ics/', ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?>
    </p>

    <?php $form = ActiveForm::begin(['action' => '/group/remove-ical']); ?>
    <?php if (is_array($urlList)) {
        echo '<ul>';
            foreach ($urlList as $url) {
                echo '<li><span ';
                echo $url->error_on_import ? 'class="url"' : NULL;
                echo '>' . $url->url . '</span>';
                echo $url->error_on_import ? ' <span style="color:red">' . Html::icon('warning-sign') . '</span> ' : NULL;
                echo Html::submitButton(Html::icon('remove'), [
                'method' => 'POST',
                'class' => 'link-btn',
                'name' => 'remove',
                'value' => $url->id,
            ]) . '</li>';
            }
        echo '</ul>';
    } ?>
    <?php $form = ActiveForm::end(); ?>

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">  
        <div class="col-md-9">
            <?= $form->field($ical, 'url')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3 space-below">
            <label class="control-label">Color</label>
                <?= ColorInput::widget([
                    'name' => 'color',
                    'id' => 'import-color',
                    'value' => $ical->color ? $ical->color : '#ff4f00',
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
            <?= Html::submitButton('Save', [
                'method' => 'POST',
                'class' => 'btn btn-primary longer',
            ]) ?>
        </div>
    </div>
    <?php $form = ActiveForm::end(); ?>
</div>