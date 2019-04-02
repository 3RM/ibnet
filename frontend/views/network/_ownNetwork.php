<?php

use common\models\network\Network;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<div class="network-card">
    <div class="network-card-header">
        <div class="network-header-left"></div>
        <div class="network-header-right"></div>
    </div>
    <div class="network-card-body">
        <div class="network-info">
            <div class="picture-name">
                <?= empty($network->network_image) ? Html::img('@img.profile/profile-logo.png', ['class' => '', 'alt' => 'Logo Image']) : Html::img($network->network_image, ['class' => '', 'alt' => 'Logo image']) ?>
                <h1><?= Html::a($network->name . '&nbsp' . Html::icon('new-window', ['class' => 'internal-link']), ['network/dashboard', 'id' => $network->id, ['target' => '_blank']]) ?></h1>
            </div>
        </div>
        <div class="network-links">
            <?php ActiveForm::begin(); ?>
            <p><?= Html::a(Html::icon('check') . ' Manage Members ', ['network/network-members', 'id' => $network->id]); ?><span class="label label-danger">4</span></p>
            <?= Html::button(Html::icon('user') . ' Invite New Members', ['id' => 'invite-id', 'class' => 'link-btn']); ?>
            <p><?= Network::STATUS_ACTIVE == $network->status ?
                 Html::a(Html::icon('edit') . ' Network Settings', ['network/network-information', 'id' => $network->id]) :
                 Html::a(Html::icon('edit') . ' Activate', ['network/network-information', 'id' => $network->id]) ?>
             </p>
            <?= (Network::STATUS_ACTIVE == $network->status) ?
                HTML::submitButton(Html::icon('ban-circle') . ' Disable', [
                    'method' => 'post',
                    'class' => 'link-btn',
                    'name' => 'disable',
                    'value' => $network->id
                ]) :
                HTML::submitButton(Html::icon('trash') . ' Trash', [
                    'method' => 'post',
                    'onclick' => 'return confirm("Are you sure you want to permanently delete this network?")',
                    'class' => 'link-btn',
                    'name' => 'trash',
                    'value' => $network->id
                ]);
            ?>
            <p><?= Html::a(Html::icon('transfer') . ' Transfer', ['network/transfer', 'id' => $network->id]) ?></p>                      
            <div class="network-status">
                <hr>
                <?php switch ($network->status) {
                    case Network::STATUS_NEW:
                        echo '<p>Status: <span style="color:#337ab7"> New</p>';
                        break;
                    case Network::STATUS_ACTIVE:
                        echo '<p>Status: <span style="color:green"> Active</span></p>';
                        break;
                    case Network::STATUS_INACTIVE:
                        echo '<p>Status: <span style="color:orange"> Inactive</p>';
                        break;
                    default:break;
                } ?>
                <p>Created: <?= Yii::$app->formatter->asDate($network->created_at) ?></p>
            </div>
            <?php ActiveForm::end(); ?>
            <?php $this->registerJS("$('#invite-id').click(function(e) {
                $.get('/network/invite', {id:" . $network->id . "}, function(data) {
                    $('#invite-modal').modal('show').find('#invite-content').html(data);
                })
            });", \yii\web\View::POS_READY); ?>
        </div>
    </div>
</div>