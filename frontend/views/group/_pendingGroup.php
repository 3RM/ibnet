<?php

use common\models\group\Group;
use frontend\assets\AjaxAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

$member = $group->pendingGroupMember ?? NULL;
/* @var $this yii\web\View */
?>

<div class="group-card">
    <div class="group-card-header">
        <div class="group-header-left"></div>
        <div class="group-header-right"></div>
    </div>
    <div class="group-card-body">
        <div class="group-info">
            <div class="picture-name joined">
                <?= empty($group->image) ? Html::img('@img.profile/profile-logo.png', ['class' => '']) : Html::img($group->image, ['class' => '']) ?>
                <h1><?= $group->name ?></h1>
            </div>
            <p class="group-description"><?= $group->description ?></p>
        </div>
        <div class="group-links">
            <?php ActiveForm::begin(); ?>
            <p></p>                      
            <?php switch ($group->status) {
                case Group::STATUS_ACTIVE:
                    echo '<p class="small">Status: <span style="color:green"> Active</span></p>';
                    break;
                case Group::STATUS_INACTIVE:
                    echo '<p class="small">Status: <span style="color:orange"> Inactive</p>';
                    break;
                default:break;
            } ?>
            <p class="small">Created: <?= Yii::$app->formatter->asDate($group->created_at) ?></p>
            <p class="small">Requested: <?= Yii::$app->formatter->asDate($member->created_at) ?></p>
            <p></p>
            <?= HTML::submitButton(Html::icon('remove') . ' Cancel Request', [
                'method' => 'post',
                'onclick' => 'return confirm("Are you sure you want to leave this group?")',
                'class' => 'link-btn',
                'name' => 'leave',
                'value' => $group->id
            ]); ?>
        <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>