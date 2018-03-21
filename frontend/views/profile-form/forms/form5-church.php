<?php

use common\models\profile\Profile;
use common\models\Utility;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Church Staff';
?>

<?php $this->registerJs(<<<'EOD'
    $('#add_submit').on('beforeSubmit', function (e) {
        $("#profile-ind_first_name").val() = $staff->ind_first_name;
        $("#profile-ind_last_name").val() = $staff->ind_last_name;
        return true;
    });
EOD
) ?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <?php if ($srPastor) { ?>

            <div class="row">
                <div class="col-md-5">
                    <h3>Senior Pastor</h3>
                    <div class="panel panel-default">
                        <div class="panel-heading">Pastor Name</div>
                        <table class="table table-hover">
                            <tr>
                                <td class="center">
                                    <?= $srPastor->formattedNames ?>
                                </td>
                                <td class="center">
                                    <?= Html::submitButton(HTML::icon('remove') . ' Remove', [
                                        'method' => 'POST',
                                        'class' => 'btn btn-form btn-sm',
                                        'name' => 'clear',
                                        'value' => $srPastor->id,
                                    ]) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?= $form->field($profile, 'pastor_interim')->checkbox() ?>
                    <?= $form->field($profile, 'cp_pastor', ['options' => ['id' => 'chkbx_cp_pastor']])->checkbox()?>
        
                    <?= Html::activeHiddenInput($profile, 'ind_first_name'); ?>
                    <?= Html::activeHiddenInput($profile, 'ind_last_name'); ?>
                    <?= Html::activeHiddenInput($profile, 'spouse_first_name'); ?>
                </div>
            </div>
            
            <br>
    
        <?php } else { ?>
        
            <?= empty($staffArray) ? NULL : '<p>Enter a Pastor name, or choose a pastor profile from the list below.</p>' ?>
            <div class="row">
                <div class="col-md-4">
                    <h3>Senior Pastor Name</h3>
                    <?= $form->field($profile, 'ind_first_name')->textInput(['maxlength' => true]); ?>
                    <?= $form->field($profile, 'ind_last_name')->textInput(['maxlength' => true]); ?>
                    <?= $form->field($profile, 'spouse_first_name')->textInput(['maxlength' => true]); ?>
                    <?= $form->field($profile, 'pastor_interim')->checkbox() ?>
                    <?= $form->field($profile, 'cp_pastor', ['options' => ['id' => 'chkbx_cp_pastor']])->checkbox() ?>
                </div>
            </div>

            <br>

        <?php } ?>

        <div class="row">
            <div class="col-md-11">
                <h3>Additional Staff</h3>
                <?php if ($staffArray) { ?>
                    <?= isset($staffArray[1]) ? '<p>' . Html::icon('search') . ' We found people in the directory who have identified themselves as church staff members. "Staff" includes anyone in ministry who serves under the ministry of the church, and need not be in a paid staff position.</p>' :
                    '<p>' . Html::icon('search') . ' We found a person in the directory who has identified themself as a church staff member.  "Staff" includes anyone in ministry who serves under the ministry of the church, and need not be in a paid staff position.</p>' ?>
                <?php } else { ?>
                    <p><?= Html::icon('search') ?> We did not find any pastor or staff for your church in the directory.  Pastor and staff profiles can be a great way to help the public get aquainted with your church.</p>
                <?php } ?>
            </div>
        </div>

        <?php if ($staffArray) { ?>
            
            <div class="row">
                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">Church Staff</div>
                        <table class="table table-hover">
                            <?php foreach($staffArray as $staff) { ?>
                            <tr>
                                <td class="center"><b><?= $staff->{'profile'}->sub_type == 'Missionary' ? 'Church Planting Pastor' : $staff->staff_title ?></b></td>
                                <td class="center"><?= $staff->{'profile'}->spouse_first_name ? $staff->{'profile'}->ind_first_name . ' (& ' . $staff->{'profile'}->spouse_first_name . ') ' . $staff->{'profile'}->ind_last_name : $staff->{'profile'}->ind_first_name . ' ' . $staff->{'profile'}->ind_last_name; ?></td>
                                <td class="center"><?= $staff->{'profile'}->ind_city . ', ' ?><?= $staff->{'profile'}->ind_st_prov_reg ? $staff->{'profile'}->ind_st_prov_reg : NULL ?></td>
                                <td class="center"><?= $staff->confirmed == NULL ? 'Uncomfirmed' : 'Confirmed Staff' ?></td>
                                

                                <td class="center">
                                    <?php if ($staff->confirmed == NULL) { ?>
                                        <?= Html::submitButton(HTML::icon('ok') . ' Add Staff', [
                                            'id' => 'add_submit',
                                            'method' => 'POST',
                                            'class' => 'btn btn-form btn-sm',
                                            'name' => 'add',
                                            'value' => $staff->id,
                                        ]); ?>
                                        

                                        <?php if ($staff['sr_pastor'] == NULL && 
                                            ($staff->staff_title == 'Senior Pastor' || $staff->staff_title == 'Pastor' || 
                                            ($staff->staff_title == 'Church Planter' && $staff->home_church != $profile->id))) { ?>
                                            <?= Html::submitButton(HTML::icon('ok') . ' Add Sr. Pastor', [
                                                'method' => 'POST',
                                                'class' => 'btn btn-form btn-sm',
                                                'name' => 'senior',
                                                'value' => $staff->staff_id . '+' . $staff->id,
                                            ]); ?>
                                        <?php } ?>


                                    <?php } else { ?>
                                        <?= Html::submitButton(HTML::icon('remove') . ' Remove', [
                                            'method' => 'POST',
                                            'class' => 'btn btn-form btn-sm',
                                            'name' => 'remove',
                                            'value' => $staff->id,
                                        ]); ?>
                                    <?php } ?>
                                </td>
                                
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

        <?php } ?>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>
        
    </div>

</div>