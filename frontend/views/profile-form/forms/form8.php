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

$profile->type == Profile::TYPE_MISSIONARY ?
    $this->title = 'Sending Church' :
    $this->title = 'Home Church';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">
 
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <p><?= HTML::icon('info-sign') ?> Your home church must have a listing in this directory.</p>
                <?php echo $form->field($profile, 'home_church')->widget(Select2::classname(), [ 
                    'data' => $initialData,
                    'options' => ['placeholder' => 'Search by name or city...'], 
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['ajax/search', 'exclude' => $exclude]),
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