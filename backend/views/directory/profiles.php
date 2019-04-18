<?php

/* @var $this yii\web\View */

use yii\widgets\ListView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'Ministry Profiles';
Url::remember();
?>

<div class="site-index">

	<?= ListView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'itemView' => '_profile',
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
                        Html::a('UID', Url::current(['sort' => '-user_id'])) : 
                        Html::a('UID', Url::current(['sort' => 'user_id']))) . 
                '</p>
        		<p class="col-100">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'type' ? 
                        Html::a('Type', Url::current(['sort' => '-type'])) : 
                        Html::a('Type', Url::current(['sort' => 'type']))) . 
                '</p>
        		<p class="col-180">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'name' ? 
                        Html::a('Name', Url::current(['sort' => '-name'])) : 
                        Html::a('Name', Url::current(['sort' => 'name']))) . 
                '</p>
        		<p class="col-150">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'created_at' ? 
                        Html::a('Created', Url::current(['sort' => '-created_at'])) : 
                        Html::a('Created', Url::current(['sort' => 'created_at']))) . 
                '</p>
        		<p class="col-150">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'renewal_date' ? 
                        Html::a('Renewal', Url::current(['sort' => '-renewal_date'])) : 
                        Html::a('Renewal', Url::current(['sort' => 'renewal_date']))) . 
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
    'header' => '<h3><i class="fa fa-address-card"></i></h3>',
    'id' => 'profile-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="profile-detail-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>