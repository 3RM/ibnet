<?php

use common\widgets\Alert;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

\Eddmash\Clipboard\ClipboardAsset::register($this);
$this->title = 'My Account';
$menuItems = [
    ['label' => '<span class="glyphicons glyphicons-settings"></span> Settings', 'url' => ['/site/settings']],
    ['label' => '<span class="glyphicons glyphicons-vcard"></span> Profiles', 'url' => ['/profile-mgmt/my-profiles']],
    ['label' => '<span class="glyphicons glyphicons-direction"></span> Updates', 'url' => ['/missionary/update-repository'], ['visible' => Yii::$app->user->identity->is_missionary]],
];
?>
<div class="account-header-container">
    <div class="account-header acc-update-header">
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

<div class="container">
	<h1>MailChimp Setup</h1>

    <?php if ($unsynced) { ?>

        <div class="top-margin">
            <h4>Your account has been unsynced from Mailchimp. Your Updates page will no longer receive automatic updates from Mailchimp.</h4>
            <p>Your Mailchimp information has been completely removed from IBNet.  However, to complete the unconnect from the Mailchimp side, log into Mailchimp and go to Account->Extras->API Keys and scroll down to Authorized Applications.  If you see IBNet in the list, clic the "X" to remove it.</p>
        </div>
        <?= Html::a('OK', ['missionary/update-repository'], ['class' => 'btn btn-primary top-margin']) ?>

    <?php } else { ?>
        <h2>Step 1 of 2</h2>
        
        <p>Click on Freddie below.  You will be taken to MailChimp to login.  Upon successful login, you will be sent back here to complete the setup.</p>
        <p><?= Html::icon('info-sign') ?> You may experience a "server error" on this step or the next if you're on a slow internet connection.  If you experience this error, please try again and on a faster connection when possible.</p>
        <div class="mailchimp"><?= Html::a(Html::img('@images/content/freddie.png'), ['missionary/mailchimp-authorize']) ?></div>

        <?php $form = ActiveForm::begin(); ?>
        <div class="row top-margin">
            <div class="col-md-8">
                <?= Html::a('Cancel', ['missionary/update-repository'], ['class' => 'btn btn-primary']) ?>
                <?= $synced ? 
                    Html::submitButton('Unsync', [
                        'method' => 'POST',
                        'class' => 'btn btn-primary',
                        'name' => 'continue',
                    ]) : NULL; ?>
            </div>
        </div>
        <?php $form = ActiveForm::end(); ?>

    <?php } ?>

</div>