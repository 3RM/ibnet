<?php

use common\models\group\Group;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<div id=<?= '"item-' . $model->id . '"' ?> class="prayer-item">
	<p class="request"><?= $model->request ?>
        <?php if ($model->group_member_id == $nmid) {
            echo '&nbsp;';
            echo Html::button(Html::icon('pencil'), ['id' => 'update-' . $model->id, 'class' => 'link-btn', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Add Update']);
            echo Html::button('<span class="glyphicons glyphicons-message-in"></span>', ['id' => 'answer-' . $model->id, 'class' => 'link-btn', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Answered']);
            echo Html::a(Html::icon('remove'), ['ajax/delete-prayer', 'id' => $model->id], [
                    'id' => 'prayer-rm-link-' . $model->id,
                    'data-on-done' => 'prayerDone',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => 'Remove',
                ]);
        } ?>
        <?php $this->registerJS("$('#update-" . $model->id . "').click(function(e) {
            $.get('/group/update-prayer', {id: " . $model->group_id . ", pid: " . $model->id . "}, function(data) {
                $('#update-prayer-modal').modal('show').find('#update-prayer-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <?php $this->registerJS("$('#answer-" . $model->id . "').click(function(e) {
            $.get('/group/answer-prayer', {id: " . $model->group_id . ", pid: " . $model->id . "}, function(data) {
                $('#answer-prayer-modal').modal('show').find('#answer-prayer-content').html(data);
            })
        });", \yii\web\View::POS_READY); ?>
        <?php $this->registerJs("$('#prayer-rm-link-" . $model->id . "').click(function(e) {
            if (confirm('Are you sure you want to delete this prayer?')) {
                handleAjaxSpanLink(e);
            } else { 
                return false;
            }
        });", \yii\web\View::POS_READY); ?>
    </p>
    <p class="name-date"> <?= Html::a($model->fullName, 'mailto:' . $model->email) . ', ' . Yii::$app->formatter->asDate($model->created_at) ?></p> 
    <div class="duration"
        <?php switch ($model->duration) {
            case '10': echo 'style="background-color:#ff3c3c"'; break;
            case '20': echo 'style="background-color:orange"'; break;
            case '30': echo 'style="background-color:lightblue"'; break;
            case '40': echo 'style="background-color:darkblue"'; break;
            default: break;
        } ?>>
    </div>
	<?= $model->description ? '<p class="prayer-description">' . $model->description . '</p>' : NULL; ?>
    <?php foreach ($model->prayerUpdates as $update) {
        echo '<div id="update-' . $update->id . '" class="update">';
        echo    '<span class="glyphicons glyphicons-note-empty"></span>
                <p class="name-date">' . Yii::$app->formatter->asDate($update->created_at); 
        if ($model->group_member_id == $nmid) {
            echo '&nbsp;' .
                    Html::a(Html::icon('remove'), ['ajax/delete-update', 'id' => $update->id], [
                        'id' => 'update-rm-link-' . $update->id,
                        'data-on-done' => 'updateDone',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'Remove',
                    ]) .
                '</p>';
                $this->registerJs("$('#update-rm-link-" . $update->id . "').click(function(e) {
                    if (confirm('Are you sure you want to delete this update?')) {
                        handleAjaxSpanLink(e);
                    } else { 
                        return false;
                    }
                });", \yii\web\View::POS_READY);
        }
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