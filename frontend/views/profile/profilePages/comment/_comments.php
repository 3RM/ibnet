<?php
use rmrevin\yii\module\Comments;
?>

<div class="container">
		
	<?= Comments\widgets\CommentListWidget::widget([
	    'entity' => (string) $profile->type . '-' . $profile->id,
	    'showCreateForm' => true,
	    'sort' => [
	    	'defaultOrder' => [
       		    'id' => SORT_DESC,
       		],
       	],
	]); ?>

</div>