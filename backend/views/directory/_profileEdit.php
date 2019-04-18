<?php

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <p>Profile <?= $profile->id ?></p>

    <?php $form = ActiveForm::begin(['action' => '/directory/update']); ?>

    <?= $form->field($profile, 'profile_name')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'type')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'sub_type')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'category')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'transfer_token')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'url_name')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'url_loc')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'has_been_inactivated')->checkbox() ?> 
    <?= $form->field($profile, 'edit')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'tagline')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'title')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'description')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'ministry_of')->textInput(['maxlength' => true])->label('Parent Ministry (ministry_of)') ?> 
    <?= $form->field($profile, 'home_church')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'image1')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'image2')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'flwsp_ass_level')->textInput(['maxlength' => true])->label('Fellowship/Association Level (flwsp_ass_level)') ?> 
    <?= $form->field($profile, 'org_name')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'org_address1')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'org_address2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_po_box')->textInput(['maxlength' => true])->label('Org PO Box') ?>      
    <?= $form->field($profile, 'org_city')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'org_st_prov_reg')->textInput(['maxlength' => true]) ?>  
    <?= $form->field($profile, 'org_state_long')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_zip')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'org_country')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_po_address1')->textInput(['maxlength' => true])->label('Org Mailing Address 1') ?>
    <?= $form->field($profile, 'org_po_address2')->textInput(['maxlength' => true])->label('Org Mailing Address 2') ?>
    <?= $form->field($profile, 'org_po_city')->textInput(['maxlength' => true])->label('Org Mailing City') ?> 
    <?= $form->field($profile, 'org_po_st_prov_reg')->textInput(['maxlength' => true])->label('Org Mailing State') ?>  
    <?= $form->field($profile, 'org_po_state_long')->textInput(['maxlength' => true])->label('Org Mailing State Long') ?>
    <?= $form->field($profile, 'org_po_zip')->textInput(['maxlength' => true])->label('Org Mailing Zip') ?>     
    <?= $form->field($profile, 'org_po_country')->textInput(['maxlength' => true])->label('Org Mailing Country') ?>
    <?= $form->field($profile, 'ind_first_name')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'ind_last_name')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'spouse_first_name')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'ind_address1')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'ind_address2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_city')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'ind_po_box')->textInput(['maxlength' => true])->label('Ind PO Box') ?>      
    <?= $form->field($profile, 'ind_st_prov_reg')->textInput(['maxlength' => true]) ?>  
    <?= $form->field($profile, 'ind_state_long')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_zip')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'ind_country')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_po_address1')->textInput(['maxlength' => true])->label('Ind Mailing Address 1') ?> 
    <?= $form->field($profile, 'ind_po_address2')->textInput(['maxlength' => true])->label('Ind Mailing Address 2') ?>
    <?= $form->field($profile, 'ind_po_city')->textInput(['maxlength' => true])->label('Ind Mailing City') ?> 
    <?= $form->field($profile, 'ind_po_st_prov_reg')->textInput(['maxlength' => true])->label('Ind Mailing State') ?>  
    <?= $form->field($profile, 'ind_po_state_long')->textInput(['maxlength' => true])->label('Ind Mailing State Long') ?>
    <?= $form->field($profile, 'ind_po_zip')->textInput(['maxlength' => true])->label('Ind Mailing Zip') ?>      
    <?= $form->field($profile, 'ind_po_country')->textInput(['maxlength' => true])->label('Ind Mailing Country') ?>
    <?= $form->field($profile, 'show_map')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'phone')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'email')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'email_pvt')->textInput(['maxlength' => true])->label('Private Email (email_pvt)') ?>      
    <?= $form->field($profile, 'email_pvt_status')->textInput(['maxlength' => true])->label('Private Email Status (email_pvt_status)') ?>      
    <?= $form->field($profile, 'website')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'pastor_interim')->checkbox() ?>      
    <?= $form->field($profile, 'cp_pastor')->checkbox() ?>      
    <?= $form->field($profile, 'bible')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'worship_style')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'polity')->textInput(['maxlength' => true]) ?>      
    
    <?= Html::submitButton('Save', [
        'name' => 'save',
        'value' => $profile->id,
        'method' => 'post',
        'class' => 'btn-main',
        'onclick' => 'return confirm("Be careful! You are updating user data. Do you have admin and/or user authorization to make changes? Click to confirm.")'
    ]); ?> 

    <?php $form = ActiveForm::end(); ?>
