<?php

use common\widgets\Alert;
use frontend\assets\GroupAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

GroupAsset::register($this);
$this->title = 'Notification';
?>

<?= $this->render('../../site/_userAreaLeftNav', ['active' => 'notification', 'gid' => $group->id, 'role' => $role, 'joinedGroups' => $joinedGroups]) ?>

<div class="right-content">
    
    <div class="notice-container">
        <?= Alert::widget() ?>
        
        <p>
            <i class="fas fa-info-circle"></i> Use this form to send an email notification to every group member.  You can also send an email to 
            <span style="color:blue;"><?= $group->notice_email ? $group->notice_email : '<i>Group Email Pending</i>' ?></span>. Any replies will 
            also be forwarded to the entire group. This feature is not intended to be a mailing list.  Sustained discussions are better handled 
            in the group forum.
        </p>
        
        <p class="top-margin-40"></p>
        
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($group, 'subject')->textInput()->label('Subject') ?>
        <?= $form->field($group, 'message')->textArea(['rows' => 6])->label('Message') ?>
        <?= Html::submitButton('Send', [
            'method' => 'POST',
            'class' => 'btn btn-primary longer',
            'name' => 'send'
        ]); ?>
        <?php $form = ActiveForm::end(); ?>   
    </div> 
    
</div>