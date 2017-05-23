<?php

use frontend\assets\AjaxAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
//AjaxAsset::register($this);
$session = Yii::$app->session;
$count = $session->get('count');
$this->title = '';
?>

<div class="wrap search">
    <div class="container">
            <?php $form = ActiveForm::begin(['id' => 'search-form']); ?>
            <span class="social social-facebook" style="color:444444; font-size:1.5em; float:right;"></span>
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
              <div class="col-md-6 center">

           <!--      newerton\fancybox3\FancyBox::widget([
                    'target' => '[data-fancybox]',
                    'config' => [
                        'speed '        => 300,               // Animation duration in ms
                        'loop'          => false,             // Enable infinite gallery navigation
                        'opacity'       => 'auto',            // Should zoom animation change opacity, too. If opacity is 'auto', then fade-out if image and thumbnail have different aspect ratios
                        'margin'        => [44,0],            // Space around image, ignored if zoomed-in or viewport smaller than 800px
                        'gutter'        => 30,                // Horizontal space between slides
                        'infobar'       => true,              // Should display toolbars
                        'buttons'       => true,                  //
                        'slideShow'     => true,              // What buttons should appear in the toolbar
                        'fullScreen'    => true,                  //
                        'thumbs'        => true,                  //
                        'closeBtn'      => true,                  //
                        'smallBtn'      => 'auto',            // Should apply small close button at top right corner of the content. If 'auto' - will be set for content having type 'html', 'inline' or 'ajax'
                        'iframe' => [
                            // Iframe template
                            'tpl'           => '<iframe id="fancybox-frame{rnd}" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen allowtransparency="true" src=""></iframe>',
                            // Preload iframe before displaying it
                            // This allows to calculate iframe content width and height
                            // (note=> Due to "Same Origin Policy", you can't get cross domain data).
                            'preload'       => true,

                            // Scrolling attribute for iframe tag
                            'scrolling'     => 'no',
  
                            // Custom CSS styling for iframe wrapping element
                            'css'           => []
  
                        ],
        
                        // Custom CSS class for layout
                        'baseClass'         => '',

                        // Custom CSS class for slide element
                        'slideClass'        => '',

                        // Base template for layout
                        'baseTpl'         => '<div class="fancybox-container" role="dialog" tabindex="-1">' .
                                '<div class="fancybox-bg"></div>' .
                                '<div class="fancybox-controls">' .
                                    '<div class="fancybox-infobar">' .
                                        '<button data-fancybox-previous class="fancybox-button fancybox-button--left" title="Previous"></button>' .
                                        '<div class="fancybox-infobar__body">' .
                                            '<span class="js-fancybox-index"></span>&nbsp;/&nbsp;<span class="js-fancybox-count"></span>' .
                                        '</div>' .
                                        '<button data-fancybox-next class="fancybox-button fancybox-button--right" title="Next"></button>' .
                                    '</div>' .
                                    '<div class="fancybox-buttons">' .
                                        '<button data-fancybox-close class="fancybox-button fancybox-button--close" title="Close (Esc)"></button>' .
                                    '</div>' .
                                '</div>' .
                                '<div class="fancybox-slider-wrap">' .
                                    '<div class="fancybox-slider"></div>' .
                                '</div>' .
                                '<div class="fancybox-caption-wrap"><div class="fancybox-caption"></div></div>' .
                            '</div>',

                        // Loading indicator template
                        'spinnerTpl'        => '<div class="fancybox-loading"></div>',

                        // Error message template
                        'errorTpl'          => '<div class="fancybox-error"><p>The requested content cannot be loaded. <br /> Please try again later.<p></div>',

                        // This will be appended to html content, if "smallBtn" option is not set to false
                        'closeTpl'          => '<button data-fancybox-close class="fancybox-close-small"></button>',

                        // Container is injected into this element
                        'parentEl'          => 'body',

                        // Enable gestures (tap, zoom, pan and pinch)
                        'touch'             => true,

                        // Enable keyboard navigation
                        'keyboard'          => true,

                        // Try to focus on first focusable element after opening
                        'focus'             => true,

                        // Close when clicked outside of the content
                        'closeClickOutside' => true,

                        // Callbacks
                        'beforeLoad'        => new \yii\web\JsExpression('function(){ console.log("beforeLoad"); }'),
                        'afterLoad'         => new \yii\web\JsExpression('function(){ console.log("afterLoad"); }'),
                        'beforeMove'        => new \yii\web\JsExpression('function(){ console.log("beforeMove"); }'),
                        'afterMove'         => new \yii\web\JsExpression('function(){ console.log("afterMove"); }'),
                        'onComplete'        => new \yii\web\JsExpression('function(){ console.log("onComplete"); }'),

                        'onInit'            => new \yii\web\JsExpression('function(){ console.log("onInit"); }'),
                        'beforeClose'       => new \yii\web\JsExpression('function(){ console.log("beforeClose"); }'),
                        'afterClose'        => new \yii\web\JsExpression('function(){ console.log("afterClose"); }'),
                        'onActivate'        => new \yii\web\JsExpression('function(){ console.log("onActivate"); }'),
                        'onDeactivate'      => new \yii\web\JsExpression('function(){ console.log("onDeactivate"); }')
                    ]
                ]); ?> -->

                <?= Html::a(Html::img('@web/images/main.jpg', ['class' => 'responsive']), 'https://vimeo.com/215343913', ['data-fancybox' => true]); ?>

              </div>
              <div class="col-md-6">
                  <h3>What is IBNet?</h3>
                  <p>IBNet is a growing online community of independent Baptists.  It is a church-finder.  It helps you identify IB ministries.  It aids you in connecting with others of like faith and practice.  It gives you a place to highlight your ministry.  It catalogs ministry connections, making it easier than ever to discern the lay of the land among your ministry partners.  It keeps you informed of what is happening among independent Baptists elsewhere.   It is a platform for promoting a Baptist worldview.    In short, IBNet is everything you need and expect in an online community of Baptists.</p>
                  <p class="line-height-sm"><?= Html::a('Register &#187', ['/site/register']) ?></p>
                  <p class="line-height-sm"><?= Html::a('How-to-videos &#187', ['/site/how-to']) ?></p>
              </div>
            </div>
            <div class="row top-margin-60">
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
<script src="jquery.fancybox.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css" />