<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Missionary Video';
?>

<div class="container">   
	
	<div class="watch-title-container">
		<div class="watch-flag">
    		<?= html::img('@img.flag/' . str_replace(' ', '-', $missionary->field) . '.png', ['alt' => 'Country flag']) ?>
    	</div>
		<div class="watch-name-title">
    		<?= $update->title ? '<h2>' . $update->title . '</h2>' : NULL; ?>
    		<h4><?= $profile->coupleName . ' &middot ' . $missionary->field ?></h4>
    		<p>
    			<?= 
    			Html::a('<i class="fas fa-at"></i>', 'mailto:' . $missionary->profile->email) . ' ' .
    			Html::a('<i class="far fa-address-card"></i>', 
    				['profile/missionary', 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name, 'id' => $profile->id], 
    				['class' => 'wprofile']) . ' ' . 
    			Yii::$app->formatter->asDate($update->created_at) ?>
    			
    		</p>
    	</div>
	</div>
	<?= $update->description ? '<p>' . $update->description . '</p>' : NULL ?>
	
	<div class="update-video-wrapper">
		<?= $update->getVideo(true) ?>
	</div>
    
    <p class="top-margin">&nbsp;</p>

</div>