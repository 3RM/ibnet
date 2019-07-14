<?php

use kartik\color\ColorInput;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>


<?php $form = ActiveForm::begin(['id' => 'category-new']); ?>
   
<?= $form->field($group, 'categoryName', ['enableAjaxValidation' => true])->textInput(['placeholder' => 'One or two words...']) ?>
<p class="top-margin-40 category-description">
    <i class="fas fa-info-circle"></i> Enter a brief description to let group members know what belongs in this category.
</p>
<?= $form->field($group, '_categoryDescription')->textArea(['style' => 'resize:none']) ?>

<div class="color-picker-container">
    <div class="picker">
	    <label class="control-label">Banner Color</label>
        <?= ColorInput::widget([
            'name' => 'categoryBannerColor',
            'id' => 'new-banner-color',
            'value' => 'blue',
       	    'showDefaultPalette' => true,
       	    'options' => ['class' => 'hidden'],
       	    'pluginOptions' => [
       	        'showInput' => true,
       	        'showInitial' => true,
       	        'showPalette' => true,
       	        'showPaletteOnly' => false,
       	        'showSelectionPalette' => true,
       	        'showAlpha' => false,
       	        'allowEmpty' => false,
       	        'preferredFormat' => 'name',
       	    ]
        ]); ?>
    </div>
    <div class="picker">
        <label class="control-label">Title Color</label>
        <?= ColorInput::widget([
            'name' => 'categoryTitleColor',
            'id' => 'new-title-color',
            'value' => 'white',
            'showDefaultPalette' => false,
            'options' => ['class' => 'hidden'],
            'pluginOptions' => [
       	        'showInput' => true,
       	        'showInitial' => true,
       	        'showPalette' => true,
       	        'showPaletteOnly' => true,
       	        'showSelectionPalette' => false,
       	        'showAlpha' => false,
       	        'allowEmpty' => false,
       	        'preferredFormat' => 'name',
       	        'palette' => ["white", "black"],
       	    ]
       	]); ?>
    </div>
</div>

<div class="top-margin-40"></div>
<div class="category-edit-buttons">
    <?= Html::submitButton('Save', [
            'method' => 'POST',
            'name' => 'new',
            'class' => 'btn btn-primary',
        ])
    ?>
</div>
<?php $form = ActiveForm::end(); ?>