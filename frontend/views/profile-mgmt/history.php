<?php

use common\widgets\Alert;
use frontend\controllers\ProfileController;
use kartik\date\DatePicker;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'My Account';
?>

<div class="account-header-container">
    <div class="account-header acc-profiles-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
</div>
<?= Alert::widget() ?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="container">

    <h2 class="top-margin-60"><span class="glyphicons glyphicons-history"></span> Ministry Timeline</h2> 
    For profile <span class="lead">"<?= $profile->profile_name ?>"</span>
    <br><br>
    <?= $profile->url_loc ? '<p>' . Html::a('View my timeline ' . Html::icon('new-window', ['class' => 'internal-link']), ['/profile/' . ProfileController::$profilePageArray[$profile->type], 'id' => $profile->id, 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'p' => 'history', '#' => 'p'], ['target' => '_blank']) . '</p>' : NULL; ?>

    <div class="row top-margin">
        <div class="col-md-8">
            <p>Share past highlights of your ministry.  Add events to your profile timeline to help others become more familiar with your ministry (e.g. ministry founding, location change, important staff changes, etc.).</p>
        </div>
    </div>

    <?php if ($action == 'add') { ?>
        <div class="timeline-form">
            <div class="row">
                <div class="col-md-12">
                    <h3>New Event</h3>
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
                    <?= $form->field($history, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Max 50 characters...',]) ?> 
                    <?= $form->field($history, 'description')->textarea(['style' => 'resize:vertical', 'maxlength' => true, 'rows' => 3, 'placeholder' => 'Max 1000 characters...',]) ?> 
                    <div style="width:200px">
                        <?= $form->field($history, 'event_image')->widget(\sadovojav\cutter\Cutter::className(), [
                            'cropperOptions' => [
                                'viewMode' => 1,    
                                'aspectRatio' => 1,           // 200px x 200px
                                'movable' => false,
                                'rotatable' => false,
                                'scalable' => false,
                                'zoomable' => false,
                                'zoomOnTouch' => false,
                                'zoomOnWheel' => false,
                            ]
                        ]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 top-margin">
                    <?= Html::a('Cancel', ['/profile-mgmt/history', 'id' => $profile->id], ['class' => 'btn btn-primary']) ?>
                    <?= HTML::submitbutton('Save', [
                        'method' => 'POST',
                        'class' => 'btn btn-primary',
                        'name' => 'save',
                    ]) ?>
                </div>
            </div>
            <br>
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-md-8">
                <?= HTML::submitbutton(Html::icon('plus') . ' Add Event', [
                    'method' => 'POST',
                    'class' => 'btn btn-primary top-margin',
                    'name' => 'add',
                ]) ?>
            </div>
        </div>
        <br>
    <?php } ?>

    <?php if ($events) { ?>

        <div class = "row">
            <div class = "col-md-8">
                <div class="panel panel-default">
                    <div id ="timeline-form-heading" class="panel-heading">Timeline Events</div>
                    <table class="table">
                    <?php foreach ($events as $event) { 
                        if ($event->edit) { ?>
                            <tr  id=<?= '"' . $event->id . '"' ?>>
                                <td colspan="3" class="edit-event">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= $form->field($event, 'date')
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
                                        <div class="col-md-6">
                                            <?= $form->field($event, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Max 50 characters...',]) ?> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= $form->field($event, 'description')->textarea(['style' => 'resize:vertical', 'maxlength' => true, 'rows' => 3, 'placeholder' => 'Max 1000 characters...',]) ?> 
                                        </div>
                                    </div>
                                    <div style="width:200px">
                                        <?= $form->field($event, 'event_image')->widget(\sadovojav\cutter\Cutter::className(), [
                                            'cropperOptions' => [
                                                'viewMode' => 1,    
                                                'aspectRatio' => 1,           // 200px x 200px
                                                'movable' => false,
                                                'rotatable' => false,
                                                'scalable' => false,
                                                'zoomable' => false,
                                                'zoomOnTouch' => false,
                                                'zoomOnWheel' => false,
                                            ]
                                        ]); ?>
                                        <p style="margin:-20px 0 20px; font-size:0.8em">Image will be resized to 200px width.  Max 4MB.</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= Html::a('Cancel', ['/profile-mgmt/history', 'id' => $profile->id], ['class' => 'btn btn-form btn-sm']) ?>
                                            <?= Html::submitButton('Save', ['method' => 'POST',
                                                'class' => 'btn btn-form btn-sm',
                                                'name' => 'edit-save',
                                                'value' => $event->id,
                                            ]) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>   
                        <?php } else { ?>
                            <tr id=<?= '"' . $event->id . '"' ?>>
                                <td style="padding:20px">
                                    <h3><?= $event->title ?></h3>
                                    <p style="color:#dc9f27"><?= Html::icon('calendar') . ' ' . Yii::$app->formatter->asDate($event->date, 'php:F j, Y') ?></p>
                                    <div class="event-desc-img">
                                        <?= $event->event_image ? Html::img($event->event_image) : NULL ?>
                                        <p><?= $event->description ?></p>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="action-buttons">
                                    <?php if ($action != 'edit' &&  $action != 'add') { ?>
                                        <?= Html::submitButton('Remove', [
                                            'method' => 'POST',
                                            'class' => 'btn btn-form btn-sm',
                                            'name' => 'remove',
                                            'value' => $event->id,
                                        ]) ?>
                                        <?= Html::submitButton('edit', [
                                            'method' => 'POST',
                                            'class' => 'btn btn-form btn-sm',
                                            'name' => 'edit',
                                            'value' => $event->id,
                                        ]) ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php }                           
                    } ?>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div>

<hr>

<?= Html::a('<span class="glyphicons glyphicons-arrow-left" style="margin-top:-3px;"></span> Return', ['my-profiles'], ['class' => 'btn btn-primary']) ?>

<?= Html::hiddenInput('hash', $a, ['id' => 'hash']) ?>
<script type="text/javascript">
    $(document).ready(function(){
            // console.log($('#hash').val());
            location.hash = "#"+$('#hash').val();           
    });
</script>