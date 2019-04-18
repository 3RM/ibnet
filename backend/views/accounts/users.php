<?php

/* @var $this yii\web\View */

use yii\widgets\ListView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'User Accounts';
Url::remember();
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
                <p class="col-180">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'name' ? 
                        Html::a('Name', Url::current(['sort' => '-name'])) : 
                        Html::a('Name', Url::current(['sort' => 'name']))) . 
                '</p>
                <p class="col-240">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'username' ? 
                        Html::a('Username', Url::current(['sort' => '-username'])) : 
                        Html::a('Username', Url::current(['sort' => 'username']))) . 
                '</p>
                <p class="col-300">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'email' ? 
                        Html::a('Email', Url::current(['sort' => '-email'])) : 
                        Html::a('Email', Url::current(['sort' => 'email']))) . 
                '</p>
                <p class="col-150">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'role' ? 
                        Html::a('Role', Url::current(['sort' => '-role'])) : 
                        Html::a('Role', Url::current(['sort' => 'role']))) . 
                '</p>
                <p class="col-150">' . 
                    (isset($_GET['sort']) && $_GET['sort'] == 'last_login' ? 
                        Html::a('Last Login', Url::current(['sort' => '-last_login'])) : 
                        Html::a('Last Login', Url::current(['sort' => 'last_login']))) . 
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
    'header' => '<h3>' . Html::icon('user'). '</h3>',
    'id' => 'user-detail-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'modal-body'],
]);
    echo '<div id="user-detail-content"></div>';
Modal::end(); ?>