<?php

use common\widgets\Alert;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

$this->title = 'My Account';
$menuItems = [
    ['label' => '<span class="glyphicons glyphicons-settings"></span> Settings', 'url' => ['/site/settings']],
    ['label' => '<span class="glyphicons glyphicons-vcard"></span> Profiles', 'url' => ['/profile-mgmt/my-profiles']],
    ['label' => '<span class="glyphicons glyphicons-cluster"></span> Networks', 'url' => ['/network/my-networks']],
    ['label' => '<span class="glyphicons glyphicons-direction"></span> Updates', 'url' => ['/missionary/update-repository'], ['visible' => Yii::$app->user->identity->isMissionary]],
];
?>
<div class="account-header-container">
    <div class=<?='"account-header acc-' . $active . '-header"'?>>
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="visible-xs">
            <?php 
            NavBar::begin([
                'options' => [
                    'id' => 'account0',
                    'class' => 'navbar-inverse account-nav no-transition',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
                'encodeLabels' => false,
            ]);
            NavBar::end(); ?>
        </div>
    </div>
</div>
<?= Alert::widget() ?>