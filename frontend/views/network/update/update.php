<?php

use common\models\Utility;
use frontend\assets\NetworkAsset;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\widgets\ListView;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

NetworkAsset::register($this);
$this->title = 'Missionary Updates';
?>

<!-- Main content -->
<section class="content">
    <div class="row">

        <!-- boxes -->
        <div class="col-md-3 boxes">

            <!-- filter box -->
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title">Missionary</h4>
                </div>
                <div class="box-body">
                    <?= Select2::widget([
                        'id' => 'name-id',
                        'name' => 'nameSelect',
                        'data' => $updateNameList,
                        'options' => ['class' => 'filter-select', 'placeholder' => 'Missionary'],
                    ]); ?>
                    <?php $this->registerJs(
                    "$('#name-id').change(function () {
                        nameVal = '?UpdateSearch%5Bname%5D\=' + $('#name-id option:selected').text();
                        window.location.replace('" . Url::to(['network/update', 'id' => $network->id]) . "' + nameVal);
                    });", \yii\web\View::POS_READY); ?>
                    <div class="reset">
                        <?= Html::a('<span class="glyphicons glyphicons-repeat">', Url::to(['network/update', 'id' => $network->id]), ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Reset List']) ?>
                    </div>
                </div>
            <!-- /filter box -->
            </div>

            <!-- alerts box -->
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title">Email Alerts</h4>
                </div>
                <div class="box-body">
                    <?php $form = ActiveForm::begin(); ?>
                    <label class="control-sidebar-subheading chbx-alert">
                        Immediate Alert
                        <?= $form->field($member, 'email_update_alert', ['options' => ['id' => 'alert-id', 'class' => 'pull-right']])->checkbox() ?>
                    </label>
                    <?php $this->registerJs(
                    "$('#alert-id').change(function () {
                        $.ajax({
                            type: 'POST',
                            url: '" . Url::toRoute(['ajax/update-alert']) . "',
                            dataType: 'json',
                            data: jQuery.param({ mid: '" . $member->id . "'}),
                            async: true,
                            success: function (msg) {
                            }
                        });
                    });", \yii\web\View::POS_READY); ?>
                    <?php $form = ActiveForm::end(); ?>
                </div>
            <!-- /alerts box -->
            </div>

        <!-- /boxes -->
        </div>

        <!-- update list -->
        <div class="col-md-9">
            <?= ListView::widget([
                'dataProvider' => $updateDataProvider,
                'showOnEmpty' => false,
                'emptyText' => '<h3><em>... no updates</em></h3>',
                'itemView' => '_updateItem',
                'itemOptions' => ['class' => 'item-bordered'],
                'layout' => '<div class="summary-row hidden-print clearfix">{items}{pager}',
            ]); ?>
        </div>
        <!-- /update list -->

    </div>
</section>