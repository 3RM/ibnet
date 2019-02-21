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
    	
   	case Profile::TYPE_ASSOCIATION: ?>
  
	    <h4>
	        <?= Html::a($model->org_name, ['association',
	    		'urlLoc' => $model->url_loc, 
	    		'urlName' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
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
	case Profile::TYPE_CAMP: ?>
		    
	    <h4>
	    	<?= Html::a($model->org_name, ['camp',
	    		'urlLoc' => $model->url_loc, 
	    		'urlName' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
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
	case Profile::TYPE_CHAPLAIN: ?>
	
	   	<h4>
	        <?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['evangelist',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['evangelist',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    </h4>
	    <b>
	        <?= $model->type . ', ' ?>
	        <?= $model->ind_city . ', ' ?>
	        <?= (empty($model->ind_st_prov_reg) ? NULL : $model->ind_st_prov_reg) . 
	        	($model->ind_country == 'United States' ? NULL : ', ' . $model->ind_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case Profile::TYPE_CHURCH: ?>
	 
	    <h4>
	    	<?= Html::a($model->org_name, ['church',
	    		'urlLoc' => $model->url_loc, 
	    		'urlName' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
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
	case Profile::TYPE_EVANGELIST: ?>
	
	   	<h4>
	        <?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['evangelist',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['evangelist',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    </h4>
	    <b>
	        <?= $model->type . ', ' ?>
	        <?= $model->ind_city . ', ' ?>
	        <?= (empty($model->ind_st_prov_reg) ? NULL : $model->ind_st_prov_reg) . 
	        	($model->ind_country == 'United States' ? NULL : ', ' . $model->ind_country) ?>
	        <?= ' --- p: ' . $model->phone ?>
	    </b>

	<?php break;
	case Profile::TYPE_FELLOWSHIP: ?>
		 
	  	<h4>
	   	    <?= Html::a($model->org_name, ['fellowship', 
	   	    	'urlLoc' => $model->url_loc, 
	   	    	'urlName' => $model->url_name, 
	   	    	'id' => $model->id]) 
	   	    ?>
	    </h4>
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
	case Profile::TYPE_SPECIAL: ?>
		 
	   	<h4>
	  		<?= Html::a($model->org_name, ['special-ministry',
	    		'urlLoc' => $model->url_loc, 
	    		'urlName' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	  	</h4>
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
	case Profile::TYPE_MISSION_AGCY: ?>
	
	  	<h4>
	  		<?= Html::a($model->org_name, ['mission-agency',
	    		'urlLoc' => $model->url_loc, 
	    		'urlName' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	   	</h4>
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
	case Profile::TYPE_MISSIONARY: ?>

	    <h4>
	    	<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['missionary',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name, ['missionary',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
		</h4>
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
	case Profile::TYPE_MUSIC: ?>
		  
	  	<h4>
	  		<?= Html::a($model->org_name, ['music',
	    		'urlLoc' => $model->url_loc, 
	    		'urlName' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	   	</h4>
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
	case Profile::TYPE_PASTOR: ?>
	
	  	<h4>
	   		<?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['pastor',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' (& ' . $model->spouse_first_name . ') ' . $model->ind_last_name, ['pastor',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	   	</h4>
        <b>
            <?= $model->sub_type . ' at ' . $model->hc_org_name . ', ' . $model->hc_org_city . ', ' . $model->hc_org_st_prov_reg .
            	(($model->hc_org_country == 'United States') ? ' --- p: ' . $model->phone : ', ' . $model->hc_org_country . ' --- p: ' . $model->phone) ?>
        </b>

    <?php break;
	case Profile::TYPE_PRINT: ?>

	    <h4>
	    	<?= Html::a($model->org_name, ['print',
	    		'urlLoc' => $model->url_loc, 
	    		'urlName' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
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
	case Profile::TYPE_SCHOOL: ?>
			
		<h4>
	    	<?= Html::a($model->org_name, ['school',
	    		'urlLoc' => $model->url_loc, 
	    		'urlName' => $model->url_name, 
	    		'id' => $model->id]) 
	    	?>
	    </h4>
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
	case Profile::TYPE_STAFF: ?>
	
	   	<h4>
	        <?= $model->spouse_first_name == NULL ? 
	        	Html::a($model->ind_first_name . ' ' . $model->ind_last_name, ['staff',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]) :
	    		Html::a($model->ind_first_name . ' (& ' . $model->spouse_first_name . ') ' . $model->ind_last_name, ['staff',
		    		'urlLoc' => $model->url_loc, 
		    		'urlName' => $model->url_name, 
		    		'id' => $model->id]);
	    	?>
	    </h4>
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