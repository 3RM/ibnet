<?php

use common\models\missionary\Missionary;
use common\models\profile\MissionAgcy;
use common\models\profile\Profile; 
use common\models\Utility;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Mission Agency & Packet';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <div class="row">
            <div class="col-md-8">
                <h3>Mission Agency</h3>
                <p><?= HTML::icon('info-sign') ?> The appearance of any organization in this list does not imply approval or endorsement as an Independent Baptist 
                    organization by IBNet.  If your mission agency is not listed, please contact us so that it can be added subject to evaluation.
                </p>
            </div>
        </div>

         <div class="row">
            <div class="col-md-6">
                <?= $form->field($missionary, 'mission_agcy_id')->widget(Select2::classname(), [
                    'data' => $list,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => ['placeholder' => 'Select your mission agency ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>

        <br>
    
        <div class="row">
            <div class="col-md-8">
                <h3>Missionary Information Packet</h3>
                <p><?= HTML::icon('info-sign') ?> Upload one PDF file (max 4MB).</p>
                <?= $form->field($missionary, 'packet')->fileInput() ?>
            </div>
        </div>
    
        <?php if (isset($missionary->packet)) { ?>
            <div class="row">
                <div class="col-md-8">
                    <p>You have one uploaded PDF file.</p>
                    <?= Html::a(HTML::icon('download-alt'), ['profile-form/download', 'path' => $missionary->packet], ['class' => 'btn btn-form btn-sm', 'target' => '_blank', 'rel' => 'noopener noreferrer']) ?>
                    <?= Html::submitButton(HTML::icon('remove'), [
                        'method' => 'POST',
                        'class' => 'btn btn-form btn-sm',
                        'name' => 'remove',
                    ]) ?>
                </div>
            </div>
        <?php } ?> 
    
        <br>
        <br>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>