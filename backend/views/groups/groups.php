<?php

/* @var $this yii\web\View */

use yii\widgets\ListView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'Groups';
?>

<div class="site-index">

	<?= ListView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'itemView' => '_group',
        'itemOptions' => ['class' => ''],
        'layout' => '
            <div class="summary-row hidden-print clearfix">{summary}</div>
        	<div class="header-row">
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'reviewed' ? 
                        Html::a(Html::icon('check'), Url::current(['sort' => '-reviewed'])) : 
                        Html::a(Html::icon('check'), Url::current(['sort' => 'reviewed']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'id' ? 
                        Html::a('ID', Url::current(['sort' => '-id'])) : 
                        Html::a('ID', Url::current(['sort' => 'id']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'user_id' ? 
                        Html::a('Owner', Url::current(['sort' => '-user_id'])) : 
                        Html::a('Owner', Url::current(['sort' => 'user_id']))) . 
                '</p>
                <p class="col-300">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'name' ? 
                        Html::a('Name', Url::current(['sort' => '-name'])) : 
                        Html::a('Name', Url::current(['sort' => 'name']))) . 
                '</p>
                <p class="col-100">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'group_level' ? 
                        Html::a('Level', Url::current(['sort' => '-group_level'])) : 
                        Html::a('Level', Url::current(['sort' => 'group_level']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'feature_prayer' ? 
                        Html::a('Prayer', Url::current(['sort' => '-feature_prayer'])) : 
                        Html::a('Prayer', Url::current(['sort' => 'feature_prayer']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'feature_calendar' ? 
                        Html::a('Cal', Url::current(['sort' => '-feature_calendar'])) : 
                        Html::a('Cal', Url::current(['sort' => 'feature_calendar']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'feature_notification' ? 
                        Html::a('Notif', Url::current(['sort' => '-feature_notification'])) : 
                        Html::a('Notif', Url::current(['sort' => 'feature_notification']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'feature_forum' ? 
                        Html::a('Forum', Url::current(['sort' => '-feature_forum'])) : 
                        Html::a('Forum', Url::current(['sort' => 'feature_forum']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'feature_update' ? 
                        Html::a('Update', Url::current(['sort' => '-feature_update'])) : 
                        Html::a('Update', Url::current(['sort' => 'feature_update']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'feature_document' ? 
                        Html::a('Doc', Url::current(['sort' => '-feature_document'])) : 
                        Html::a('Doc', Url::current(['sort' => 'feature_document']))) . 
                '</p>
                <p class="col-60">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'feature_donation' ? 
                        Html::a('Donate', Url::current(['sort' => '-feature_donation'])) : 
                        Html::a('Donate', Url::current(['sort' => 'feature_donation']))) . 
                '</p>
                <p class="col-100">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'status' ? 
                        Html::a('Status', Url::current(['sort' => '-status'])) : 
                        Html::a('Status', Url::current(['sort' => 'status']))) . 
                '</p>
        	</div>{items}{pager}',
    ]); ?>

</div>

<?php Modal::begin([
    'header' => '<h3><i class="fa fa-users"></i></h3>',
    'id' => 'group-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="group-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>