<?php

/* @var $this yii\web\View */

use yii\widgets\ListView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

$this->title = 'Ministry Profiles';
?>

<div class="site-index">

	<?= ListView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'showOnEmpty' => false,
        'itemView' => '_profile',
        'itemOptions' => ['class' => ''],
        'layout' => '
        	<div class="summary-row hidden-print clearfix">{summary}</div>
        	<div class="header-row">
        		<p class="col-60">' . Html::icon('check') . '</p>
        		<p class="col-60">ID</p>
        		<p class="col-60">UID</p>
        		<p class="col-100">Type</p>
        		<p class="col-180">Name</p>
        		<p class="col-150">Created</p>
        		<p class="col-150">Renewal</p>
        		<p class="col-60">Status</p>
        	</div>{items}{pager}',
    ]); ?>

</div>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'profile-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'profile-detail-modal-body'],
]);
    echo '<div id="profile-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'user-detail-modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>