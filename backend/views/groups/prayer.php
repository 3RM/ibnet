<?php

/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

$this->title = 'Prayer Table';
?>

<div class="site-index">

	<?= GridView::widget([
	    'dataProvider' => $dataProvider,
	    'filterModel' => $searchModel,
	    'columns' => $gridColumns,
	    'options' => [ 'style' => 'table-layout:fixed;' ],
	]); ?>

</div>

<?php foreach ($dataProvider->models as $model) {
    $this->registerJS("$('#prayer-group-" . $model->id . '-' . $model->group_id . "').click(function(e) {
        $.get('/groups/view-detail', {id: " . $model->group_id . "}, function(data) {
            $('#group-detail-modal').modal('show').find('#group-detail-content').html(data);
        })
    });", \yii\web\View::POS_READY);

    $this->registerJS("$('#prayer-member-" . $model->id . '-' . $model->group_member_id . "').click(function(e) {
        $.get('/groups/view-member-detail', {id: " . $model->group_member_id . "}, function(data) {
            $('#member-detail-modal').modal('show').find('#member-detail-content').html(data);
        })
    });", \yii\web\View::POS_READY);
} ?>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-users"></i></h3>',
    'id' => 'group-detail-modal',
    'headerOptions' => ['class' => 'modal-header-group'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="group-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-users"></i></h3>',
    'id' => 'member-detail-modal',
    'headerOptions' => ['class' => 'modal-header-group'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="member-detail-content"></div>';
Modal::end(); ?>