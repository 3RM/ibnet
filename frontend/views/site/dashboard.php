<?php

use common\widgets\Alert;
use kartik\checkbox\CheckboxX;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

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
                        'label' => 'Dashboard',
                        'active' => true,
                    ],
                    [
                        'label' => 'Profiles',  
                        'url' => ['//profile-mgmt/my-profiles'],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
<div class="clearprofiles"></div>
<?= Alert::widget() ?>

<div class="profile-owner-index dashboard">
    <div class="container">

        <div class="row top-margin">
            <div class="col-md-4">
                <h2>Personal Settings</h2>
            </div>
        </div>

        <div class="row top-margin-28">
            <div class="col-md-2 top-margin center">
                <?= empty($userP->image) ?
                    Html::img('@web/images/user.png', ['class' => 'img-circle']) :
                    Html::img($userP->image, ['class' => 'img-circle']); ?>
            </div>
            <div class="col-md-8" style="padding-left:60px">
                <h4 class="lead"><b>Screen Name:</b> <?= $userP->screen_name ?></h4>
                <h4 class="lead"><b>Home Church:</b> <?= $home_church ?></h4>
                <h4 class="lead"><b>Primary Role:</b> <?= $userP->role ?></h4>
                <h4 class="lead"><b>Joined:</b> <?= Yii::$app->formatter->asDate($userP->created_at, 'php:F Y') ?></h4>
            </div>
        </div>
                
        <div class="row">
            <a href="#personal-settings" id="personal-settings">edit<span class="glyphicon glyphicon-triangle-bottom tiny"></span></a>
        </div>

        <?php $form = ActiveForm::begin([
            'action' => 'personal-settings', 
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>

        <div class="row edit-personal" style="display: none;">
            <div class="col-md-6 personal">
                
                <?= $form->field($userP, 'screen_name')->textInput(['maxlength' => true]) ?>
                <?php echo $form->field($userP, 'home_church')->widget(Select2::classname(), [ 
                    'options' => ['placeholder' => 'Search by name or city...'],
                    'initValueText' => $home_church ? $home_church : 'Search ...', 
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['profile-form/church-list-ajax']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(profile) { 
                            if(profile.org_city > "" && profile.org_st_prov_reg > "") {
                                return profile.text+", "+profile.org_city+", "+profile.org_st_prov_reg;
                            } else {
                                return profile.text;
                            };
                        }'),
                        'templateSelection' => new JsExpression('function (profile) { 
                            if(profile.org_city > "" && profile.org_st_prov_reg > "") {
                                return profile.text+", "+profile.org_city+", "+profile.org_st_prov_reg;
                            } else {
                                return profile.text;
                            };
                        }'),
                    ],
                ]); ?>
                <!-- <p class="small"><?= Html::icon('info-sign') ?> You must identify a home church before you can post comments on this site.</p> -->
                
                <?= $form->field($userP, 'role')->widget(Select2::classname(), [
                    'data' => $list,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Select ...',],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Primary Role'); ?>
                                         
                <div style="width:160px">
                    <?= $form->field($userP, 'image')->widget(\sadovojav\cutter\Cutter::className(), [
                        'cropperOptions' => [
                            'viewMode' => 1,    
                            'aspectRatio' => 1,           // 160px x 160px
                            'movable' => false,
                            'rotatable' => false,
                            'scalable' => false,
                            'zoomable' => false,
                            'zoomOnTouch' => false,
                            'zoomOnWheel' => false,
                        ]
                    ]); ?>
                </div>

                <?= Html::submitButton('Save', [
                    'method' => 'post',
                    'class' => 'btn btn-primary', 
                    'name' => 'personal'
                ]) ?>

            </div>
        </div>
        <?php $form = ActiveForm::end(); ?>






        <hr>







        <div class="row top-margin">
            <div class="col-md-4">
                <h2>Account Settings</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 top-margin">
                <h4><b>Username:</b> <?= $userA->username ?></h4>
                <h4><b>Email:</b> <?= $userA->email ?></h4>
                <h4><b>Password:</b> <span class="pwd"></span></h4>
                <h4><b>Email Preferences:</b> 
                        <span class="fa fa-check-square-o"></span>
                    <?= $userA->emailPrefProfile ? 
                        '<span class="fa fa-check-square-o"></span>' : '<span class="fa fa-square-o"></span>' ?>
                    <?= $userA->emailPrefLinks ?
                        '<span class="fa fa-check-square-o"></span>' : '<span class="fa fa-square-o"></span>' ?>
                    <?= $userA->emailPrefFeatures ?
                        '<span class="fa fa-check-square-o"></span>' : '<span class="fa fa-square-o"></span>' ?>
                </h4>
            </div>
        </div>
                
        <div class="row top-margin">
            <a href="#account-settings" id="account-settings">edit<span class="glyphicon glyphicon-triangle-bottom tiny"></span></a>
        </div>

        <?php $form = ActiveForm::begin(['action' => 'account-settings']); ?>

        <div class="row edit-account" style="display: none;">
            <div class="col-md-6 account">

                <h3 class="top-margin">Login ID</h3>

                <p>Username: <?= $userA->username ?></p>
                <?= $form->field($userA, 'newUsername')->textInput(['maxlength' => true, 'placeholder' => 'New username']) ?>

                <p>Email: <?= $userA->email ?></p>
                <?= $form->field($userA, 'newEmail')->textInput(['maxlength' => true, 'placeholder' => 'New email']) ?>

                <h3 class="top-margin-28">Password</h3>
                <?= $form->field($userA, 'currentPassword')->passwordInput(['maxlength' => true, 'placeholder' => 'Current password']) ?>
                <?= $form->field($userA, 'newPassword')->passwordInput(['maxlength' => true, 'placeholder' => 'New password']) ?>

                <h3 class="top-margin-28">Email Preferences</h3>
                <?= $form->field($userA, 'emailMaintenance')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'disabled'=>true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($userA, 'emailPrefProfile')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                    'theme' => 'krajee-flatblue',
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($userA, 'emailPrefLinks')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($userA, 'emailPrefFeatures')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>

                <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'personal']) ?>
            </div>
        </div>

        <?php $form = ActiveForm::end(); ?>
        
        <div class="row">
            <div class="col-md-12">
                <p>&nbsp;</p>
            </div>
        </div>
    </div>
</div>


<script src="https://use.fontawesome.com/1db1e4efa2.js"></script>
