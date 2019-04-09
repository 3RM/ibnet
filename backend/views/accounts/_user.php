<?php

use common\models\User;
use yii\bootstrap\Html;
// use yii\helpers\HtmlPurifier;
?>

<div class="list-row">
	<p class="col-60 review"><?= $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review', 'id' => $model->id]) ?></p>
	<p class="col-60 button" id=<?= '"user-' . $model->id . '"' ?>>
		<?php if ($model->status == User::STATUS_DELETED) {
    		echo '<span style="color:orange">' . Html::button($model->id, ['class' => 'btn-link']) . '</span>';
    	} elseif ($model->status == User::STATUS_ACTIVE) {
    		echo '<span style="color:green">' . Html::button($model->id, ['class' => 'btn-link']) . '</span>';
    	} elseif ($model->status == User::STATUS_BANNED) {
    		echo '<span style="color:red">' . Html::button($model->id, ['class' => 'btn-link']) . '</span>';  
    	}?>
    </p>
    <p class="col-180"><?= $model->fullName ?></p>
    <p class="col-240"><?= $model->username ?></p>
    <p class="col-300"><?php if ($model->email) {
        	echo $model->email;
        } elseif ($model->new_email) {
        	echo '<span style="color:red">' . $model->new_email . '</span>';
        }?>
    </p>    
    <p class="col-150"><?= Yii::$app->formatter->asDate($model->last_login); ?></p>
</div>

<?php $this->registerJS("$('#user-" . $model->id . "').click(function(e) {
    $.get('/accounts/account-detail', {id: " . $model->id . "}, function(data) {
        $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
    })
});", \yii\web\View::POS_READY); ?>
