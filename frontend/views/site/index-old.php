<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
$session = Yii::$app->session;
$count = $session->get('count');
$this->title = '';
?>

<div class="wrap search">
    <div class="container">
            <?php $form = ActiveForm::begin(['id' => 'search-form']); ?>

            <!-- Search Box -->
            <div class="input-group row">
                <?= $form->field($searchModel, 'term')->textInput([
                    'maxlength' => true, 
                    'class' => 'form-control',
                    'placeholder' => 'Type search term and hit enter',
                    'autocomplete' => 'off',
                ])->label('') ?>
                <div class="input-group-btn">
                    <?= Html::submitButton('', [
                        'method' => 'POST',
                        'class' => 'btn btn-default',
                        'name' => 'search',
                    ]) ?>
                </div>
            </div>
            <!-- End Search Box -->

            <?php $form = ActiveForm::end(); ?>
    </div>
</div>
<div class="clearsearch"></div>
<div class="wrap">
    <div class="container">
        <div class="site-index">
            <div class="row">
              <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                  <?= Html::a(Html::img('@web/images/' . 'blog-coming-soon.jpg', ['class' => 'img-thumbnail']), ['/site/blog']) ?>
                  <div class="caption">
                    <h3>IBNet Blog - Coming Soon</h3>
                    <p>Put your thinking caps on and crack those knuckles.  Our first new feature launch will be a blog where users of IBNet can share original content.</p>
                    <p class="center"><?= Html::a('View', ['/site/blog'], ['class' => 'btn btn-home', 'role' => 'button']) ?></p>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                  <?= Html::a(Html::img('@web/images/' . 'grand-opening.jpg', ['class' => 'img-thumbnail']), ['/site/grand-opening']) ?>
                  <div class="caption">
                    <h3>Grand Opening</h3>
                    <p>Welcome to the grand opening of the newly updated IBNet.org!  Find out what IBNet is all about and how it can benefit you and your ministry!</p>
                    <p class="center"><?= Html::a('View', ['/site/grand-opening'], ['class' => 'btn btn-home', 'role' => 'button']) ?></p>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                  <?php Pjax::begin() ?>
                    <?= $box3Content ?>
                      <?php if ($count > 1) { ?>
                        <p class="center"><?= Html::a('Next &#187', ['/box3/next'], ['class' => 'btn btn-home', 'role' => 'button']) ?></p>
                      <?php } ?>
                  <?php Pjax::end() ?>
                </div>
              </div>
            </div>
          </div>
    </div>
</div>