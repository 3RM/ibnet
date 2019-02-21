<?php

use common\models\profile\Profile;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>

<div class="browse-result-container">

    <?php switch ($model->type) { 
    	
   	case Profile::TYPE_ASSOCIATION: ?>

   		<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/association',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_CAMP: ?>
		    
		<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/camp',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_CHAPLAIN: ?>

		<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/evangelist',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['/profile/evangelist',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->ind_city . ', ' ?>
	    		<?= (empty($model->ind_st_prov_reg) ? NULL : $model->ind_st_prov_reg) .
	    			($model->ind_country == 'United States' ? NULL : ', ' . $model->ind_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_CHURCH: ?>

		<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/church',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p>Independent Baptist Church</p>
	    		<p><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_EVANGELIST ?>
	
	   	<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/evangelist',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['/profile/evangelist',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->ind_city . ', ' ?>
	    		<?= (empty($model->ind_st_prov_reg) ? NULL : $model->ind_st_prov_reg) .
	    			($model->ind_country == 'United States' ? NULL : ', ' . $model->ind_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_FELLOWSHIP: ?>
		 
	  	<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/fellowship',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_SPECIAL: ?>
		 
	   	<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/special-ministry',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_MISSION_AGCY: ?>
	
	  	<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/mission-agency',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_MISSIONARY: ?>

	    <div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/missionary',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['profile/missionary',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    	</h4>
	    	<div class="text">
	    		<p><?php if ($model->sub_type == 'Furlough Replacement') { ?>
						<?= $model->spouse_first_name == NULL ? 'Furlough Replacement Missionary' : 'Furlough Replacement Missionaries' ?>
					<?php } elseif ($model->sub_type == 'Bible Translator') { ?>
						<?= $model->spouse_first_name == NULL ? 'Bible Translator' : 'Bible Translators' ?>
					<?php } else { ?>
						<?= $model->spouse_first_name == NULL ? 'Missionary to ' : 'Missionaries to ' ?><?= $model->miss_field ?>
					<?php } ?>
				</p>
	    		<p class="loc"><?= $model->ind_city . ', ' ?>
	    		<?= (empty($model->ind_st_prov_reg) ? NULL : $model->ind_st_prov_reg) .
	    			($model->ind_country == 'United States' ? NULL : ', ' . $model->ind_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_MUSIC: ?>
		  
	  	<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/music',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_PASTOR: ?>
	
	  	<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/pastor',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['/profile/pastor',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->sub_type . ' at ' . $model->hc_org_name ?></p>
	    		<p class="loc"><?= $model->hc_org_city . ', ' ?>
	    		<?= (empty($model->hc_org_st_prov_reg) ? NULL : $model->hc_org_st_prov_reg) .
	    			($model->hc_org_country == 'United States' ? NULL : ', ' . $model->hc_org_country) ?>
	    		</p>
	    	</div>
		</div>

    <?php break;
	case Profile::TYPE_PRINT: ?>

	    <div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/print',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_SCHOOL: ?>
			
		<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= Html::a($model->org_name, ['/profile/school',
	    			'urlLoc' => $model->url_loc, 
	    			'urlName' => $model->url_name, 
	    			'id' => $model->id]) 
	    		?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->type ?></p>
	    		<p class="loc"><?= $model->org_city . ', ' ?>
	    		<?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) .
	    			($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	case Profile::TYPE_STAFF: ?>
	
	   	<div class="browse-card">
			<?= empty($model->image2) ? Html::img('@img.profile/profile-logo.png') : Html::img($model->image2) ?>
			<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    	<h4>
	    		<span class="icon"><?= Profile::$icon[$model->type] . ' ' ?></span>
	    		<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/staff',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' (& ' . $model->spouse_first_name . ') ' . $model->ind_last_name, ['/profile/staff',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    	</h4>
	    	<div class="text">
	    		<p><?= $model->title ?> at <?= $model->ministry_of ? Profile::findOne($model->ministry_of)->org_name : NULL ?></p>
	    		<p class="loc"><?= $model->ind_city . ', ' ?>
	    		<?= (empty($model->ind_st_prov_reg) ? NULL : $model->ind_st_prov_reg) .
	    			($model->ind_country == 'United States' ? NULL : ', ' . $model->ind_country) ?>
	    		</p>
	    	</div>
		</div>

	<?php break;
	default: 
		break;
	} ?>

</div>