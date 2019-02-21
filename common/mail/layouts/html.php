<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
   		#container { 
   			border: 1px solid #eee;
			border-radius: 20px;
    		width: 100%;
    		max-width: 500px;
    	}
    	p#title {
    		margin: 20px; 
    		font-size: 1.6em;
    	}
    	#content {
    		margin: 0 20px;
    		font-size: 1.2em;
    	}
    	#footer {
    		margin: 20px; 
    	}
    	#footer hr {
    		margin: 5px 0;
    		color: #eee;
    	}
    </style>
</head>
<body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
