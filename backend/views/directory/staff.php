<?php

/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal; use common\models\Utility;

$this->title = 'Staff Table';
?>

<div class="site-index">

	<?= GridView::widget([
	    'dataProvider'=>$dataProvider,
	    'filterModel'=>$searchModel,
	    'columns'=>$gridColumns,
	]); ?>

</div>

<?php foreach ($dataProvider->models as $model) {
    $this->registerJS("$('#profile-" . $model->id . "-" . $model->staff_id . "').click(function(e) {
        $.get('/directory/view-detail', {id: " . $model->staff_id . "}, function(data) {
            $('#profile-detail-modal').modal('show').find('#profile-detail-content').html(data);
        })
    });", \yii\web\View::POS_READY);

    $this->registerJS("$('#profile-" . $model->id . "-" . $model->ministry_id . "').click(function(e) {
        $.get('/directory/view-detail', {id: " . $model->ministry_id . "}, function(data) {
            $('#profile-detail-modal').modal('show').find('#profile-detail-content').html(data);
        })
    });", \yii\web\View::POS_READY);
} ?>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'profile-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="profile-detail-content"></div>';
Modal::end(); ?>