<?php

/* @var $this yii\web\View */

use common\models\Utility;
use kartik\grid\GridView;
use yii\bootstrap\Html;

$this->title = 'Database';
?>

<div class="site-index">

	<?php $results = Yii::$app->db->createCommand('SHOW TABLES FROM ibnet')->queryAll(); ?>
	<h5>Tables: <?= count($results); ?></h5>
	<h5>DB size: <?= (double) $t = Yii::$app->db->createCommand('
			SELECT 
				table_schema, 
				Round(Sum(data_length + index_length) / 1024 / 1024, 4) 
			FROM information_schema.TABLES
			GROUP BY table_schema;
		')->execute() ?> MB
	</h5>

	<div class="top-margin">
		<table class="table table-striped table-bordered table-condensed kartik-sheet-style">
			<th>Table Name</th>
			<th>Records</th>
			<th>Size (MB)</th>
			</th>
			<?php foreach ($results as $result) { ?>
				<?php Yii::$app->db->createCommand('Optimize TABLE ' . $result['Tables_in_ibnet'] )->execute(); ?>
				<tr>
					<td><?= $result['Tables_in_ibnet'] ?></td>
					<td><?= (new \yii\db\Query())->select('id')->from($result['Tables_in_ibnet'])->count(); ?></td>
					<td><?= var_dump((float) $t = Yii::$app->db->createCommand('
						SELECT 
					    	table_name, 
					    	round(((data_length + index_length) / 1024 / 1024), 2) 
						FROM information_schema.TABLES 
						WHERE table_schema = "ibnet"
					    	AND table_name = "profile" ;
		    			')->execute()); ?>
		    		</td>
				</tr>
			<?php } ?>
		</table>
	</div>

</div>
