<?php

use common\models\profile\Profile;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Programs';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">
 
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <p><?= HTML::icon('info-sign') ?> A program must have a listing in this directory.</p>
                <?php echo $form->field($profile, 'select')->widget(Select2::classname(), [ 
                    'options' => ['placeholder' => 'Search by name or city...'],
                    'initValueText' => 'Search ...', 
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['program-list-ajax']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(profile) { 
                            if(profile.org_city > "" && profile.org_st_prov_reg > "") {
                                return profile.text+", "+profile.org_city+", "+profile.org_st_prov_reg;
                            } else {
                                return profile.text;
                            };
                        }'),
                        'templateSelection' => new JsExpression('function (profile) { 
                            if(profile.org_city > "" && profile.org_st_prov_reg > "") {
                                return profile.text+", "+profile.org_city+", "+profile.org_st_prov_reg;
                            } else {
                                return profile.text;
                            };
                        }'),
                    ],
                ]); ?>
                <?= Html::submitButton(Html::icon('ok') . ' Add', [
                    'method' => 'POST',
                    'class' => 'btn btn-form btn-sm',
                    'name' => 'add',
                ]); ?>
            </div>
        </div>
            <br>

        <?php if ($programs != NULL) { ?>

            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Program(s) of <?= $profile->org_name ?></div>
                        <table class="table table-hover">
                            <?php foreach ($programs as $program) {
                                echo '<tr>';
                                    echo '<td><b>' . $program->org_name . '</b></td>';
                                    echo '<td>';
                                        echo Html::submitButton(Html::icon('remove'), [
                                            'method' => 'POST',
                                            'class' => 'btn btn-form btn-sm',
                                            'name' => 'remove',
                                            'value' => $program->id,
                                        ]);
                                    echo '</td>';
                                echo '</tr>';
                            } ?>
                        </table>
                    </div>
                </div>
            </div>

        <?php } ?>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>