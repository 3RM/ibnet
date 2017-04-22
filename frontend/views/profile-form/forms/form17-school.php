<?php

use common\models\profile\Accreditation;
use common\models\profile\Profile;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'School Accreditation or Association';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(); ?>

        <p><?= Html::icon('info-sign') ?> Is your accreditation or association missing?  Please <?= Html::a('let us know', ['/site/contact'], ['target' => '_blank']) ?> so we can add it.</p>

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($profile, 'select')->widget(Select2::classname(), [                 // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => ArrayHelper::map(Accreditation::find()->all(), 'id', 'association', 'classification'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => [
                        'placeholder' => 'Select Association(s) ...', 
                        ['State', 'Regional', 'National/International', 'Programmatic',],
                        'multiple' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>
