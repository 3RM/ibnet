<?php

use common\models\group\Group;
use frontend\assets\AjaxAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
$member = $group->groupMember;
AjaxAsset::register($this);
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
                <h1><?= Html::a($group->name . '&nbsp' . Html::icon('new-window', ['class' => 'internal-link']), ['group/dashboard', 'id' => $group->id]) ?></h1>
            </div>
            <p class="group-description"><?= $group->description ?></p>
        </div>
        <div class="group-links">
            <?php ActiveForm::begin(); ?>
            <p></p>                      
            <div class="group-status">
                <?php switch ($group->status) {
                    case Group::STATUS_ACTIVE:
                        echo '<p>Status: <span style="color:green"> Active</span></p>';
                        break;
                    case Group::STATUS_INACTIVE:
                        echo '<p>Status: <span style="color:orange"> Inactive</p>';
                        break;
                    default:break;
                } ?>
                <p>Created: <?= Yii::$app->formatter->asDate($group->created_at) ?></p>
                <p>Joined: <?= Yii::$app->formatter->asDate($member->created_at) ?></p>

                <?php if (Yii::$app->user->identity->isMissionary && $group->feature_update) { ?>
                    <div id="show-result">
                        <?= $member->show_updates ?
                            Html::button('<i class="far fa-times-circle"></i> Stop sharing updates', ['id' => 'show-updates', 'class' => 'link-btn']) :
                            Html::button('<i class="far fa-check-circle"></i> Start sharing updates', ['id' => 'show-updates', 'class' => 'link-btn']);
                        ?>
                    </div>
                    <?php $this->registerJs(
                        "$('#show-result').on('click', '#show-updates', function () {
                            $.ajax({
                                type: 'POST',
                                url: '" . Url::toRoute(['ajax/show-updates']) . "',
                                dataType: 'json',
                                data: jQuery.param({ mid: '" . $member->id . "'}),
                                async: true,
                                success: function (msg) { $('#show-result').html(msg.body); }
                            });
                        });", \yii\web\View::POS_READY); ?>
                <?php } ?>

                <?= HTML::submitButton(Html::icon('remove') . ' Leave Group', [
                    'method' => 'post',
                    'onclick' => 'return confirm("Are you sure you want to leave this group?")',
                    'class' => 'link-btn',
                    'name' => 'leave',
                    'value' => $group->id
                ]); ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>