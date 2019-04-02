<?php

use frontend\assets\AdminLtePluginAsset;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use kartik\form\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */

AdminLtePluginAsset::register($this);

$this->title = 'Calendar';
?>

<!-- Main content -->
<section class="content">
    <div class="row">

        <!-- boxes -->
        <div class="col-md-3 boxes">
            
            <!-- create event box -->
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title">Create Event</h4>
                </div>
                <div class="box-body">
                    <?php Modal::begin([
                        'header' => '<h3>' . Html::icon('calendar') . ' New Event</h3>',
                        'id' => 'new-event-modal',
                        'toggleButton' => [
                            'id' => 'new-event-btn',
                            'label' => Html::icon('calendar') . ' New Event',
                            'class' => 'btn btn-primary new-event-modal'],
                        'headerOptions' => ['class' => 'new-event-modal-header'],
                    ]); ?>
                        <div id="new-event-content"></div>
                    <?php Modal::end(); ?>
                </div>
                <p id="import-link"> <?= $urls ? 
                    Html::a('Import or manage calendar(s)', '#', ['id' => 'import']) :
                    Html::a('Import a calendar', '#', ['id' => 'import']); ?>
                </p>
            <!-- /create event box -->
            </div>

            <!-- upcoming box -->
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Upcoming</h3>
                </div>
                <div class="box-body">
                    <ul class="upcoming">
                        <?php if ($upcomingList) {
                            foreach ($upcomingList as $upcoming) {
                                echo '<li>';
                                echo '<span class="icon">' . Html::icon('pushpin') . '</span> <b>' . $upcoming->dates . '</b><br>' . $upcoming->title;
                                echo '</li>';
                            }
                        } else {
                            echo '<span style="font-style:italic; color:gray">No Upcoming Events</span>';
                        } ?>
                    </ul>
                </div>
            <!-- /upcoming box -->
            </div>  

            <!-- calendary tips box -->
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title">Calendar Tips</h4>
                </div>
                <div class="box-body">
                    <ul class="new">
                        <li>Click event to view, edit or remove</li>
                        <li>Click day to jump to day view</li>
                        <li>Press and hold day on mobile devices</li>
                    </ul>
                </div>
            <!-- /calendar tips box -->
            </div>

        <!-- /boxes -->
        </div>

        <!-- calendar -->
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-body no-padding">
                    <?= \yii2fullcalendar\yii2fullcalendar::widget([
                        'header' => [
                            'left' => 'prev,next today',
                            'center' => 'title',
                            'right' => 'picker month,agendaWeek,agendaDay',
                        ],
                        'clientOptions' => [
                            'theme' => true,
                            'themeSystem' => 'jquery-ui',
                            'selectable' => true,
                            'eventLimit' => true,
                            'select' => new JSExpression("function(date, allDay, jsEvent, view) {
                                if (view.name == 'day') return;
                                    view.calendar.gotoDate(date);
                                    view.calendar.changeView('agendaDay');
                            }"),
                            'eventClick' => new JsExpression("function(event, jsEvent, view) {
                                $.get('/network/view-event', {id: " . $network->id . ", eid: event.id, resourceId: event.resourceId}, function(data) {
                                    var viewModal = $('#view-event-modal');
                                    viewModal.modal('show').find('#view-event-content').html(data);
                                    viewModal.find('.view-event-modal-header').css('background-color', event.color);
                                    
                                    $('#edit-event-btn').click(function(e) {
                                        $('#view-event-modal').modal('hide');
                                       
                                        setTimeout(function() {
                                            $.get('/network/edit-event', {id:" . $network->id . ", eid:event.id}, function(data) {
                                                $('#edit-event-modal').modal('show').find('#edit-event-content').html(data);
                                            });
                                        }, 500);
                                     
                                    });
                                });
                            }"),
                            'eventSources' => [
                                $eventList,
                                $icalList,
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        <!-- /calendar -->
        </div>

    </div>
</section>

<?php Modal::begin([
    'header' => '<span class="glyphicons glyphicons-note-empty"></span>',
    'id' => 'view-event-modal',
    'size' => 'modal-sm',
    'headerOptions' => ['class' => 'view-event-modal-header'],
]);
    echo '<div id="view-event-content"></div>';
Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h3>' . Html::icon('calendar') . ' Edit Event</h3>',
    'id' => 'edit-event-modal',
    'size' => 'modal-md',
    'headerOptions' => ['class' => 'edit-event-modal-header'],
]);
    echo '<div id="edit-event-content"></div>';
Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h3><span class="glyphicons glyphicons-redo"></span> Import a Calendar</h3>',
    'id' => 'import-modal',
    'size' => 'modal-md',
    'headerOptions' => ['class' => 'import-modal-header'],
]);
    echo '<div id="import-content"></div>';
Modal::end(); ?>

<?php $this->registerJs("$('#new-event-btn').click(function(e) {
    $.get('/network/new-event', {id:" . $network->id . "}, function(data) {
        $('#new-event-modal').modal('show').find('#new-event-content').html(data);
    });
})", \yii\web\View::POS_READY); ?>
<?php $this->registerJs("$('a#import').click(function(e) { 
    $.get('/network/import-calendar', {id:" . $network->id . "}, function(data) {
        $('#import-modal').modal('show').find('#import-content').html(data);
    });
})", \yii\web\View::POS_READY); ?>