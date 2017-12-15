<?php
use rmrevin\yii\module\Comments;
use yii\helpers\Html;

$this->registerCssFile("@web/css/history.css", ['depends' => 'frontend\assets\AppAsset'], 'css-profile-history');
?>
		
<h3>History</h3>
<hr>
	
<?php 
if ($events != NULL) {
	$i = 1; 
?>

<?= Yii::$app->user->id == $profile->user_id ? 
	'You can add events to your timeline in your ' . Html::a('profile settings', ['profile-mgmt/settings', 'id' => $profile->id], ['target' => '_blank']) . '</em>' : 
	NULL; ?>

<ul id='timeline'>
	<?php foreach ($events as $event) { ?>

	<li class='work'>
	  <input class='radio' id="<?= 'work' . $i?>" name='works' type='radio' <?= $i==1 ? 'checked' : NULL ?>>
	  <div class="relative">
	    <label for="<?= 'work' . $i?>"><?= $event->title ?></label>
	    <span class='date'><?= Yii::$app->formatter->asDate($event->date, 'php:F j, Y') ?></span>
	    <span class='circle'></span>
	  </div>
	  <div class='content'>
	  	<?= $event->description ? '<p>' . $event->description . '</p>' : NULL; ?>
	  </div>
	</li>

	<?php 
	$i++;
	} ?>
</ul>

<?php } else {
	echo Yii::$app->user->id == $profile->user_id ?
		'<em>No events recorded.  You can add events to your timeline in your ' . Html::a('profiles settings', ['profile-mgmt/settings', 'id' => $profile->id], ['target' => '_blank']) . '</em>' :
		'<em>No events recorded.</em>';
} ?>