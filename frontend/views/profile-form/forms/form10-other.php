<?php

use common\models\profile\Profile;
use common\models\Utility;
use frontend\controllers\ProfileController;
use kartik\markdown\MarkdownEditor;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Other Ministries';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-8">
                <p>List any other ministries or organizations with which you are involved.</p>
                <p><?= HTML::icon('info-sign') ?> A ministry must have a listing in this directory.</p>
            </div>
        </div>

        <?php if ($ministryM) { ?>

            <div class = "row">
                <div class = "col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">Other Ministries</div>
                        <table class="table table-hover">
                            <?php foreach ($ministryM as $m) {
                                $p = ProfileController::findActiveProfile($m->ministry_id);
                                if ($m && $p) { ?>
                                    <tr>
                                        <td>
                                            <?= $m->staff_title . ' at ' . $p->org_name . ', ' . $p->org_city . 
                                                ($p->org_st_prov_reg ? (', ' . $p->org_st_prov_reg) : NULL) . 
                                                ($p->org_country == 'United States' ? NULL : ', ' . $p->org_country) ?>
                                        </td>
                                        <td>
                                            <?= Html::submitButton(Html::icon('remove'), [
                                                'method' => 'POST',
                                                'class' => 'btn btn-form btn-sm',
                                                'name' => 'removeM',
                                                'value' => $m['id'],
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

        <?php } ?>

        <?php if ($more) { ?>

            <div class = "row">
                <div class = "col-md-3">
                    <?= $form->field($profile, 'titleM')->textInput(['maxlength' => true]) ?>
                </div>
                <div class = "col-md-5">
                    <?php echo $form->field($profile, 'selectM')->widget(Select2::classname(), [ 
                        'options' => ['placeholder' => 'Search by name or city...'],
                        'initValueText' => 'Search ...', 
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['ministry-list-ajax', 'id' => $profile->id]),
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
                </div> 
            </div>
            <div class="row">
                <div class = "col-md-2">
                    <?= HTML::submitbutton('Save Ministry', [
                        'method' => 'POST',
                        'class' => 'btn btn-form btn-sm',
                        'name' => 'submitM',
                    ]) ?>
                </div>
            </div>
            <br>

        <?php } else { ?>

            <div class="row">
                <div class="col-md-8">
                    <?= HTML::submitbutton(Html::icon('plus') . ' Add a Ministry', [
                        'method' => 'POST',
                        'class' => 'btn btn-form btn-sm',
                        'name' => 'more',
                    ]) ?>
                </div>
            </div>

            <br>

        <?php } ?>
        <?= Html::activeHiddenInput($profile, 'select'); ?>

        <?= $this->render('_profileFormFooter', ['profile' => $profile, 'e' => $e]) ?>

        <?php ActiveForm::end(); ?>

    </div>
    
</div>