<?php

use common\models\profile\Profile;
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

$this->title = 'Church Plant';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php if (Yii::$app->session->hasFlash('success')) {                                                // Show info message to alert that church plant profile is currently inactive
            echo '<br>';
            echo Alert::widget([                                                                             
                'options' => ['class' => 'alert-success'],
                'body' => Yii::$app->session->getFlash('success'),                                     
            ]);
        } elseif (Yii::$app->session->hasFlash('info')) {
            echo '<br>';
            echo Alert::widget([                                                                             
                'options' => ['class' => 'alert-info'],
                'body' => Yii::$app->session->getFlash('info'),                                         
            ]);
        } elseif (Yii::$app->session->hasFlash('error')) {
            echo '<br>';
            echo Alert::widget([                                                                             
                'options' => ['class' => 'alert-danger'],
                'body' => Yii::$app->session->getFlash('error'),                                    
            ]);
        } ?>
    
        <?php $form = ActiveForm::begin(); ?>
    
        <?php if (empty($ministryLink)) { ?>

            <div class="row">
                <div class="col-md-6">
                    <p><?= HTML::icon('info-sign') ?> A church or church plant must have a listing in this directory.</p>
                    <?php echo $form->field($missionary, 'select')->widget(Select2::classname(), [ 
                        'options' => ['placeholder' => 'Search by name or city...'],
                        'initValueText' => 'Search ...', 
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['church-list-ajax', 'chId' => $profile->home_church]),
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

        <?php } else { ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">Church Plant</div>
                        <table class="table table-hover">
                            <td>Church-planting pastor at <b>
                                <?= $ministryLink->org_name . ', ' . $ministryLink->org_city .
                                    (empty($ministryLink->org_st_prov_reg) ? NULL : ', ' . $ministryLink->org_st_prov_reg) .
                                    (empty($ministryLink->org_country) || ($ministryLink->org_country == 'United States') ? NULL : ', ' . $ministryLink->org_country) ?></b>
                            </td>
                            <td>    
                                <?= Html::submitButton(Html::icon('remove'), [
                                    'method' => 'POST',
                                    'class' => 'btn btn-form btn-sm',
                                    'name' => 'remove',
                                ]) ?>
                            </td>
                        </table>
                        <?= Html::activeHiddenInput($missionary, 'select'); ?>
                    </div>
                </div>
            </div>

        <?php } ?>

        <br>

        <div class="row">
            <div class="col-md-8">
                <p><?php if ($msg == Profile::MAP_PRIMARY) {
                    echo Html::icon('map-marker') . ' Your profile currently shows a map of your primary address.';
                } elseif ($msg == Profile::MAP_CHURCH) {
                    echo Html::icon('map-marker') . ' Your profile currently shows a map of your home church address.';
                } elseif ($msg == Profile::MAP_MINISTRY) {
                    echo Html::icon('map-marker') . ' Your profile currently shows a map of your ministry address.';
                } elseif ($msg == NULL) {
                    echo Html::icon('map-marker') . ' Your profile is not currently showing a map.';
                } ?>
                <?= $form->field($missionary, 'showMap')->checkbox() ?></p>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>
    
        <?php ActiveForm::end(); ?>

    </div>

</div>