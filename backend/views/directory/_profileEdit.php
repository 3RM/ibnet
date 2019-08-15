<?php

use common\models\profile\Bible;
use common\models\profile\Profile;
use common\models\profile\Polity;
use common\models\profile\WorshipStyle; 
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
?>
    <p>Profile <?= $profile->id ?></p>

    <?php $form = ActiveForm::begin(['action' => '/directory/update']); ?>

    <?= $form->field($profile, 'profile_name')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'type')->dropDownList([
        Profile::TYPE_PASTOR => 'Pastor',
        Profile::TYPE_EVANGELIST => 'Evangelist',
        Profile::TYPE_MISSIONARY => 'Missionary',
        Profile::TYPE_CHAPLAIN => 'Chaplain',
        Profile::TYPE_STAFF => 'Staff',
        Profile::TYPE_CHURCH => 'Church',
        Profile::TYPE_MISSION_AGCY => 'Mission Agency',
        Profile::TYPE_FELLOWSHIP => 'Fellowship',
        Profile::TYPE_ASSOCIATION => 'Association',
        Profile::TYPE_CAMP => 'Camp',
        Profile::TYPE_SCHOOL => 'School',
        Profile::TYPE_PRINT => 'Print Ministry',
        Profile::TYPE_MUSIC => 'Music Ministry',
        Profile::TYPE_SPECIAL => 'Special Ministry',
    ]) ?> 
    <?= $form->field($profile, 'sub_type')->dropDownList([
        Profile::SUBTYPE_PASOTR_ASSOCIATE => 'Associate Pastor', 
        Profile::SUBTYPE_PASTOR_ASSISTANT => 'Assistant Pastor',
        Profile::SUBTYPE_PASTOR_MUSIC => 'Music Pastor',
        Profile::SUBTYPE_PASTOR_PASTOR => 'Pastor',
        Profile::SUBTYPE_PASTOR_EMERITUS => 'Pastor Emeritus',
        Profile::SUBTYPE_PASTOR_SENIOR => 'Senior Pastor',
        Profile::SUBTYPE_PASTOR_YOUTH => 'Youth Pastor',
        Profile::SUBTYPE_PASTOR_ELDER => 'Elder',
        Profile::SUBTYPE_MISSIONARY_CP => 'Church Planter',
        Profile::SUBTYPE_MISSIONARY_BT => 'Bible Translator',
        Profile::SUBTYPE_MISSIONARY_MM => 'Medical Missionary',
        Profile::SUBTYPE_CHAPLAIN_J => 'Jail Chaplain',
        Profile::SUBTYPE_CHAPLAIN_M => 'Military Chaplain',
        Profile::TYPE_EVANGELIST => 'Evangelist',
        Profile::TYPE_STAFF => 'Staff',
        Profile::TYPE_CHURCH => 'Church',
        Profile::TYPE_MISSION_AGCY => 'Mission Agency',
        Profile::TYPE_FELLOWSHIP => 'Fellowship',
        Profile::TYPE_ASSOCIATION => 'Association',
        Profile::TYPE_CAMP => 'Camp',
        Profile::TYPE_SCHOOL => 'School',
        Profile::TYPE_PRINT => 'Print Ministry',
        Profile::TYPE_MUSIC => 'Music Ministry',
        Profile::TYPE_SPECIAL => 'Special Ministry',
    ]) ?>
    <?= $form->field($profile, 'category')->dropDownList([
        Profile::CATEGORY_IND => 'Individual', 
        Profile::CATEGORY_ORG => 'Organization'
    ]) ?>
    <?= $form->field($profile, 'transfer_token')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'url_name')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'url_loc')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'has_been_inactivated')->checkbox() ?> 
    <?= $form->field($profile, 'edit')->dropDownList([
        Profile::EDIT_NO => 'No', 
        Profile::EDIT_YES => 'Yes'
    ], ['id' => 'profile-edit-input']) ?> 
    <?= $form->field($profile, 'tagline')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'title')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'description')->textArea(['rows' => 3]) ?> 
    <?= $form->field($profile, 'ministry_of')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'home_church')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'image1')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'image2')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'flwsp_ass_level')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'org_name')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'org_address1')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'org_address2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_po_box')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'org_city')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'org_st_prov_reg')->textInput(['maxlength' => true]) ?>  
    <?= $form->field($profile, 'org_state_long')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_zip')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'org_country')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_po_address1')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_po_address2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_po_city')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'org_po_st_prov_reg')->textInput(['maxlength' => true]) ?>  
    <?= $form->field($profile, 'org_po_state_long')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'org_po_zip')->textInput(['maxlength' => true]) ?>     
    <?= $form->field($profile, 'org_po_country')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_first_name')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'ind_last_name')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'spouse_first_name')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'ind_address1')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'ind_address2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_city')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'ind_po_box')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'ind_st_prov_reg')->textInput(['maxlength' => true]) ?>  
    <?= $form->field($profile, 'ind_state_long')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_zip')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'ind_country')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_po_address1')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'ind_po_address2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_po_city')->textInput(['maxlength' => true]) ?> 
    <?= $form->field($profile, 'ind_po_st_prov_reg')->textInput(['maxlength' => true]) ?>  
    <?= $form->field($profile, 'ind_po_state_long')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'ind_po_zip')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'ind_po_country')->textInput(['maxlength' => true]) ?>
    <?= $form->field($profile, 'show_map')->dropDownList([
        Profile::MAP_PRIMARY => 'Primary Address',
        Profile::MAP_CHURCH => 'Home Church Address',
        Profile::MAP_MINISTRY => 'Primary Ministry Address',
        Profile::MAP_CHURCH_PLANT => 'Church Plant Address',
    ]) ?>      
    <?= $form->field($profile, 'phone')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'email')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'email_pvt')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'email_pvt_status')->dropDownList([
        Profile::PRIVATE_EMAIL_NONE => 'No private email',
        Profile::PRIVATE_EMAIL_ACTIVE => 'Active',
        Profile::PRIVATE_EMAIL_PENDING => 'Pending',
    ]) ?>      
    <?= $form->field($profile, 'website')->textInput(['maxlength' => true]) ?>      
    <?= $form->field($profile, 'pastor_interim')->checkbox() ?>      
    <?= $form->field($profile, 'cp_pastor')->checkbox() ?>
    <?php if ($profile->category == Profile::CATEGORY_ORG) {
        echo $form->field($profile, 'bible')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Bible::find()->orderBy('id')->all(), 'bible', 'bible'),
            'hideSearch' => true,
            'pluginOptions' => ['allowClear' => false],
        ])->label('Bible');
        echo $form->field($profile, 'worship_style')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(WorshipStyle::find()->orderBy('id')->all(), 'style', 'style'),
            'hideSearch' => true,
            'pluginOptions' => ['allowClear' => false],
        ])->label('Worship');
        echo $form->field($profile, 'polity')->widget(Select2::classname(), [                 
            'data' => ArrayHelper::map(Polity::find()->orderBy('id')->all(), 'polity', 'polity'),
            'hideSearch' => true,
            'pluginOptions' => ['allowClear' => false],
        ])->label('Church Government'); 
    } ?> 
    
    <?= Html::submitButton('Save', [
        'name' => 'save',
        'value' => $profile->id,
        'method' => 'post',
        'class' => 'btn-main',
        'onclick' => 'return confirm("Be careful! You are updating user data. Do you have admin and/or user authorization to make changes? Click to confirm.")'
    ]); ?> 

    <?php $form = ActiveForm::end(); ?>
