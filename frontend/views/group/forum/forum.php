<?php

use common\models\Utility;
use yii\bootstrap\Html;
use yii\bootstrap\Modal; 

/* @var $this yii\web\View */
$this->title = 'My Account';
?>

<div class="account-header-container">
    <div class="account-header acc-group-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
</div>

<div class="container">
   
    <h3>Forum Categories</h3>

    <?= Html::Button('<i class="fas fa-plus"></i> New Category', ['id' => 'new-category', 'class' => 'btn btn-primary top-margin-28']) ?>
    <?php $this->registerJS("$('#new-category').click(function(e) {
        $.get('/group/category-edit', {id:" . $group->id . "}, function(data) {
            $('#category-modal-new').modal('show').find('#category-content-new').html(data);
        })
    });", \yii\web\View::POS_READY); ?>

    <div class="category-item" style="margin-top:40px; border-left:10px solid <?= Utility::colorFilter($parentCategory->color) ?>">
        <h4><?= $parentCategory->name ?> <span>(<i>top level category</i>)</span> <?= Html::button('<i class="fas fa-pen"></i>', ['id' => 'parent', 'class' => 'link-btn']) ?></h4>
        <p><i class="fas fa-caret-right"></i> Topics: <?= $parentCategory->topic_count ?></p>
        <p><i class="fas fa-caret-right"></i> Posts: <?= $parentCategory->post_count ?></p>

        <?php if ($categories) { ?>
        <div class="category-child">
        <?php foreach ($categories as $category) { ?>
            <div class="category-item" style="border-left:10px solid <?= Utility::colorFilter($category->color) ?>">
                <h4><?= $category->name ?> <?= Html::button('<i class="fas fa-pen"></i>', ['id' => 'category-' . $category->id, 'class' => 'link-btn']) ?></h4>
                <p><i class="fas fa-caret-right"></i> Topics: <?= $category->topic_count ?></p>
                <p><i class="fas fa-caret-right"></i> Posts: <?= $category->post_count ?></p>
            </div>
            <?php $this->registerJS("$('#category-" . $category->id . "').click(function(e) {
                $.get('/group/category-edit', {id:" . $group->id . ", cid:" . $category->id . "}, function(data) {
                    $('#category-modal').modal('show').find('#category-content').html(data);
                })
            });", \yii\web\View::POS_READY); ?>
        <?php } ?>
        </div>   
    <?php } ?>
    </div>
    <?php $this->registerJS("$('#parent').click(function(e) {
        $.get('/group/category-edit', {id:" . $group->id . ", cid:" . $parentCategory->id . "}, function(data) {
            $('#category-modal').modal('show').find('#category-content').html(data);
        })
    });", \yii\web\View::POS_READY); ?>

    <div class="top-margin-60"></div>
    <?= Html::a('<span class="glyphicons glyphicons-arrow-left" style="margin-top:-3px;"></span> Return', ['my-groups'], ['class' => 'btn btn-primary']) ?>

</div>

<?php Modal::begin([
    'header' => '<h3><i class="fas fa-pen"></i> Edit Category</h3>',
    'id' => 'category-modal',
    'size' => 'modal-md',
    'headerOptions' => ['class' => ''],
]);
    echo '<div id="category-content"></div>';
Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h3><i class="fas fa-plus"></i> New Category</h3>',
    'id' => 'category-modal-new',
    'size' => 'modal-md',
    'headerOptions' => ['class' => ''],
]);
    echo '<div id="category-content-new"></div>';
Modal::end(); ?>