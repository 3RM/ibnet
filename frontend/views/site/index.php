<?php

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

<div class="wrap search">
    <div class="container">
            <?php $form = ActiveForm::begin(['id' => 'search-form']); ?>
            <!-- Search Box -->
            <div class="input-group row">
                <?= $form->field($searchModel, 'term')->textInput([
                    'maxlength' => true, 
                    'class' => 'form-control',
                    'placeholder' => 'Find churches, organizations and individuals ...',
                    'autocomplete' => 'off',
                ])->label('') ?>
                <div class="input-group-btn">
                    <?= Html::submitButton('', [
                        'method' => 'POST',
                        'class' => 'btn btn-default search-icon',
                        'name' => 'search',
                    ]) ?>
                </div>
            </div>
            <!-- End Search Box -->

            <?php $form = ActiveForm::end(); ?>
    </div>
</div>
<div class="clearsearch"></div>

<div class="header-img">
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

<div class="wrap bg-gray">
    <div class="container">
        <div class="site-index">
            
            <div class="row top-margin-40">
              <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                  <?= Html::a(Html::img('@web/images/' . 'blog-coming-soon.jpg', ['class' => 'img-thumbnail']), ['/site/blog']) ?>
                  <div class="caption">
                    <h3>IBNet Blog - Coming Soon</h3>
                    <p>Stay tuned for the upcoming launch of the new IBNet blog!  It will be a place where IBNet users can share relevant content.</p>
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
                    <div id="box3Content"><?= $box3Content ?></div>
                    <?php if ($count) { ?>
                      <p class="center">
                        <?= $count > 1 ? Html::a('Next New Profile &#187', ['ajax/next'], [
                          'id' => 'next-id', 
                          'data-on-done' => 'nextDone', 
                          'class' => 'btn btn-home'
                        ]) : 
                        NULL; ?>
                        <?php $this->registerJs("$('#next-id').click(handleAjaxLink);", \yii\web\View::POS_READY); ?>
                      </p>
                    <?php } ?>
                </div>
              </div>
            </div>
          </div>
    </div>
</div>