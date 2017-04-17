<?php

use common\models\profile\Profile;
use common\models\profile\SchoolLevel;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Education Levels Offered';
?>

<div class="wrap profile-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($profile, 'select')->widget(Select2::classname(), [                 // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => ArrayHelper::map(SchoolLevel::find()->orderBy('id')->all(), 'id', 'school_level'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => [
                        'placeholder' => 'Select all that apply ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile, 'e' => $e]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>
