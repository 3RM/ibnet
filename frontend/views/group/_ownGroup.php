<?php

use common\models\group\Group;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
$pending = $group->private ? $group->getPending() : NULL;
$newMembers = $group->private ? NULL : $group->newMembers;
/* @var $this yii\web\View */
?>

<div class="group-card">
    <div class="group-card-header">
        <div class="group-header-left"></div>
        <div class="group-header-right"></div>
    </div>
    <div class="group-card-body">
        <div class="group-info">
            <div class="picture-name">
                <?= empty($group->image) ? Html::img('@img.profile/profile-logo.png', ['class' => '']) : Html::img($group->image, ['class' => '']) ?>
                <h1><?= Html::a($group->name . '&nbsp' . Html::icon('new-window', ['class' => 'internal-link']), ['group/dashboard', 'id' => $group->id]) ?></h1>
            </div>
        </div>
        <div class="group-links">
            <?php ActiveForm::begin(); ?>
            <p><?= Html::a(Html::icon('check') . ' Manage Members ', ['group/group-members', 'id' => $group->id]); ?>
                <?= $pending ? '<span class="badge" style="background-color:#da0017">' . $pending . '</span></p>' : NULL ?>
                <?= $newMembers ? '<span class="badge" style="background-color:#05aa36">' . $newMembers . '</span></p>' : NULL ?>
            </p>
            <p><?= Html::button(Html::icon('user') . ' Invite New Members', ['id' => 'invite-id', 'class' => 'link-btn']); ?></p>
            <p><?= group::STATUS_ACTIVE == $group->status ?
                 Html::a(Html::icon('edit') . ' Group Settings', ['group/group-information', 'id' => $group->id]) :
                 Html::a(Html::icon('edit') . ' Activate', ['group/group-information', 'id' => $group->id]) ?>
             </p>
            <?= (Group::STATUS_ACTIVE == $group->status) ?
                HTML::submitButton(Html::icon('ban-circle') . ' Disable', [
                    'method' => 'post',
                    'class' => 'link-btn',
                    'name' => 'disable',
                    'value' => $group->id
                ]) :
                HTML::submitButton(Html::icon('trash') . ' Trash', [
                    'method' => 'post',
                    'onclick' => 'return confirm("Are you sure you want to permanently delete this group?")',
                    'class' => 'link-btn',
                    'name' => 'trash',
                    'value' => $group->id
                ]);
            ?>
            <p><?= Html::a(Html::icon('transfer') . ' Transfer', ['group/transfer', 'id' => $group->id]) ?></p>                      
            <div class="group-status">
                <hr>
                <?php switch ($group->status) {
                    case Group::STATUS_NEW:
                        echo '<p>Status: <span style="color:#337ab7"> New</p>';
                        break;
                    case Group::STATUS_ACTIVE:
                        echo '<p>Status: <span style="color:green"> Active</span></p>';
                        break;
                    case Group::STATUS_INACTIVE:
                        echo '<p>Status: <span style="color:orange"> Inactive</p>';
                        break;
                    default:break;
                } ?>
                <p>Created: <?= Yii::$app->formatter->asDate($group->created_at) ?></p>
            </div>
            <?php ActiveForm::end(); ?>
            <?php $this->registerJS("$('#invite-id').click(function(e) {
                $.get('/group/invite', {id:" . $group->id . "}, function(data) {
                    $('#invite-modal').modal('show').find('#invite-content').html(data);
                })
            });", \yii\web\View::POS_READY); ?>
        </div>
    </div>
</div>