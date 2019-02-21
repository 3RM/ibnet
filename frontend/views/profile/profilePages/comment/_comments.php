<?php
use rmrevin\yii\module\Comments;
use Yii\helpers\Html;
?>

<h3>Comments</h3>
<hr>

<?php if (Yii::$app->user->isGuest) {
	echo '<p class="msg">' . Html::a('Register', ['/site/register']) . ' or ' . Html::a('login', ['/site/login']) . ' in order to comment.</p>';
} elseif(!(($CommentModel = \Yii::createObject(Comments\Module::instance()->model('comment'))) && $CommentModel::canCreate())) {
	echo '<p class="msg">Set your screen name and identify a home church in your ' . Html::a('personal settings', ['site/settings']) . ' in order to comment.</p>';
} ?>

	<?= Comments\widgets\CommentListWidget::widget([
	    'entity' => (string) $profile->type . '-' . $profile->id,
	    'showCreateForm' => true,
	    'sort' => [
	    	'defaultOrder' => [
       		    'id' => SORT_DESC,
       		],
       	],
	]); ?>