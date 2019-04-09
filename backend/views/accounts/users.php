<?php

/* @var $this yii\web\View */

use yii\widgets\ListView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

$this->title = 'User Accounts';
?>

<div class="site-index">

	<?= ListView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'itemView' => '_user',
        'itemOptions' => ['class' => ''],
        'layout' => '
            <div class="summary-row hidden-print clearfix">{summary}</div>
        	<div class="header-row">
                <p class="col-60">' . Html::icon('check') . '</p>
                <p class="col-60">ID</p>
                <p class="col-180">Name</p>
                <p class="col-240">Username</p>
                <p class="col-300">Email</p>
                <p class="col-150">Last Login</p>
        	</div>{items}{pager}',
    ]); ?>

</div>

<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'user-detail-modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>