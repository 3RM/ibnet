<?php

use common\widgets\Alert;
use kartik\checkbox\CheckboxX;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use yii\grid\GridView;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'My Account';
?>
<div class="wrap my-profiles">
    <div class="container">
        <div class="row">
            <h1><?= Html::encode($this->title) ?></h1>

            <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'Profiles',  
                        'url' => ['//profile-mgmt/my-profiles'],
                    ],
                    [
                        'label' => 'Settings',
                        'active' => true,
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
<div class="clearprofiles"></div>
<?= Alert::widget() ?>

<div class="profile-owner-index my-settings">
    <div class="container">
        
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <h3>Username</h3>
            <a href="#" id="current-username">Update <span class="glyphicon glyphicon-triangle-bottom"></span></a>
        </div>
        <div class="row update-username" style="display: none;">
            <div class="col-md-6 update">
                <p><?= $account->currentUsername ?></p>
                <?= $form->field($account, 'username')->textInput(['maxlength' => true]) ?>
                
                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'updateUsername']) ?>
                </div>
            </div>
            <div class="col-md-6"></div>
        </div>
        <?php $form = ActiveForm::end(); ?>

        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <h3>Email</h3>
            <a href="#" id="current-email">Update <span class="glyphicon glyphicon-triangle-bottom"></span></a>
        </div>
        <div class="row update-email" style="display: none;">
            <div class="col-md-6 update">
                <p><?= $account->email ?></p>
                <?= $form->field($account, 'newEmail')->textInput(['maxlength' => true]) ?>
                
                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'updateEmail']) ?>
                </div>
            </div>
            <div class="col-md-6"></div>
        </div>
        <?php $form = ActiveForm::end(); ?>

        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <h3>Password</h3>
            <a href="#" id="current-password"> Update <?= Html::icon('triangle-bottom') ?></a>
        </div>
        <div class="row update-password" <?= 'style="display:' . $account->toggle . ';"' ?>>
            <div class="col-md-6 update">
                <?= $form->field($account, 'currentPassword')->passwordInput(['maxlength' => true]) ?>
                <?= $form->field($account, 'newPassword')->passwordInput(['maxlength' => true]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'updatePass']) ?>
                </div>
            </div>
            <div class="col-md-6"></div>
        </div>
        <?php $form = ActiveForm::end(); ?>

        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <h3>Email Preferences</h3>
            <a href="#" id="current-preferences"> View & Update <?= Html::icon('triangle-bottom') ?></a>
        </div>
        <div class="row update-preferences" style="display: none;">
            <div class="col-md-6 update">
                <?= $form->field($account, 'emailMaintenance')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'disabled'=>true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($account, 'emailPrefProfile')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                    'theme' => 'krajee-flatblue',
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($account, 'emailPrefLinks')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($account, 'emailPrefFeatures')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'updatePreferences']) ?>
                </div>
            </div>
            <div class="col-md-6"></div>
        </div>
        <?php $form = ActiveForm::end(); ?>
        
        <div class="row">
            <div class="col-md-12">
                <p>&nbsp;</p>
            </div>
        </div>
    </div>
</div>