<?php

use common\models\Utility;
use common\widgets\Alert;
use frontend\assets\AjaxAsset;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

AjaxAsset::register($this);

$this->title = 'My Account';
?>

<div class="account-header-container">
    <div class="account-header acc-group-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
</div>

<div class="container">
    <?= Alert::widget() ?>
   
    <h3>Forum Categories</h3>

    <?= Html::Button('<i class="fas fa-plus"></i> New Category', ['id' => 'new-category', 'class' => 'btn btn-primary top-margin-28']) ?>
    <?php $this->registerJS("$('#new-category').click(function(e) {
        $.get('/group/category-new', {id:" . $group->id . "}, function(data) {
            $('#category-modal-new').modal('show').find('#category-content-new').html(data);
        })
    });", \yii\web\View::POS_READY); ?>

    <?php Modal::begin([
    'header' => '<h3><i class="fas fa-info-circle"></i> Forum Tips</h3>',
    'toggleButton' => [
        'id' => 'forum-tips-id',
        'label' => '<i class="fas fa-info-circle"></i>',
        'class' => 'link-btn',
    ]
    ]); ?>
        <div class="forum-tips">
            <div class="tip">
                <div><i class="fas fa-pen"></i></div><div>Edit a category.  Use the category description to inform users of the purpose of the category, or to establish category guidelines or rules.
                Why should people use the category?  What is it for?  What should topics in the category generally contain?</div>
            </div>
            <div class="tip">
                <div><i class="fas fa-lock"></i></div><div>Close a topic. A closed topic will not accept new posts.</div>
            </div>
            <div class="tip">
                <div><i class="fas fa-thumbtack"></i></div><div>Pin a topic.  A pinned topic will appear at the top of a category. Once someone reads to the bottom of a pinned topic, 
                it is automatically unpinned for them specifically.</div>
            </div>
            <div class="tip">
                <div><i class="far fa-trash-alt"></i></div><div>Remove a topic.  The topic and all posts will removed.  It is possible to recover a removed topic. Inquire with an admin.</div>
            </div>
            <p class="hint">
                Hint: Don’t create too many initial categories, as you can overwhelm your audience. You can always add more categories. It’s better to figure out the 
                organization as you go rather than assuming you’ll get it all right from the beginning.
            </p>
        </div>
    <?php Modal::end(); ?>

    <div class="category-item" style="margin-top:40px; border-left:10px solid <?= Utility::colorToHex($parentCategory->color) ?>">
        <h4><?= $parentCategory->name ?> <span>(<i>top level category</i>)</span> <?= Html::button('<i class="fas fa-pen"></i>', ['id' => 'parent', 'class' => 'link-btn']) ?></h4>
        <p id="category-topic-<?= $parentCategory->id ?>" class="<?= $parentCategory->topic_count>0 ? 'pointer' : NULL ?>">
            <i class="fas fa-caret-right"></i> Topics: <?= $parentCategory->topic_count ?>
        </p>
        <div id="topics-container-<?= $parentCategory->id ?>">
            <?php if (isset($topics[$parentCategory->id])) { ?>
                <?php foreach ($topics[$parentCategory->id] as $i => $topic) { ?>
                    <?php if ($i > 0) { ?>
                        <div id="list-item-<?= $topic->id ?>">
                            <?= ($topic->pinned ? '<i class="fas fa-thumbtack topic-status"></i>' : NULL) .
                                ($topic->closed ? '<i class="fas fa-lock topic-status"></i>' : NULL) . 
                                $topic->fancy_title . ' (' . $topic->posts_count . ' post' . ($topic->posts_count > 1 ? 's' : NULL) . ')' .
                                Html::a($topic->closed ? '<i class="fas fa-unlock"></i>' : '<i class="fas fa-lock"></i>', ['ajax/close-topic', 'gid' => $group->id, 'tid' => $topic->id],
                                    [
                                        'id' => 'close-topic-' . $topic->id,
                                        'data-on-done' => 'topicStatusDone',
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                        'title' => $topic->closed ? 'Open Topic' : 'Close Topic',
                                    ]) .
                                Html::a('<i class="fas fa-thumbtack ' . ($topic->pinned ? 'fa-rotate-180' : NULL) . '"></i>', ['ajax/pin-topic', 'gid' => $group->id, 'tid' => $topic->id],
                                    [
                                        'id' => 'pin-topic-' . $topic->id,
                                        'data-on-done' => 'topicStatusDone',
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                        'title' => $topic->pinned ? 'Unpin Topic' : 'Pin Topic',
                                    ]) .
                                Html::a('<i class="far fa-trash-alt"></i>', ['ajax/remove-topic', 'gid' => $group->id, 'tid' => $topic->id],
                                    [
                                        'id' => 'remove-topic-' . $topic->id,
                                        'data-on-done' => 'removeTopicDone',
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                        'title' => 'Remove Topic',
                                    ]);
                            ?>
                        </div>
                        <?php 
                            $this->registerJs("$('#list-item-" . $topic->id . "').on('click', '#close-topic-" . $topic->id . "', handleAjaxSpanLink);", \yii\web\View::POS_READY);
                            $this->registerJs("$('#list-item-" . $topic->id . "').on('click', '#pin-topic-" . $topic->id . "', handleAjaxSpanLink);", \yii\web\View::POS_READY);
                            $this->registerJs("$('#remove-topic-" . $topic->id . "').click(function(e) {
                                if (confirm('Are you sure you want to remove this topic?')) {
                                    handleAjaxSpanLink(e);
                                } else { 
                                    return false;
                                }
                            });", \yii\web\View::POS_READY);
                        ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
        <?php if ($parentCategory->topic_count > 0) {
            $this->registerJS("$('#category-topic-" . $parentCategory->id . "').click(function(e) {
                if ($('#topics-container-" . $parentCategory->id . "').is(':visible')) {
                    $('[id^=category-topic-] > i').removeClass('fas fa-caret-down');
                    $('[id^=category-topic-] > i').addClass('fas fa-caret-right');
                    $('#topics-container-" . $parentCategory->id . "').hide(300);
                } else {
                    $('[id^=category-topic-] > i').removeClass('fas fa-caret-down');
                    $('[id^=category-topic-] > i').addClass('fas fa-caret-right');
                    $('i', this).removeClass('fas fa-caret-right');
                    $('i', this).addClass('fas fa-caret-down');
                    $('[id^=topics-container-]').hide(300);
                    $('#topics-container-" . $parentCategory->id . "').show(300);
                }
            });", \yii\web\View::POS_READY);
        } ?>

        <?php if ($categories) { ?>
        <div class="category-child">
            <?php foreach ($categories as $category) { ?>
                <div class="category-item" style="border-left:10px solid <?= Utility::colorToHex($category->color) ?>">
                    <h4><?= $category->name ?> <?= Html::button('<i class="fas fa-pen"></i>', ['id' => 'category-' . $category->id, 'class' => 'link-btn']) ?></h4>
                    <p id="category-topic-<?= $category->id ?>" class="<?= $category->topic_count>0 ? 'pointer' : NULL ?>">
                        <i class="fas fa-caret-right"></i> Topics: <?= $category->topic_count ?>
                    </p>
                    <div id="topics-container-<?= $category->id ?>">
                        <?php if (isset($topics[$category->id])) { ?>
                            <?php foreach ($topics[$category->id] as $topic) { ?>
                                <div id="list-item-<?= $topic->id ?>">
                                    <?= ($topic->pinned ? '<i class="fas fa-thumbtack topic-status"></i>' : NULL) .
                                        ($topic->closed ? '<i class="fas fa-lock topic-status"></i>' : NULL) . 
                                        $topic->fancy_title . ' (' . $topic->posts_count . ' post' . ($topic->posts_count > 1 ? 's' : NULL) . ')' .
                                        Html::a($topic->closed ? '<i class="fas fa-unlock"></i>' : '<i class="fas fa-lock"></i>', ['ajax/close-topic','gid' => $group->id, 'tid' => $topic->id],
                                            [
                                                'id' => 'close-topic-' . $topic->id,
                                                'data-on-done' => 'topicStatusDone',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => $topic->closed ? 'Open Topic' : 'Close Topic',
                                            ]) .
                                        Html::a('<i class="fas fa-thumbtack ' . ($topic->pinned ? 'fa-rotate-180' : NULL) . '"></i>', ['ajax/pin-topic', 'gid' => $group->id, 'tid' => $topic->id],
                                            [
                                                'id' => 'pin-topic-' . $topic->id,
                                                'data-on-done' => 'topicStatusDone',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => $topic->pinned ? 'Unpin Topic' : 'Pin Topic',
                                            ]) .
                                        Html::a('<i class="far fa-trash-alt"></i>', ['ajax/remove-topic', 'gid' => $group->id, 'tid' => $topic->id],
                                            [
                                                'id' => 'remove-topic-' . $topic->id,
                                                'data-on-done' => 'removeTopicDone',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => 'Remove Topic',
                                            ]);
                                    ?>
                                </div>
                                <?php 
                                    $this->registerJs("$('#list-item-" . $topic->id . "').on('click', '#close-topic-" . $topic->id . "', handleAjaxSpanLink);", \yii\web\View::POS_READY);
                                    $this->registerJs("$('#list-item-" . $topic->id . "').on('click', '#pin-topic-" . $topic->id . "', handleAjaxSpanLink);", \yii\web\View::POS_READY);
                                    $this->registerJs("$('#remove-topic-" . $topic->id . "').click(function(e) {
                                        if (confirm('Are you sure you want to remove this topic?')) {
                                            handleAjaxSpanLink(e);
                                        } else { 
                                            return false;
                                        }
                                    });", \yii\web\View::POS_READY);
                                ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if ($category->topic_count > 0) {
                        $this->registerJS("$('#category-topic-" . $category->id . "').click(function(e) {
                            if ($('#topics-container-" . $category->id . "').is(':visible')) {
                                $('[id^=category-topic-] > i').removeClass('fas fa-caret-down');
                                $('[id^=category-topic-] > i').addClass('fas fa-caret-right');
                                $('#topics-container-" . $category->id . "').hide(300);
                            } else {
                                $('[id^=category-topic-] > i').removeClass('fas fa-caret-down');
                                $('[id^=category-topic-] > i').addClass('fas fa-caret-right');
                                $('i', this).removeClass('fas fa-caret-right');
                                $('i', this).addClass('fas fa-caret-down');
                                $('[id^=topics-container-]').hide(300);
                                $('#topics-container-" . $category->id . "').show(300);
                            }
                        });", \yii\web\View::POS_READY); 
                    } ?>
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