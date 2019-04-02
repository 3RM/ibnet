<?php

use common\widgets\Alert;
use frontend\assets\NetworkAsset;
use kartik\checkbox\CheckboxX;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

NetworkAsset::register($this);
$this->title = 'Network Features';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container-form">
    <?= Alert::widget() ?>

    <h2>Select the Features for your Network</h2>
    <p>Can you think of a useful feature not included here?  Let us know your suggestion!</p>

    <?php $form = ActiveForm::begin(); ?>

    <div class="feature-container">
        <div class="feature-image prayer">
            <?= $form->field($network, 'feature_prayer')->widget(CheckboxX::classname(), [
                'initInputType' => CheckboxX::INPUT_CHECKBOX,
                'autoLabel' => true,
                'pluginOptions'=>[
                    'theme' => 'krajee-flatblue',
                    'enclosedLabel' => true,
                    'threeState'=>false, 
                ]
            ])->label(false) ?>
        </div>
        <div class="feature-text">
            <h3><i class="fa fa-hand-o-up"></i> Prayer List</h3>
            <p>Create and track prayer requests and answers.  Tag requests with status.  Receive daily or weekly prayer updates.</p>
        </div>
    </div>
    <div class="feature-container">
        <div class="feature-image chat">
            <?= $form->field($network, 'feature_forum')->widget(CheckboxX::classname(), [
                'initInputType' => CheckboxX::INPUT_CHECKBOX,
                'autoLabel' => true,
                'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                    'theme' => 'krajee-flatblue',
                    'enclosedLabel' => true,
                    'threeState'=>false, 
                ]
            ])->label(false) ?>
        </div>
        <div class="feature-text">
            <h3><i class="fa fa-quote-left"></i> Discussions</h3>
            <p>A discussion forum gives you the ease of categorizing, tracking, and archiving important discussions.  You can also chat with other members directly.<p>
        </div>
    </div>
    <div class="feature-container">
        <div class="feature-image calendar">
            <?= $form->field($network, 'feature_calendar')->widget(CheckboxX::classname(), [
                'initInputType' => CheckboxX::INPUT_CHECKBOX,
                'autoLabel' => true,
                'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                    'theme' => 'krajee-flatblue',
                    'enclosedLabel' => true,
                    'threeState'=>false, 
                ]
            ])->label(false) ?>
        </div>
        <div class="feature-text">
            <h3><i class="fa fa-calendar"></i> Calendar</h3>
            <p>Keep track of important dates such as special events.</p>
        </div>
    </div>
    <div class="feature-container">
        <div class="feature-image notification">
            <?= $form->field($network, 'feature_notification')->widget(CheckboxX::classname(), [
                'initInputType' => CheckboxX::INPUT_CHECKBOX,
                'autoLabel' => true,
                'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                    'theme' => 'krajee-flatblue',
                    'enclosedLabel' => true,
                    'threeState'=>false, 
                ]
            ])->label(false) ?>
        </div>
        <div class="feature-text">
            <h3><i class="fa fa-bullhorn"></i> Notifications</h3>
            <p>Members can send email notifications to the entire network.</p>
        </div>
    </div>
    <div class="feature-container">
        <div class="feature-image document">
            <?= $form->field($network, 'feature_document')->widget(CheckboxX::classname(), [
                'initInputType' => CheckboxX::INPUT_CHECKBOX,
                'autoLabel' => true,
                'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                    'theme' => 'krajee-flatblue',
                    'enclosedLabel' => true,
                    'threeState'=>false, 
                ]
            ])->label(false) ?>
        </div>
        <div class="feature-text">
            <h3><i class="fa fa-archive"></i> Document Library</h3>
            <p>Collaborate with your network through the document library.  Upload, author, link to, and store documents and files.</p>
        </div>
    </div>
    <div class="feature-container">
        <div class="feature-image update-feature">
            <?= $form->field($network, 'feature_update')->widget(CheckboxX::classname(), [
                'initInputType' => CheckboxX::INPUT_CHECKBOX,
                'autoLabel' => true,
                'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                    'theme' => 'krajee-flatblue',
                    'enclosedLabel' => true,
                    'threeState'=>false, 
                ]
            ])->label(false) ?>
        </div>
        <div class="feature-text">
            <h3><i class="fa fa-file-text"></i> Missionary Updates</h3>
            <p>The missionary update tool is available to every missionary who has a missionary profile.  This feature makes their updates accessible to the network.</p> 
        </div>
    </div>
    <div class="feature-container">
        <div class="feature-image donation">
            <?= $form->field($network, 'feature_donation')->widget(CheckboxX::classname(), [
                'initInputType' => CheckboxX::INPUT_CHECKBOX,
                'autoLabel' => true,
                'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                    'theme' => 'krajee-flatblue',
                    'enclosedLabel' => true,
                    'threeState'=>false, 
                ]
            ])->label(false) ?>
        </div>
        <div class="feature-text">
            <h3><i class="fa fa-paypal"></i> Grace Gifts</h3>
            <p>Members can send monetary gifts to other members using popular payment gateways such as PayPal.</p>
        </div>
    </div>

    <?= $this->render('_networkFormFooter', ['network' => $network]) ?>

    <?php ActiveForm::end(); ?>
 
    </div>

</div>