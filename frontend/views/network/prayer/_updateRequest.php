<?php

use common\models\network\Network;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($update, 'update')->textArea(['maxlength' => true]); ?>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
	    <?= $form->field($update, 'select')->widget(Select2::classname(), [
	        'data' => ArrayHelper::map($tagList, 'id', 'tag'),
	        'language' => 'en',
	        'theme' => 'krajee',
	        'options' => [
	            'placeholder' => 'Tag(s)...', 
	            'multiple' => true,
	            'onchange' => 'saveValue(this)', 
	        ],
	        'pluginOptions' => ['allowClear' => true],
	    ])->label(false) ?>
	</div>
</div>
<?= Html::submitButton('Save', [
    'method' => 'POST',
    'class' => 'btn btn-primary longer',
]); ?>
<?php $form = ActiveForm::end(); ?>