<?php

use common\models\profile\Profile;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

$ministry = $model->ministry_id ? Profile::findProfile($model->ministry_id) : NULL;
?>

<div class="group-search-item">
	<div class="search-item-img">
		<?= Html::img($model->image) ?>

		<?php if ($model->user_id == Yii::$app->user->identity->id) {
			echo '<i class="fas fa-user-shield"></i>';
		} elseif (in_array($model->id, $aids)) {
			echo '<i class="fas fa-user-check"></i>';
		} elseif (in_array($model->id, $pids)) {
			echo '<i class="fas fa-user-plus"></i>';
		} else {
			$form = ActiveForm::begin([
				'id' => 'group-' . $model->id,
				'action' => 'my-groups'
			]);
			echo Html::submitButton('Join', [
    		    'id' => 'join-' . $model->id,
    		    'name' => 'join',
    		    'value' => $model->id,
    		    'class' => 'btn btn-primary', 
    		    'title' => 'Join Group'
    		]);
    		$form = ActiveForm::end(); 
    	} ?>

	</div>
	<div class="search-item-text">
		<h3><?= $model->name ?> <?= $model->private ? '<i class="fas fa-lock"></i>' : NULL ?></h3>
		<?= $ministry ? '<p class="ministry">A group for ' . $ministry->org_name . ', ' . $ministry->org_city . ', ' . $ministry->org_st_prov_reg . '</p>' : NULL ?>
		<p><?= $model->description ?></p>
	</div>
</div>