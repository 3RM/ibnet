<?php

use common\models\Utility;
use frontend\assets\AjaxAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
AjaxAsset::register($this);
$session = Yii::$app->session;
$count = $session->get('count');
$this->title = '';
?>

<div class="header-container">
    <div class="header-img">
        <?= Html::img('@img.site/ibnet-large.png') ?>
        <div id="search-box" class="input-group">
            <?php $form = ActiveForm::begin(['id' => 'search-form']); ?>
            <?= $form->field($searchModel, 'term')->textInput([
                'maxlength' => true, 
                  'class' => 'form-control',
                'placeholder' => 'Find churches, organizations and individuals ...',
                'autocomplete' => 'off',
            ])->label('') ?>
            <?= Html::submitButton('', [
                'method' => 'POST',
                'class' => 'btn btn-default search-icon',
                'name' => 'search',
            ]) ?>
            <?php $form = ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div class="header-wh">
    <p>Looking for that blessed hope, and the glorious appearing of the great God and our Saviour Jesus Christ; Who gave himself for us, that he might redeem us from all iniquity, and purify unto himself a peculiar people, zealous of good works. Titus 2:13-14</p>
</div>
<div class="header-wh-mb">
    <p>Looking for that blessed hope, and the glorious appearing of the great God and our Saviour Jesus Christ; Ttus 2:13</p>
</div>
<div class="header-wh-sm">
    <p>Looking for that blessed hope,<br>Titus 2:13</p>
</div>

<div class="new-profile">
    <?= Html::a('Get Started &#187', ['site/register'], ['class' => 'btn btn-home get-started']); ?>
    <div id="box3Content"><?= $box3Content ?></div>
    <?= $count > 1 ? Html::a('Next New Profile &#187', ['ajax/next'], [
      'id' => 'next-id', 
      'data-on-done' => 'nextDone', 
      'class' => 'btn btn-home'
    ]) : NULL; ?>
    <?php $this->registerJs("$('#next-id').click(handleAjaxLink);", \yii\web\View::POS_READY); ?>
</div>

<div class="blog-container">
    <?php for ($i=0; $i<3 ; $i++) { ?>
        <?php $date = Yii::$app->formatter->asDate($posts[$i]['post_date'], 'php:F j, Y'); ?>
        <div class="blog-card">
          <?= '<div class="blog-crop">' . Html::a(Html::img($posts[$i]['image_url']) . '</div>' .
            '<h3>' . $posts[$i]['post_title'] . '</h3>' .
            '<p>' . $posts[$i]['author_name'] . ' &#8226 ' . $date . '<span class="comments"><span class="glyphicons glyphicons-chat"></span> (' . ($comments[$posts[$i]['post_id']]['COUNT(comment_id)'] ? $comments[$posts[$i]['post_id']]['COUNT(comment_id)'] : 0) . ')</span></p>' .
            '<p>' . Utility::trimText($posts[$i]['post_content'], 300, " ") . '</p>', $posts[$i]['post_url']); ?>
        </div>
    <?php } ?>
</div>
<div class="blog-container">
    <?php for ($i=3; $i<6 ; $i++) { ?>
        <?php $date = Yii::$app->formatter->asDate($posts[$i]['post_date'], 'php:F j, Y'); ?>
        <div class="blog-card">
          <?= '<div class="blog-crop">' . Html::a(Html::img($posts[$i]['image_url']) . '</div>' .
            '<h3>' . $posts[$i]['post_title'] . '</h3>' .
            '<p>' . $posts[$i]['author_name'] . ' &#8226 ' . $date . '<span class="comments"><span class="glyphicons glyphicons-chat"></span> (' . ($comments[$posts[$i]['post_id']]['COUNT(comment_id)'] ? $comments[$posts[$i]['post_id']]['COUNT(comment_id)'] : 0) . ')</span></p>' .
            '<p>' . Utility::trimText($posts[$i]['post_content'], 300, " ") . '</p>', $posts[$i]['post_url']); ?>
        </div>
    <?php } ?>
</div>