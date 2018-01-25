    <?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
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

<div class="index-wrap crop" id="main-wrap">
    <?php
    NavBar::begin([
        'brandLabel' =>  '<span class="abbreviated">IBNet</span><span class="fullname">IBNet | for independent Baptists everywhere</span>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Search', 'url' => ['/site/index']],
        ['label' => 'Browse', 'url' => ['/profile/browse']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => 'My Account', 'url' => ['/site/dashboard']];
        $menuItems[] = [
                'label' => 'Logout (' . Yii::$app->user->identity->first_name . ')',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
    <div class="clearfix"></div>
    <div class="container">
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Independent Baptist Network <?php echo date('Y') ?> </p>
        <p class="pull-left"><?= HTML::a('Privacy', ['/site/privacy']) ?> </p>
        <p class="pull-left"><?= HTML::a('Terms', ['/site/terms']) ?> </p>
        <p class="pull-left"><?= HTML::a('Beliefs', ['/site/beliefs']) ?> </p>

        <p class="pull-right">Designed by <a href="http://ifbdesign.com" target="_blank">IFBDesign</a> & <a href="https://ibnet.org" target="_blank">IBNet</a></p>
    </div>
</footer>

<?php $this->endBody() ?>
<a href="#0" class="cd-top">Top</a>
</body>
</html>
<?php $this->endPage() ?>