<?php

/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\bootstrap\Html;

$this->title = 'Cron Jobs';
?>

<div class="site-index">

	<?= GridView::widget([
	    'dataProvider'=>$dataProvider,
	    // 'filterModel'=>$searchModel,
	    'columns'=>$gridColumns,
	]); ?>

	<h3>Description</h3>
	<div class="schedule">
		<p><b>profile-expirations</b>: Check for upcoming or expired profiles (daily)</p>
		<p><b>profile-tracking</b>: Log number of users and profiles (weekly)</p>
		<p><b>blog</b>: Send email blog-digest (weekly)</p>
		<p><b>video-accessible</b>: Check that remote video links are accessible (weekly)</p>
	</div>

</div>
<script src="https://use.fontawesome.com/1db1e4efa2.js"></script>
