<?php

use backend\models\BanMeta;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>

    <?php foreach ($history as $event) { ?>
        <?= $event->profile_id == NULL ? 
        '<p>' : 
        '<p class="indent">'; ?> 
            <?= $event->action == BanMeta::ACTION_BAN ?
                '<span class="Banned">Ban</span>' :
                '<span class="Active">Restore</span>';
            ?> 
            <?= isset($event->profile_id) ? ' Profile ' . $event->profile_id : NULL ?>
            <?= '<span class="ban-history-date">' . Yii::$app->formatter->asDate($event->created_at, 'php:Y-m-d') . '</span>'; ?>
            <br>
            <?= $event->description ?>
        </p>
    <?php } ?>