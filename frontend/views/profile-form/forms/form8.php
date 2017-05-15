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

$profile->type == 'Missionary' ?
    $this->title = 'Sending Church' :
    $this->title = 'Home Church';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">
 
        <?php $form = ActiveForm::begin(); ?>

        <?php if (empty($churchLink)) { ?>

            <div class="row">
                <div class="col-md-6">
                    <p><?= HTML::icon('info-sign') ?> Your home church must have a listing in this directory.</p>
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
                                'url' => Url::to(['church-list-ajax', 'chId' => $chId]),
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
                        <div class="panel-heading">Church</div>
                        <table class="table table-hover">
                            <td><?= $churchLabel ?> <b><?= $churchLink->org_name . ', ' . $churchLink->org_city . ', ' . $churchLink->org_st_prov_reg ?></b></td>
                            <td>
                                <?= Html::submitButton(HTML::icon('remove'), [
                                    'method' => 'POST',
                                    'class' => 'btn btn-form btn-sm',
                                    'name' => 'remove',
                                ]) ?>
                            </td>
                        </table>
                    </div>
                    <?= Html::activeHiddenInput($profile, 'select'); ?>
                </div>
            </div>

        <?php } ?>

        <br>

        <div class="row">
            <div class="col-md-8">
                <p><?php if ($profile->show_map == Profile::MAP_PRIMARY) {
                    echo Html::icon('map-marker') . ' Your profile currently shows a map of your primary address.';
                } elseif ($profile->show_map == Profile::MAP_MINISTRY) {
                    echo Html::icon('map-marker') . ' Your profile currently shows a map of your ministry address.';
                } elseif ($profile->show_map == Profile::MAP_CHURCH_PLANT) {
                    echo Html::icon('map-marker') . ' Your profile currently shows a map of your church plant address.';
                } elseif ($profile->show_map == NULL) {
                    echo Html::icon('map-marker') . ' Your profile is not currently showing a map.';
                } ?>
                <?= $form->field($profile, 'map')->checkbox() ?></p>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>
    
        <?php ActiveForm::end(); ?>

    </div>

</div>