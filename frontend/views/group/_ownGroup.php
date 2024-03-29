<?php

use common\models\group\Group;
use frontend\assets\AjaxAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

$pending = $group->private ? $group->getPendingMembers() : NULL;
$newMembers = $group->private ? NULL : $group->newMembers;
$member = $group->groupMember;
AjaxAsset::register($this);

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
                <h1><?= $group->name ?></h1>
            </div>
        </div>
        <div class="group-links">
            <?php if (Yii::$app->user->identity->isMissionary && $group->feature_update) { ?>
                <div id=<?= '"show-result-' . $group->id .'"' ?>>
                    <?= $member->show_updates ?
                        Html::button('<i class="far fa-times-circle"></i> Stop sharing updates', ['id' => 'show-updates-' . $group->id, 'class' => 'link-btn']) :
                        Html::button('<i class="far fa-check-circle"></i> Start sharing updates', ['id' => 'show-updates-' . $group->id, 'class' => 'link-btn']);
                    ?>
                </div>
                <?php $this->registerJs(
                    "$('#show-result-" . $group->id . "').on('click', '#show-updates-" . $group->id . "', function () {
                        $.ajax({
                            type: 'POST',
                            url: '" . Url::toRoute(['ajax/show-updates']) . "',
                            dataType: 'json',
                            data: jQuery.param({ mid: '" . $member->id . "', gid: '" . $group->id . "'}),
                            async: true,
                            success: function (msg) { $('#show-result-" . $group->id . "').html(msg.body); }
                        });
                    });", \yii\web\View::POS_READY); ?>
            <?php } ?>
            <?php ActiveForm::begin(); ?>
            <?= $group->feature_forum ? '<p>' . Html::a('<i class="fa fa-comments"></i>' . ' Manage Forum ', ['group/manage-forum', 'id' => $group->id]) . '</p>' : NULL ?>
            <p><?= Html::a(Html::icon('check') . ' Manage Members ', ['group/manage-members', 'id' => $group->id]); ?>
                <?= $pending ? '<span class="badge" style="background-color:#da0017">' . $pending . '</span></p>' : NULL ?>
                <?= $newMembers ? '<span class="badge" style="background-color:#05aa36">' . $newMembers . '</span></p>' : NULL ?>
            </p>
            <?= $group->status == Group::STATUS_ACTIVE ? Html::button(Html::icon('user') . ' Invite New Members', ['id' => 'invite-' . $group->id, 'class' => 'link-btn']) : NULL ?>
            <p><?= $group->status == Group::STATUS_ACTIVE ?
                 Html::a(Html::icon('edit') . ' Group Settings', ['group/group-information', 'id' => $group->id]) :
                 Html::a(Html::icon('edit') . ' Activate', ['group/group-information', 'id' => $group->id]) ?>
             </p>
            <?= (Group::STATUS_ACTIVE == $group->status) ?
                HTML::submitButton(Html::icon('ban-circle') . ' Disable', [
                    'method' => 'post',
                    'class' => 'link-btn',
                    'name' => 'disable',
                    'value' => $group->id,
                     'onclick' => 'return confirm("Are you sure you want to disable this group?")',
                ]) :
                HTML::submitButton(Html::icon('trash') . ' Trash', [
                    'method' => 'post',
                    'class' => 'link-btn',
                    'name' => 'trash',
                    'value' => $group->id,
                    'onclick' => 'return confirm("Are you sure you want to permanently delete this group?")',
                ]);
            ?>
            <p><?= Html::a(Html::icon('transfer') . ' Transfer', ['group/transfer', 'id' => $group->id]) ?></p>                      
            <hr>
            <?php switch ($group->status) {
                case Group::STATUS_NEW:
                    echo '<p class="small">Status: <span style="color:#337ab7"> New</p>';
                    break;
                case Group::STATUS_ACTIVE:
                    echo '<p class="small">Status: <span style="color:green"> Active</span></p>';
                    break;
                case Group::STATUS_INACTIVE:
                    echo '<p class="small">Status: <span style="color:orange"> Inactive</p>';
                    break;
                default:break;
            } ?>
            <p class="small">Created: <?= Yii::$app->formatter->asDate($group->created_at) ?></p>
            <?php ActiveForm::end(); ?>
            <?php $this->registerJS("$('#invite-" . $group->id . "').click(function(e) {
                $.get('/group/invite', {id:" . $group->id . "}, function(data) {
                    $('#invite-modal').modal('show').find('#invite-content').html(data);
                })
            });", \yii\web\View::POS_READY); ?>
        </div>
    </div>
</div>