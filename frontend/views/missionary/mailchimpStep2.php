<?php

use common\widgets\Alert;
use kartik\select2\Select2;
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
	<h2>Step 2 of 2</h2>

	<?php if ($msg == NULL) { ?>
		
		<p>Select which of your Mailchimp mailing lists you would like to sync to IBNet:</p>
		<?php $form = ActiveForm::begin(); ?>
    	<div class="row">
    	    <div class = "col-md-4">
				<?= $form->field($mcList, 'select')->widget(Select2::classname(), [
    			    'data' => $listArray,
    			    'theme' => 'krajee',
    			    'options' => [
    			        'placeholder' => 'Select mailing list(s) ...', 
    			        'multiple' => true,
    			    ],
    			    'pluginOptions' => ['allowClear' => true],
    			]) ?>
    	    </div>
    	</div>
    	<div class="row">
    	    <div class = "col-md-4">
                <?= Html::a('Cancel', ['missionary/update-repository'], ['class' => 'btn btn-primary top-margin']) ?>
    	        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary top-margin']) ?>
    	    </div>
    	</div>
    	<?php $form = ActiveForm::end(); ?>
    
    <?php } else { ?>

        <div class="top-margin">
    	   <p><?= $msg ?></p>
        </div>
    	<?= Html::a('OK', ['missionary/update-repository'], ['class' => 'btn btn-primary top-margin']) ?>

    <?php } ?>

</div>