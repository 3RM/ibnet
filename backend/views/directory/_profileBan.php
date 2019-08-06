<?php

use common\models\profile\Profile;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>

<?= $profile->image1 ? Html::img(Yii::$app->params['url.frontend'] . $profile->image1, ['class' => 'image1']) : NULL ?>
<div class="detail-head">
    <div class="picture">
        <?= $profile->image2 ? Html::img(Yii::$app->params['url.frontend'] . $profile->image2) : Html::img('@img.profile/profile-logo.png') ?>
    </div>
    <div class="name">
        <h2><?= $profile->formatName ?></h2>
        <h5><em><?= $profile->tagline ?></em></h5>
        <?= $profile->type . ($profile->sub_type != $profile->type ? ' (' . $profile->sub_type . ')' : NULL) ?>
    </div>
</div>

<div class="detail">

    <p>
        <?= $profile->status == Profile::STATUS_BANNED ? 
            'Restore profile ' . $profile->id . ' to a state of inactive.  The user will be notified by email of this change. 
            The description is for administration use only and is not shared with the user.' :
            'Ban profile ' . $profile->id . '.  The user will be notified by email of this change. The description is for 
            administration use only and is not shared with the user.' ?>
    </p>

    <?php $form = ActiveForm::begin(['action' => '/directory/flagged']); ?>

    <?= $form->field($profile, 'select')->textArea(['rows' => 1])->label('Description') ?>
    
    <?= $profile->status == Profile::STATUS_BANNED ? 
        Html::submitButton('Restore', [
            'name' => 'restore',
            'value' => $profile->id,
            'method' => 'post',
            'class' => 'btn-main',
            'onclick' => 'return confirm("Are you sure you want to restore this profile? Click to confirm.")'
        ]) :
        Html::submitButton('Ban', [
            'name' => 'ban',
            'value' => $profile->id,
            'method' => 'post',
            'class' => 'btn-main',
            'onclick' => 'return confirm("Are you sure you want to ban this profile? Click to confirm.")'
        ]); 
    ?>

    <?php $form = ActiveForm::end(); ?>
    
</div>