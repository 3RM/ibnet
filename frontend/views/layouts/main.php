<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AjaxAsset;
use frontend\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
AjaxAsset::register($this);
$title="IBNet | Independent Baptist Network";
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= $this->title == NULL ? $title : $this->title . ' - ' . $title ?></title>
    <meta name="description" content="IBNet is a place for independent Baptist churches, ministries, and individuals to connect.  Now it’s easier than ever to reach out, share ideas, and much more.">
    <?php $this->head() ?>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#dc9f27">
    <meta name="theme-color" content="#ffffff">


</head>
<body class="<?= Html::encode($this->title) ?>">
<?php $this->beginBody() ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-92552138-1', 'auto');
  ga('send', 'pageview');
</script>

<div class="wrap" id="main-wrap">
    <?= $this->render('_navbar') ?>
    <div class="clearfix"></div>
    <div class="container">
        <?= $content ?>
    </div>
</div>

<?= $this->render('_footer') ?>

<?php $this->endBody() ?>
<a href="#0" class="cd-top">Top</a>
</body>
</html>
<?php $this->endPage() ?>