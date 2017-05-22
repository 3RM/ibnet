<?php

use common\models\profile\Profile;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>

<div class="col-sm-2 col-md-1 center search-icon icon-sm">
	<?= Profile::$icon[$model->type] ?>
</div>
<div class="col-sm-10 col-md-11">
    <?php switch ($model->type) { 
    	
   	case 'Association': ?>
  
	    <h4>
	        <?= Html::a($model->org_name, ['/profile/association',
	    		'city' => $model->url_city, 
	    		'name' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
        <b>
            <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
            <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
	        <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
	        <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
        </b> 

	<?php break;
	case 'Camp': ?>
		    
	    <h4>
	    	<?= Html::a($model->org_name, ['/profile/camp',
	    		'city' => $model->url_city, 
	    		'name' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
        <b>
            <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
            <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
	        <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
	        <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case 'Chaplain': ?>
	
	   	<h4>
	        <?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/evangelist',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['/profile/evangelist',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?= $model->type . ', ' ?>
	        <?= $model->ind_city . ', ' ?>
	        <?= (empty($model->ind_st_prov_reg) ? NULL : $model->ind_st_prov_reg) . 
	        	($model->ind_country == 'United States' ? NULL : ', ' . $model->ind_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case 'Church': ?>
	 
	    <h4>
	    	<?= Html::a($model->org_name, ['/profile/church',
	    		'city' => $model->url_city, 
	    		'name' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
	        <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
	        <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
	        <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>     
	    </b>

	<?php break;
	case 'Evangelist': ?>
	
	   	<h4>
	        <?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/evangelist',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['/profile/evangelist',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?= $model->type . ', ' ?>
	        <?= $model->ind_city . ', ' ?>
	        <?= (empty($model->ind_st_prov_reg) ? NULL : $model->ind_st_prov_reg) . 
	        	($model->ind_country == 'United States' ? NULL : ', ' . $model->ind_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case 'Fellowship': ?>
		 
	  	<h4>
	   	    <?= Html::a($model->org_name, ['/profile/fellowship', 
	   	    	'city' => $model->url_city, 
	   	    	'name' => $model->url_name, 
	   	    	'id' => $model->id]) 
	   	    ?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
            <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
	        <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
	        <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case 'Special Ministry': ?>
		 
	   	<h4>
	  		<?= Html::a($model->org_name, ['/profile/special-ministry',
	    		'city' => $model->url_city, 
	    		'name' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	  	</h4>
	  	<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
	        <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
	        <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
	        <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case 'Mission Agency': ?>
	
	  	<h4>
	  		<?= Html::a($model->org_name, ['/profile/mission-agency',
	    		'city' => $model->url_city, 
	    		'name' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	   	</h4>
	   	<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
	        <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
	        <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
	        <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case 'Missionary': ?>

	    <h4>
	    	<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/missionary',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['profile/missionary',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
		</h4>
		<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?php if ($model->sub_type == 'Furlough Replacement') { ?>
	        	Furlough Replacement <?= $model->spouse_first_name ? 'Missionaries' : 'Missionary' ?>, Status: <?= $model->miss_status ?> --- p: <?= $model->phone ?>
	    	<?php } elseif ($model->sub_type == 'Bible Translator') { ?>
				Bible <?= $model->spouse_first_name ? 'Translators' : 'Translator' ?>, Status: <?= $model->miss_status ?> --- p: <?= $model->phone ?>
			<?php } else { ?>
				<?= $model->spouse_first_name ? 'Missionaries' : 'Missionary' ?> to <?= $model->miss_field ?>, Status: <?= $model->miss_status ?> --- p: <?= $model->phone ?>
			<?php } ?>
	    </b>

	<?php break;
	case 'Music Ministry': ?>
		  
	  	<h4>
	  		<?= Html::a($model->org_name, ['/profile/music',
	    		'city' => $model->url_city, 
	    		'name' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	   	</h4>
	   	<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
	        <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
	        <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
	        <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case 'Pastor': ?>
	
	  	<h4>
	   		<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/pastor',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['/profile/pastor',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	   	</h4>
	   	<span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
        <b>
            <?= $model->sub_type . ' at ' . $model->hc_org_name . ', ' . $model->hc_org_city . ', ' . $model->hc_org_st_prov_reg .
            	(($model->hc_org_country == 'United States') ? ' --- p: ' . $model->phone : ', ' . $model->hc_org_country . ' --- p: ' . $model->phone) ?>
        </b>

    <?php break;
	case 'Print Ministry': ?>

	    <h4>
	    	<?= Html::a($model->org_name, ['/profile/print',
	    		'city' => $model->url_city, 
	    		'name' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
        <b>
            <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
            <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
            <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
            <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
        </b>

	<?php break;
	case 'School': ?>
			
		<h4>
	    	<?= Html::a($model->org_name, ['/profile/school',
	    		'city' => $model->url_city, 
	    		'name' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
        <b>
            <?= empty($model->org_address1) ? NULL : $model->org_address1 . ', ' ?>
            <?= empty($model->org_address2) ? NULL : $model->org_address2 . ', ' ?>
            <?= empty($model->org_po_box) ? NULL : 'PO Box ' . $model->org_po_box . ', ' ?>
            <?= $model->org_city . ', ' ?>
	        <?= (empty($model->org_st_prov_reg) ? NULL : $model->org_st_prov_reg) . 
	        	(empty($model->org_zip) ? NULL : ' ' . $model->org_zip) . 
	        	($model->org_country == 'United States' ? NULL : ', ' . $model->org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
        </b>

	<?php break;
	case 'Staff': ?>
	
	   	<h4>
	        <?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['/profile/staff',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' (& ' . $model->spouse_first_name . ') ' . $model->ind_last_name, ['/profile/staff',
		    		'city' => $model->url_city, 
		    		'name' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    </h4>
	    <span class="pull-right"><?= $model->_distance_ ? '(' . round($model->_distance_, 1) . ' mi)' : NULL ?></span>
	    <b>
	        <?= $model->title . ' at ' ?>
	        <?= $model->ministry_org_name . ', ' . $model->ministry_org_city . ', ' ?>
	        <?= (empty($model->ministry_org_st_prov_reg) ? NULL : $model->ministry_org_st_prov_reg) . 
	        	($model->ministry_org_country == 'United States' ? NULL : ', ' . $model->ministry_org_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	default: 
		break;
	} ?>

        <br><?= strlen($model->desc_synopsis) < 300 ? $model->desc_synopsis : $model->desc_synopsis . '...' ?>

</div>