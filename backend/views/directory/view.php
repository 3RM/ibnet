<?php

/* @var $this yii\web\View */

use common\models\profile\Profile;
use yii\bootstrap\Html;
use yii\widgets\DetailView;

$this->title = 'View Ministry Profile';
?>

<div class="site-index">

	<?= $model->status === Profile::STATUS_ACTIVE ? 
        Html::a(Html::icon('new-window'), ['frontend/profile/view-profile-by-id', 'id' => $model->id], ['target' => '_blank']) :
        '';
    ?>

    <?= DetailView::widget([
    	'model' => $model,
    	'attributes' => $attributes,
	]) ?>
	
</div>
