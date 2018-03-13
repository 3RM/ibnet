<?php

/* @var $this yii\web\View */

$this->title = '';
?>
<?php $this->registerJsFile("https://code.highcharts.com/highcharts.src.js"); ?>

<div class="site-index">

    <div class="body-content">

        <?= 'Your IP: ' . Yii::$app->request->userIP; ?>
        

    </div>
</div>