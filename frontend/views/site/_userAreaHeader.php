<?php

use common\widgets\Alert;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar; use common\models\Utility; //Utility::pp(Yii::$app->user->identity->isMissionary);

$this->title = 'My Account';
$menuItems = [
    ['label' => '<span class="glyphicons glyphicons-settings"></span> Settings', 'url' => ['/site/settings']],
    ['label' => '<span class="glyphicons glyphicons-vcard"></span> Profiles', 'url' => ['/profile-mgmt/my-profiles']],
    ['label' => '<span class="glyphicons glyphicons-cluster"></span> Groups', 'url' => ['/network/my-groups']],
];
if (Yii::$app->user->identity->isMissionary) {
    $menuItems[] = ['label' => '<span class="glyphicons glyphicons-direction"></span> Updates', 'url' => ['/missionary/update-repository']];
}
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