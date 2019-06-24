<?php

use common\models\group\Group;
use common\models\Utility;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<div class="update-item">
        
    <div class="container">
        <?= Html::a(Html::img($model->user->avatar, ['class' => 'avatar']), ['/profile/missionary', 'id' => $model->groupMemberProfile->id, 'urlLoc' => $model->groupMemberProfile->url_loc, 'urlName' => $model->groupMemberProfile->url_name], ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?>
        <div class="name-date">
            <p class="name"><?= Html::a($model->user->fullName . ' - ' . $model->missionary->field, ['/profile/missionary', 'id' => $model->groupMemberProfile->id, 'urlLoc' => $model->groupMemberProfile->url_loc, 'urlName' => $model->groupMemberProfile->url_name], ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?></p>
            <p><?= Yii::$app->formatter->asDate($model->from_date, 'php:F j, Y'); ?></p>
        </div>
    </div>
    
    <?php
        if ($model->mailchimp_url) {
            echo $this->render('cards/_card-mailchimp', ['update' => $model]);
        } elseif ($model->pdf) {
            echo $this->render('cards/_card-pdf', ['update' => $model]);
        } elseif ($model->vimeo_url || $model->youtube_url) {
            echo $this->render('cards/_card-video', ['update' => $model]);
        }
    ?>

</div>