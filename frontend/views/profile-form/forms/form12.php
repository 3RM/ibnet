
<?php

use app\models\MissionAgcy;
use common\models\profile\Profile;
use common\models\profile\School;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'School(s) Attended';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(); ?>

        <p>Select any schools from which you have graduated.</p>
        <p>
            <?= Html::icon('info-sign') ?> The appearance of any school in this list does not imply approval or endorsement as an Independent Baptist 
            school by IBNet.
        </p>
        <div class="row">
            <div class="col-md-8">
                <?= $form->field($profile, 'select')->widget(Select2::classname(), [                 // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => $list,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'maintainOrder' => true,
                    'showToggleAll' => false,
                    'options' => [
                        'placeholder' => 'Select School(s) ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ])->label(''); ?>
            </div>
        </div>

       <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>