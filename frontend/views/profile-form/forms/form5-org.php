<?php

use common\models\profile\Profile;
use common\models\Utility;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Staff';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <?php if ($staffArray) { ?>

            <div class="row">
                <div class="col-md-11">
                    <?= isset($staffArray[1]) ? '<p>' . Html::icon('search') . ' We found people in the directory who have identified themselves as staff members.  Use the buttons to add or remove them.</p>' :
                    '<p>' . Html::icon('search') . ' We found a person in the directory who has identified themself as a staff member.  Use the button to add or remove.</p>' ?>
                    <?= Html::activeHiddenInput($profile, 'staff') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-heading"><?= $profile->type ?> Staff</div>
                        <table class="table table-hover">
                            <?php foreach($staffArray as $staff) { ?>
                            <tr>
                                <td class="center"><b><?= $staff->staff_title ?></b></td>
                                <td class="center"><?= $staff->{'profile'}->formattedNames ?></td>
                                <td class="center"><?= $staff->{'profile'}->ind_city . ', ' . $staff->{'profile'}->ind_st_prov_reg ?></td>
                                <td class="center"><?= $staff->confirmed == NULL ?
                                    'Uncomfirmed' :
                                    'Confirmed Staff' ?></td>
                                <td class="center"><?= $staff->confirmed == NULL ?
                                    Html::submitButton(HTML::icon('ok') . ' Add', [
                                        'id' => 'add_submit',
                                        'method' => 'POST',
                                        'class' => 'btn btn-form btn-sm',
                                        'name' => 'add',
                                        'value' => $staff->id,
                                    ]) :
                                    Html::submitButton(HTML::icon('remove') . ' Remove', [
                                        'method' => 'POST',
                                        'class' => 'btn btn-form btn-sm',
                                        'name' => 'remove',
                                        'value' => $staff->id,
                                    ]); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

        <?php } else { ?>

            <div class="row">
                <div class="col-md-11">
                    <p><?= Html::icon('search') ?> We did not find any staff for your ministry in the directory.  Staff profiles can be a great way to help the public get aquainted with your ministry.</p>
                    <?= Html::activeHiddenInput($profile, 'staff'); ?>
                </div>
            </div>

        <?php } ?>

        <br>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>