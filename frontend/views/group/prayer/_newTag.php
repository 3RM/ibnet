<?php

use common\models\group\Group;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */ 
?>

<div id="add-tag">
<?php $form = ActiveForm::begin(['id' => 'new-tag-form', 'action' => '/group/new-tag']); ?>
<?= $form->field($tag, 'tag')->textInput(['maxlength' => true]) ?>
<?= $form->field($tag, 'group_id')->hiddenInput(['maxlength' => true])->label(false) ?>
<?= Html::submitButton('Save', [
    'id' => 'submit-tag',
    'method' => 'POST',
    'class' => 'btn btn-primary',
    'name' => 'main',
]) ?>
<?php $form = ActiveForm::end(); ?>
<div class="modal-footer">
    <?php foreach ($tagList as $tagItem) {
       echo '<div id="tag-' . $tagItem->id . '" class="tag-row">';
            echo $tagItem->tag . ' ' . Html::a(Html::icon('remove'), ['ajax/delete-tag', 'tid' => $tagItem->id], [
               'id' => 'tagitem-' . $tagItem->id, 
               'data-on-done' => 'tagDone']) . '<br>';
       echo '</div>';
       $this->registerJs("$('#tagitem-" . $tagItem->id . "').click(handleAjaxSpanLink);", \yii\web\View::POS_READY);
    } ?>
</div>
</div>