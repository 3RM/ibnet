<?php
use yii\helpers\Html;
use yii\filters\AccessControl;

/* @var $this \yii\web\View */
/* @var $content string */



backend\assets\AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="robots" content="noindex,nofollow">
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition <?= \dmstr\helpers\AdminLteHelper::skinClass() ?> sidebar-mini">
<script src="https://use.fontawesome.com/1db1e4efa2.js"></script>
<?php $this->beginBody() ?>
<?php $user = Yii::$app->user->identity; ?>
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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>