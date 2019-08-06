<?php

use common\models\User;
use common\models\group\Group;
use common\models\Utility;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
?>

<p>Group <?= $group->id ?></p>

<?php $form = ActiveForm::begin(['action' => '/groups/update']); ?>

<?= $form->field($group, 'name')->textInput(['maxlength' => true]) ?> 
<?= $form->field($group, 'image')->textInput(['maxlength' => true])->label('Image') ?> 
<?= $form->field($group, 'group_level')->dropDownList([
    Group::LEVEL_LOCAL => 'Local',
    Group::LEVEL_REGIONAL => 'Regional',
    Group::LEVEL_STATE => 'State',
    Group::LEVEL_NATIONAL => 'National',
    Group::LEVEL_INTERNATIONAL => 'International',
]) ?>
<?= $form->field($group, 'description')->textArea(['rows' => 3]) ?> 
<?= $form->field($group, 'hide_on_profiles')->checkbox() ?>
<?= $form->field($group, 'not_searchable')->checkbox() ?>
<?= $form->field($group, 'ministry_id')->textInput(['maxlength' => true]) ?> 
<?= $form->field($group, 'discourse_group_name')->textInput(['maxlength' => true]) ?> 
<?= $form->field($group, 'discourse_group_id')->textInput(['maxlength' => true]) ?> 
<?= $form->field($group, 'discourse_category_id')->textInput(['maxlength' => true]) ?> 
<?= $form->field($group, 'feature_prayer')->checkbox()->label('Prayer Feature') ?>
<?= $form->field($group, 'feature_calendar')->checkbox()->label('Calendar Feature') ?>
<?= $form->field($group, 'feature_forum')->checkbox()->label('Forum Feature') ?>
<?= $form->field($group, 'feature_update')->checkbox()->label('Update Feature') ?>
<?= $form->field($group, 'feature_notification')->checkbox()->label('Notification Feature') ?>
<?= $form->field($group, 'feature_document')->checkbox()->label('Document Share Feature') ?>
<?= $form->field($group, 'feature_donation')->checkbox()->label('Grace Giving Feature') ?>
<?= $form->field($group, 'prayer_email')->textInput() ?>
<?= $form->field($group, 'prayer_email_pwd')->textInput() ?>
<?= $form->field($group, 'notice_email')->textInput() ?>
<?= $form->field($group, 'notice_email_pwd')->textInput() ?>

<?= Html::submitButton('Save', [
    'name' => 'save',
    'value' => $group->id,
    'method' => 'post',
    'class' => 'btn-main',
    'onclick' => 'return confirm("Be careful! You are updating user data. Do you have admin and/or user authorization to make changes? Click to confirm.")'
]); ?> 

<?php $form = ActiveForm::end(); ?>