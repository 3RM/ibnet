<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;

$this->title = 'Database';
?>

<div class="site-index">

	<h5>Tables: <?= $totalTables ?></h5>
	<h5>Total Size: <?= $totalSize ?> MB
	</h5>

	<div class="top-margin db-table">
		<table class="table table-striped table-bordered">
			<th>Table Name</th>
			<th>Records</th>
			<th>Size (MB)</th>
			</th>
			<?php foreach ($db as $table) { ?>
				<tr>
					<td><?= $table['name'] ?></td>
					<td><?= $table['rows'] ?></td>
					<td><?= $table['size'] ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>

</div>
