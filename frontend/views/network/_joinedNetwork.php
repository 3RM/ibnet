<?php

use common\models\network\Network;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
$member = $network->networkMember;
?>

<div class="network-card">
    <div class="network-card-header">
        <div class="network-header-left"></div>
        <div class="network-header-right"></div>
    </div>
    <div class="network-card-body">
        <div class="network-info">
            <div class="picture-name joined">
                <?= empty($network->network_image) ? Html::img('@img.profile/profile-logo.png', ['class' => '', 'alt' => 'Logo Image']) : Html::img($network->network_image, ['class' => '', 'alt' => 'Logo image']) ?>
                <h1><?= Html::a($network->name . '&nbsp' . Html::icon('new-window', ['class' => 'internal-link']), ['network/dashboard', 'id' => $network->id, ['target' => '_blank']]) ?></h1>
            </div>
            <p class="network-description"><?= $network->description ?></p>
        </div>
        <div class="network-links">
            <?php ActiveForm::begin(); ?>
            <p></p>                      
            <div class="network-status">
                <?php switch ($network->status) {
                    case Network::STATUS_ACTIVE:
                        echo '<p>Status: <span style="color:green"> Active</span></p>';
                        break;
                    case Network::STATUS_INACTIVE:
                        echo '<p>Status: <span style="color:orange"> Inactive</p>';
                        break;
                    default:break;
                } ?>
                <p>Created: <?= Yii::$app->formatter->asDate($network->created_at) ?></p>
                <p>Joined: <?= Yii::$app->formatter->asDate($member->created_at) ?></p>
                <p>
                    <?= HTML::submitButton(Html::icon('remove') . ' Leave Network', [
                        'method' => 'post',
                        'onclick' => 'return confirm("Are you sure you want to leave this network?")',
                        'class' => 'link-btn',
                        'name' => 'leave',
                        'value' => $network->id
                    ]); ?>
                </p>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>