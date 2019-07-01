<?php

use kartik\color\ColorInput;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

/* @var $this yii\web\View */
?>


<?php $form = ActiveForm::begin(['id' => 'category-edit']); ?>
   
<?= $form->field($group, 'categoryName')->textInput() ?>
    <p class="top-margin-40 category-description">
        <i class="fas fa-info-circle"></i> Enter a brief description to let group members know what belongs in this category.
    </p>
    <?= $form->field($group, '_categoryDescription')->textArea(['style' => 'resize:none']) ?>
    <?= $form->field($group, 'cid')->hiddenInput(['value' => $group->cid])->label(false) ?>

    <div class="color-picker-container">
        <div class="picker">
	      <label class="control-label">Banner Color</label>
        <?= ColorInput::widget([
            'name' => 'categoryBannerColor',
            'id' => $group->categoryBannerColor ? 'banner-color' : 'new-banner-color',
            'value' => $group->categoryBannerColor ?? 'blue',
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
            'id' => $group->categoryTitleColor ? 'title-color' : 'new-title-color',
            'value' => $group->categoryTitleColor ?? 'white',
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
        <?= $group->cid != NULL ? 
            Html::submitButton('Save', [
                'method' => 'POST',
                'name' => 'save',
                'value' => $group->cid,
                'class' => 'btn btn-primary',
            ]) :
            Html::submitButton('Save', [
                'method' => 'POST',
                'name' => 'new',
                'class' => 'btn btn-primary',
            ])
        ?>
        <?php if ($group->cid != $group->discourse_category_id && $group->cid !=NULL) { ?>
            <?= Html::submitButton('<i class="fas fa-trash-alt"></i>', [
                'method' => 'POST',
                'name' => 'trash',
                'value' => $group->cid,
                'class' => 'btn btn-primary red',
                'onclick' => 'return confirm("Are you sure you want to delete this category?  Topics and posts may be lost.")',
        ]) ?>
        <?php } ?>
    </div>
<?php $form = ActiveForm::end(); ?>