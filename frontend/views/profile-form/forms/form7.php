<?php

use common\models\profile\Country;
use common\models\profile\Profile;
use common\models\profile\MissionaryStatus;
use kartik\markdown\MarkdownEditor;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Missionary Field';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <p><?= HTML::icon('info-sign') ?> Some Restricted Access Nations (RAN) are currently excluded from this list.</p>
        <p><?= HTML::icon('info-sign') ?> If you are a furlough replacement missionary, select "Furlough Replacement" or choose your current field of service.</p>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($missionary, 'field')->widget(Select2::classname(), [                        // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => ArrayHelper::map(Country::find()->where(['ran' => ''])->all(), 'printable_name', 'printable_name'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => ['placeholder' => 'Select a country ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
                <?= $form->field($missionary, 'status')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(MissionaryStatus::find()->all(), 'status', 'status'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Select status ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>
    
        <?php ActiveForm::end(); ?>

    </div>

</div>
