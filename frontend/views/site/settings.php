<?php

use common\models\User;
use kartik\checkbox\CheckboxX;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
Url::Remember();
?>
<?= $this->render('_userAreaHeader', ['active' => 'settings']) ?>
<div class="container">
    <?= $this->render('../site/_userAreaLeftNav', ['active' => 'settings', 'gid' => NULL, 'role' => $role, 'joinedGroups' => $joinedGroups]) ?>

    <div class="right-content">
        <h2>Personal Profile Settings</h2>
        <div class="personal-settings">
            <?= empty($userP->usr_image) ?
                Html::img('@img.site/user.png', ['class' => 'img-circle']) :
                Html::img($userP->usr_image, ['class' => 'img-circle']); ?>
            <div class="personal-info">
                <p class="lead"><b>Display Name:</b> <?= $userP->display_name ?></p>
                <p class="lead"><b>Home Church:</b> <?= $home_church ?></p>
                <p class="lead"><b>Primary Role:</b> <?= $userP->primary_role ?></p>
                <p class="lead"><b>Joined:</b> <?= Yii::$app->formatter->asDate($userP->created_at, 'php:F Y') ?></p>
            </div> 
        </div>
        <div class="settings-form-link">
            <a href="#personal-settings" id="personal-settings">edit<span class="glyphicon glyphicon-triangle-bottom small"></span></a>
        </div>

        <?php $form = ActiveForm::begin([
            'action' => 'personal-settings', 
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
        <div class="row edit-personal" style="display: none;">
            <div class="col-md-6 settings-form">
                    
                <?= $form->field($userP, 'display_name')->textInput(['maxlength' => true]) ?>
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
                            'url' => Url::to(['ajax/search']),
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
                
                <?= $form->field($userP, 'primary_role')->widget(Select2::classname(), [
                    'data' => $list,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'hideSearch' => true,
                    'options' => ['placeholder' => 'Select ...',],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Primary Role'); ?>
                                         
                <div style="width:160px">
                    <?= $form->field($userP, 'usr_image')->widget(\sadovojav\cutter\Cutter::className(), [
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
                <p style="margin-top:-20px">Max 4MB</p>

                <?= Html::submitButton('Save', [
                    'method' => 'post',
                    'class' => 'btn btn-primary', 
                    'name' => 'personal'
                ]) ?>

            </div>
        </div>
        <?php $form = ActiveForm::end(); ?>

        <div class="settings-lock">
            <?= $userP->isSafeUser ? 
                NULL : '<p>' . Html::icon('lock') . ' Identify your home church from the directory to unlock additional features!</p>'
            ?>
            <?= ($userP->isPrimaryRoleMissionary && !$userP->isMissionary) ?
                '<p>' . Html::icon('lock') . ' Create a missionary profile to unlock additional missionary features!</p>' : NULL
            ?>
        </div>

        <hr>

        <h2>Account Settings</h2>

        
            <h4><b>Username:</b> <?= $userA->username ?></h4>
            <h4><b>Email:</b> <?= $userA->email ?></h4>
            <h4><b>Password:</b> <span class="pwd"></span></h4>
            <h4><b>Timezone:</b> <?= $userA->timezone ?></h4>
            <h4><b>Email Preferences:</b> 
                <span class="fa fa-check-square-o"></span>
                <?= $userA->subscriptionProfile ? 
                    '<span class="fa fa-check-square-o"></span>' : '<span class="fa fa-square-o"></span>' ?>
                <?= $userA->subscriptionLinks ?
                    '<span class="fa fa-check-square-o"></span>' : '<span class="fa fa-square-o"></span>' ?>
                <?= $userA->subscriptionComments ?
                    '<span class="fa fa-check-square-o"></span>' : '<span class="fa fa-square-o"></span>' ?>
                <?= $userA->subscriptionFeatures ?
                    '<span class="fa fa-check-square-o"></span>' : '<span class="fa fa-square-o"></span>' ?>
                <?= $userA->subscriptionBlog ?
                    '<span class="fa fa-check-square-o"></span>' : '<span class="fa fa-square-o"></span>' ?>
            </h4>
     
                
        <div class="settings-form-link">
            <a href="#account-settings" id="account-settings">edit<span class="glyphicon glyphicon-triangle-bottom small"></span></a>
        </div>

        <?php $form = ActiveForm::begin(['action' => 'account-settings']); ?>

        <div class="row edit-account" style="display: none;">
            <div class="col-md-6 settings-form">

                <h3 class="top-margin-10">Login ID</h3>

                <p class="top-margin-10">Username: <?= $userA->username ?></p>
                <?= $form->field($userA, 'newUsername', ['options' => ['class' => "no-label-32"]])->textInput(['maxlength' => true, 'placeholder' => 'New username']) ?>

                <p class="top-margin-10">Email: <?= $userA->email ?></p>
                <?= $form->field($userA, 'newEmail', ['options' => ['class' => "no-label-32"]])->textInput(['maxlength' => true, 'placeholder' => 'New email']) ?>

                <h3 class="top-margin-28">Password</h3>
                <?= $form->field($userA, 'currentPassword', ['options' => ['class' => "no-label"]])->passwordInput(['maxlength' => true, 'placeholder' => 'Current password']) ?>
                <?= $form->field($userA, 'newPassword', ['options' => ['class' => "no-label"]])->passwordInput(['maxlength' => true, 'placeholder' => 'New password']) ?>

                <h3 class="top-margin-28">Timezone</h3>
                <?= $form->field($userA, 'timezone')->widget(\yiidreamteam\widgets\timezone\Picker::className(), ['options' => ['class' => 'input form-control no-label']]) ?>

                <h3 class="top-margin-28">Email Preferences</h3>
                <?= $form->field($userA, 'emailMaintenance', ['options' => ['class' => 'top-margin-10']])->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'disabled'=>true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($userA, 'subscriptionProfile')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                    'theme' => 'krajee-flatblue',
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($userA, 'subscriptionLinks')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($userA, 'subscriptionComments')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($userA, 'subscriptionFeatures')->widget(CheckboxX::classname(), [
                    'initInputType' => CheckboxX::INPUT_CHECKBOX,
                    'autoLabel' => true,
                    'pluginOptions'=>[
                        'theme' => 'krajee-flatblue',
                        'enclosedLabel' => true,
                        'threeState'=>false, 
                    ]
                ])->label(false) ?>
                <?= $form->field($userA, 'subscriptionBlog')->widget(CheckboxX::classname(), [
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
              
    </div>
</div>


<script src="https://use.fontawesome.com/1db1e4efa2.js"></script>
