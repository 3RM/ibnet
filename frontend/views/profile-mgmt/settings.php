<?php

use common\widgets\Alert;
use kartik\date\DatePicker;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'My Account';
?>
<div class="wrap my-profiles">
    <div class="container">
        <div class="row">
        <h1><?= $this->title ?></h1>

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'Dashboard',
                    'url' => ['/site/dashboard'],
                ],
                [
                    'label' => 'Profiles',  
                    'active' => true,
                ],
            ],
        ]); ?>
        </div>
    </div>
</div>
<div class="clearprofiles"></div>
<?= Alert::widget() ?>

<?php $form = ActiveForm::begin(); ?>

<div class="container">

    <h2><?= Html::icon('cog') ?> Settings</h2> 
    For profile <span class="lead">"<?= $profile->profile_name ?>"</span>

    <div class="top-margin-60">
        <h3><span class="glyphicons glyphicons-history"></span> History</h3>
        <p>Share past highlights of your ministry.  Add events to your profile timeline to help others become more familiar with your ministry (e.g. ministry founding, location change, important staff changes, etc.).</p>

        <?php if ($events) { ?>

            <div class = "row">
                <div class = "col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">Timeline Events</div>
                        <table class="table table-hover">
                            <?php foreach ($events as $event) { 
                                if ($event->deleted != 1) { ?>
                                    <tr>
                                        <td nowrap>
                                            <?= Yii::$app->formatter->asDate($event->date, 'php:F j, Y') ?>
                                        </td>
                                        <td>
                                            <?= $event->title ?>
                                        </td>
                                        <td>
                                            <?= $event->description ?>
                                        </td>
                                        <td>
                                            <?= Html::submitButton(Html::icon('remove'), [
                                                'method' => 'POST',
                                                'class' => 'btn btn-form btn-sm',
                                                'name' => 'remove',
                                                'value' => $event->id,
                                            ]) ?>
                                        </td>
                                    </tr>                               
                                <?php }
                                } ?>
                        </table>
                    </div>
                </div>
            </div>

        <?php } ?>


        <?php if ($add) { ?>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($history, 'date')
                        ->widget(DatePicker::classname(), [
                            'name' => 'date',
                            'value' => 'Event Date...',
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'mm/dd/yyyy'
                            ]
                    ]) ?>
                </div>
                <div class = "col-md-5">
                   <?= $form->field($history, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Max 50 characters...',]) ?> 
                </div>
            </div>
            <div class="row">
                <div class = "col-md-8">
                   <?= $form->field($history, 'description')->textarea(['maxlength' => true, 'rows' => 2, 'placeholder' => 'Max 1000 characters...',]) ?> 
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <?= HTML::submitbutton('Save Event', [
                        'method' => 'POST',
                        'class' => 'btn btn-form btn-sm',
                        //'name' => 'submit',
                    ]) ?>
                </div>
            </div>
            <br>

        <?php } else { ?>

            <div class="row">
                <div class="col-md-8">
                    <?= HTML::submitbutton(Html::icon('plus') . ' Add Event', [
                        'method' => 'POST',
                        'class' => 'btn btn-form btn-sm',
                        'name' => 'add',
                    ]) ?>
                </div>
            </div>

            <br>

        <?php } ?>
    </div>

    <hr>

    <?= Html::a('<span class="glyphicons glyphicons-arrow-left" style="margin-top:-3px;"></span> Return', ['my-profiles'], ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>