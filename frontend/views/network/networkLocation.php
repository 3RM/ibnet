<?php

use frontend\assets\AjaxAsset;
use frontend\assets\NetworkAsset;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Location and Keywords';
AjaxAsset::register($this);
NetworkAsset::register($this);
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container-form">

    <p><?= HTML::icon('info-sign') ?> The information collected here will aid in network searches.</p>

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($network, 'network_level')->widget(Select2::classname(), [
                'data' => ['Local' => 'Local', 'Regional' => 'Regional', 'State/Province' => 'State/Province', 'National' => 'National', 'International' => 'International'],
                'options' => ['placeholder' => 'Select ...'],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>
            <?= Html::submitButton('Save', [
                'id' => 'saveNetwork',
                'method' => 'POST',
                'style' => 'display:none',
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

    <!-- Places -->
    <p class="top-margin">
        <?= HTML::icon('info-sign') ?> Add one or more places. For example: a city for a local network, or 
        multiple states for a regional network.
    </p>
    <?php $form = ActiveForm::begin([
        'id' => $place->formName(), 
        'action' => '/ajax/network-place', 
        'options' => ['data-on-done' => 'placeAddDone'],
    ]); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($place, 'place')->textInput(['placeholder' => 'Search...', 'id' => 'places']); ?>
        </div>
        <div class="col-md-1">
            <?= Html::submitButton('Save', [
                'method' => 'POST',
                'class' => 'btn-form btn-sm btn-lower',
                'name' => 'add',
                'value' => $network->id,
            ]) ?>
        </div>
    </div>
    <?php $form = ActiveForm::end(); ?>
    <div id="placeListContainer">
        <?php if ($placeList != NULL) { ?>
        <div id="placeList" class="item-list">
            <?php foreach ($placeList as $loc) {
                echo '<div id="place-' . $loc->id . '" class="item-row place-row">';
                    echo $loc->city ? $loc->city . ', ' : NULL;
                    echo $loc->state ? $loc->state . ', ' : NULL;
                    echo $loc->country ? $loc->country . ' ' : NULL;
                    echo Html::a(Html::icon('remove'), ['ajax/delete-network-place', 'pid' => $loc->id], 
                        [
                            'id' => 'placeitem-' . $loc->id, 
                            'data-on-done' => 'placeDeleteDone'
                        ]) . '<br>';
                echo '</div>';
                $this->registerJs("$('#placeitem-" . $loc->id . "').click(handleAjaxSpanLink);", \yii\web\View::POS_READY);
            } ?>
        </div>
        <?php } ?>
    </div>
    <?php $this->registerJs("$('form#{$place->formName()}').submit(handleAjaxForm);", \yii\web\View::POS_READY); ?>

    <!-- Keywords -->
    <p class="top-margin">
        <?= HTML::icon('info-sign') ?> Add one or more keywords to help identify what your network is about. 
        Example: Minnesota pastors or school staff.
    </p>
    <?php $form = ActiveForm::begin([
        'id' => $keyword->formName(), 
        'action' => '/ajax/network-keyword', 
        'options' => ['data-on-done' => 'keywordAddDone'],
    ]); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($keyword, 'keyword')->textInput(['id' => 'keywords']); ?>
        </div>
        <div class="col-md-1">
            <?= Html::submitButton('Save', [
                'method' => 'POST',
                'class' => 'btn-form btn-sm btn-lower',
                'name' => 'add',
                'value' => $network->id,
            ]) ?>
        </div>
    </div>
    <?php $form = ActiveForm::end(); ?>
    <div id="keywordListContainer">
        <?php if ($keywordList != NULL) { ?>
        <div id="keywordList" class="item-list">
            <?php foreach ($keywordList as $word) {
                echo '<div id="keyword-' . $word->id . '" class="item-row keyword-row">';
                    echo $word->keyword;
                    echo Html::a(Html::icon('remove'), ['ajax/delete-network-keyword', 'kid' => $word->id], 
                        [
                            'id' => 'keyworditem-' . $word->id, 
                            'data-on-done' => 'keywordDeleteDone'
                        ]) . '<br>';
                echo '</div>';
                $this->registerJs("$('#keyworditem-" . $word->id . "').click(handleAjaxSpanLink);", \yii\web\View::POS_READY);
            } ?>
        </div>
        <?php } ?>
    </div>
    <?php $this->registerJs("$('form#{$keyword->formName()}').submit(handleAjaxForm);", \yii\web\View::POS_READY); ?>

    <div class="row top-margin-40">
        <div class="col-md-8">
            <?= Html::a('Cancel', ['my-networks'], ['class' => 'btn btn-primary']) ?>
            <?= Html::button('Save', [
                'id' => 'save',
                'class' => 'btn btn-primary',
            ]) ?>
            <?php $this->registerJs("$('#save').click(function(){ $('#saveNetwork').click(); });", \yii\web\View::POS_READY); ?>
        </div>
    </div>

</div>


<script type="text/javascript">

    function init() {

        var options = {
            types: ['(regions)'],
            // componentRestrictions: {country: "us"}
        };

        var input = document.getElementById('places');
        var autocomplete = new google.maps.places.Autocomplete(input, options);
    }

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= Yii::$app->params['apiKey.Google'] ?>&libraries=places&callback=init" async defer></script>