<?php

/* @var $this yii\web\View */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$this->title = 'Solr Admin Panel';
?>

<div class="site-index">

	<p>Todo: Use 1and1 Cloud api to update Solr firewall policy with user's current IP.</p>
	<p><?= 'Your IP: ' . Yii::$app->request->userIP; ?></p>

	<p><?= Html::a('Solr Admin Panel ' . Html::icon('new-window'), 'http://62.151.181.176:8983/solr/#/', ['target' => '_blank']) ?></p>

	<p>Todo: Add ability to browse config files.  Be able to stop/start/restart solr.</p>


</div>
