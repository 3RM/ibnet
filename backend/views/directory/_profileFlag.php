<?php

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <?php $form = ActiveForm::begin(['action' => '/directory/update']); ?>

    <p>
        Flag profile <?= $profile->id ?> for inappropriate content.  After flagging the profile, it will show up in  
        <?= Html::a('Flagged Profiles', ['/directory/flagged']) ?> list where it can be further dispositioned.  A flagged 
        profile is still visible in the public directory.
    </p>

    <?= Html::submitButton('Flag', [
        'name' => 'flag',
        'value' => $profile->id,
        'class' => 'btn-main',
        'method' => 'post',
    ]); ?>

    <?php $form = ActiveForm::end(); ?>