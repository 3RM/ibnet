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

if ($profile->type == Profile::TYPE_STAFF) {
    $this->title = 'Place of Ministry';
} else {
    $this->title = 'Ministry or Organization';
}
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <?= $profile->type == Profile::TYPE_EVANGELIST ? '<p>If you serve as an evangelist with a ministry or organization in addition to your home church, list it here.</p>' : NULL; ?>
        </div>
        <p><?= HTML::icon('info-sign') ?> A ministry or orginzation must have a listing in this directory.</p>
        <div class="row">
            <div class="col-md-6">
                <?php echo $form->field($profile, 'ministry_of')->widget(Select2::classname(), [
                    'data' => $initialData, 
                    'options' => ['placeholder' => 'Search by name or city...'], 
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['ajax/search', 'type' => 'ministry']),
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
                } elseif ($profile->show_map == Profile::MAP_CHURCH) {
                    echo Html::icon('map-marker') . ' Your profile currently shows a map of your home church address.';
                } elseif ($profile->show_map == Profile::MAP_CHURCH_PLANT) {
                    echo Html::icon('map-marker') . ' Your profile currently shows a map of your church plant address.';
                } elseif ($profile->show_map == NULL) {
                    echo Html::icon('map-marker') . ' Your profile is not currently showing a map.';
                } ?>
                <?= $form->field($profile, 'map')->checkbox() ?></p>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-8">
                <h3>Other Ministries</h3>
                <p>List any other ministries or organizations with which you are involved.</p>

                <?php if ($otherMinistries) { ?>
           
                    <div class="panel panel-default">
                        <div class="panel-heading">Other Ministries</div>
                        <table class="table table-hover">
                            <?php foreach ($otherMinistries as $m) { ?>
                            <tr>
                                <?php $p = Profile::getActiveProfile($m->ministry_id); ?>
                                <td>
                                    <?= $m['staff_title'] . ' at ' . $p->org_name . ', ' . $p->org_city . 
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
                        </table>
                    </div>

                <?php } ?>

            </div>
        </div>

        <?php if ($more) { ?>
        
        <div class = "row">
            <div class = "col-md-2">
                <?= $form->field($profile, 'titleM')->textInput(['maxlength' => true]) ?>
            </div>
            <div class = "col-md-5">
                <?php echo $form->field($profile, 'selectM')->widget(Select2::classname(), [ 
                    'options' => ['placeholder' => 'Search by name or city...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['ajax/search', 'type' => 'ministry']),
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
            <div class = "col-md-1 top-margin-28">
                <?= HTML::submitbutton('Save Ministry', [
                    'method' => 'POST',
                    'class' => 'btn btn-form btn-sm',
                    'name' => 'submit-more',
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

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>
        
        <?php ActiveForm::end(); ?>

    </div>

</div>