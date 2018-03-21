<?php

use common\models\profile\Bible;
use common\models\profile\Profile;
use common\models\profile\Polity;
use common\models\profile\WorshipStyle;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Distinctives';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <p><?= HTML::icon('info-sign') ?> We do not attempt to define the following terms, but offer them as options which may approximate various positions.</p>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($profile, 'bible')->widget(Select2::classname(), [                 // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => ArrayHelper::map(Bible::find()->orderBy('id')->all(), 'bible', 'bible'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Select ...',],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Bible'); ?>
                <?= $form->field($profile, 'worship_style')->widget(Select2::classname(), [                 
                    'data' => ArrayHelper::map(WorshipStyle::find()->orderBy('id')->all(), 'style', 'style'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Select ...',],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Worship'); ?>
                <?= $form->field($profile, 'polity')->widget(Select2::classname(), [                 
                    'data' => ArrayHelper::map(Polity::find()->orderBy('id')->all(), 'polity', 'polity'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Select ...',],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Church Government'); ?>
            </div>
        </div>

       <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>
