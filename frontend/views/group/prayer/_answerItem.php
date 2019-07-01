<?php

use common\models\group\Group;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<div id=<?= '"item-' . $model->id . '"' ?> class="prayer-item answered">
    <p class="answer-date">Answered <?= Yii::$app->formatter->asDate($model->answer_date) ?>
        <?php if ($model->group_member_id == $nmid) {
            echo '&nbsp;';
            echo Html::a('<span class="glyphicons glyphicons-unshare"></span>', ['ajax/return-request', 'id' => $model->id], [
                    'id' => 'request-return-link-' . $model->id,
                    'data-on-done' => 'requestDone',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => 'Send back to prayer list',
                ]);
            echo Html::a(Html::icon('remove'), ['ajax/delete-request', 'id' => $model->id], [
                    'id' => 'request-rm-link-' . $model->id,
                    'data-on-done' => 'requestDone',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => 'Remove',
                ]);
        } ?>
        <?php $this->registerJs("$('#request-return-link-" . $model->id . "').click(function(e) {
            if (confirm('Are you sure you want to send this request back to the prayer list?')) {
                handleAjaxSpanLink(e);
            } else { 
                return false;
            }
        });", \yii\web\View::POS_READY); ?>
        <?php $this->registerJs("$('#request-rm-link-" . $model->id . "').click(function(e) {
            if (confirm('Are you sure you want to delete this request?')) {
                handleAjaxSpanLink(e);
            } else { 
                return false;
            }
        });", \yii\web\View::POS_READY); ?>
    </p>
    <p><?= $model->answer_description ?></p>
	<p class="request"><?= $model->request ?></p>
    <p class="name-date"> <?= Html::a($model->fullName, 'mailto:' . $model->email) . ', ' . Yii::$app->formatter->asDate($model->created_at) ?></p>
	<?= $model->description ? '<p class="prayer-description">' . $model->description . '</p>' : NULL; ?>
    <?php foreach ($model->prayerUpdates as $update) {
        echo '<div class="update">';
        echo    '<span class="glyphicons glyphicons-note-empty"></span><p class="name-date">' . Yii::$app->formatter->asDate($update->created_at) . '</p>';
        echo    '<p class="description">' . $update->update . '</p>';
        echo '</div>';
    } ?>
    <p class="tag">
        <?php foreach ($model->prayerTags as $tag) {
            echo '<span class="tag-dot">' . $tag->tag . '</span> ';
        } ?>
    </p>
</div>
<hr>