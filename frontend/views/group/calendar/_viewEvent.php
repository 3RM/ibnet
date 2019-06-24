<?php

use common\models\group\Group;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

?>

<div class="view-event">
	<h4><?= $viewEvent->title ?></h4>
	<p class="date"><?= $viewEvent->dates ?></p>
	<?= $viewEvent->description ? '<p>' . $viewEvent->description . '</p>' : NULL; ?>
	<div class="links">
        <?php $form = ActiveForm::begin(['action' => '/group/remove-event']); ?>
		<?php if ($isOwner && ($resourceId == Group::RESOURCE_GROUP)) { 
            echo Html::button(Html::icon('edit'), [
                'id' => 'edit-event-btn',
                'class' => 'link-btn',
                'data-toggle' => 'tooltip', 
                'data-placement' => 'top', 
                'title' => 'Edit Event',
                'value' => $viewEvent->id,
            ]);
			echo Html::submitButton(Html::icon('remove'), [
                'class' => 'remove link-btn',
                'name' => 'remove',
                'value' => $viewEvent->id,
                'data-toggle' => 'tooltip', 
                'data-placement' => 'top', 
                'title' => 'Remove Event',
                'onclick' => 'return confirm("You are about to delete this event.  Click to confirm.")'
            ]);
		} ?>
        <?php $form = ActiveForm::end(); ?>
	</div>
</div>