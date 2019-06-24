<?php

use common\models\group\Group;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<?php 
    if ($index < 9) {
        $cols = 'single';
        echo '<div class="left" style="margin:10px 0 0 12px; height:auto; overflow:hidden;">';
    } elseif ($index < 99) {
        $cols = 'double';
        echo '<div class="left" style="margin:10px 0 0 6px; height:auto; overflow:hidden;">';
    }  else {
        $cols = 'triple';
        echo '<div class="left" style="margin:10px 0 0 0; height:auto; overflow:hidden;">';
    }
?>
    <div style="float:left; overflow:hidden;">
        <?= ($index+1) ?>.&nbsp;&nbsp;
    </div>
    <div style="overflow:hidden;">
        <p class="request details" style="margin-bottom:0;"><?= '<b>' . $model->request . '</b>' ?> (<?= $model->fullName . ', ' . (Yii::$app->formatter->asDate($model->created_at, 'php:Y') == date('Y') ? Yii::$app->formatter->asDate($model->created_at, 'php:M. j') : Yii::$app->formatter->asDate($model->created_at, 'php:M. j, Y')) ?>)</p>
        <p class="request details" style="margin-bottom:0; display:none;"><?= $model->request ?> (<?= $model->fullName . ', ' . (Yii::$app->formatter->asDate($model->created_at, 'php:Y') == date('Y') ? Yii::$app->formatter->asDate($model->created_at, 'php:M. j') : Yii::$app->formatter->asDate($model->created_at, 'php:M. j, Y')) ?>)</p>
        <div class="details">
            <?= $model->description ? '<div class="' . $cols .'">' . $model->description . '</div>' : NULL; ?>
            <?php if ($updates = $model->prayerUpdates) { ?>
            <ul style="margin-bottom:0;">
                <?php foreach ($updates as $update) {
                    echo '<li><i>';
                    echo    Yii::$app->formatter->asDate($update->created_at, 'php:Y') == date('Y') ? Yii::$app->formatter->asDate($update->created_at, 'php:M. j') : Yii::$app->formatter->asDate($update->created_at, 'php:M. j, Y');
                    echo    '</i>: ' . $update->update;
                    echo '</li>';
                } ?>
            </ul>
        <?php } ?>
        </div>
    </div>
</div>