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

$this->title = 'Name and Description';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($profile, 'org_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($profile, 'tagline')->textInput(['maxlength' => true]) ?>
                <?= $form->field($profile, 'description')->widget(MarkdownEditor::classname(), [
                        'height' => 200, 
                        'options' => ['smarty' => true],
                        'toolbar' => $toolbar
                    ]
                ); ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>
