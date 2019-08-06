<?php

use backend\models\BanMeta;
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

    <?php foreach ($history as $event) { ?>
        <p>
            <?= $event->action == BanMeta::ACTION_BAN ?
                '<span class="Banned">Ban</span>' :
                '<span class="Active">Restore</span>';
            ?>
            <?php if ($event->action == BanMeta::ACTION_BAN) { ?>
                <?= isset($event->user_id) ? ' (user banned) ' : NULL; ?>
            <?php } else { ?>
                <?= isset($event->user_id) ? ' (user restored) ' : NULL; ?>
            <?php } ?>
            <?= '<span class="ban-history-date">' . Yii::$app->formatter->asDate($event->created_at, 'php:Y-m-d') . '</span>'; ?>
            <br>
            <?= $event->description ?>
        </p>
    <?php } ?>
    
</div>