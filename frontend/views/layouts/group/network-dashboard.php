<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\models\Utility;
use common\models\network\NetworkMember;
use frontend\assets\AjaxAsset;
use frontend\assets\NetworkAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;

NetworkAsset::register($this);
AjaxAsset::register($this);
dmstr\web\AdminLteAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$user = Yii::$app->user->identity;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="description" content="IBNet is a place for Independent Baptist churches, ministries, and individuals to connect.  Now it’s easier than ever to reach out, share ideas, and much more.">
    <?php $this->head() ?>
</head>
<body class="hold-transition <?= \dmstr\helpers\AdminLteHelper::skinClass() ?> sidebar-mini">
<script src="https://use.fontawesome.com/1db1e4efa2.js"></script>
<?php $this->beginBody() ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-92552138-1', 'auto');
  ga('send', 'pageview');
</script>

<div class="wrapper">
  <?= $this->render(
      'header.php',
      ['directoryAsset' => $directoryAsset, 'user' => $user]
  ) ?>
  <?= $this->render(
      'left.php',
      ['directoryAsset' => $directoryAsset, 'user' => $user]
  )
  ?>
  <?= $this->render(
      'content.php',
      ['content' => $content, 'directoryAsset' => $directoryAsset]
  ) ?>
</div>
<?= $this->render('../_footer') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>