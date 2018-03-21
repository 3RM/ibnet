<?php

use common\models\profile\Profile;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Profile Forms Menu';
$progress = $profile->getProgress();
?>

<div class="account-header-container">
    <div class="account-header acc-forms-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class = "col-md-4">
            <div class="progress" >
                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?= $progressPercent ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $progressPercent ?>%"><span style="color:#444"><?= $progressPercent ?>%</span>
                    <span class="sr-only"><?= $progressPercent ?>% Complete</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrap profile-form">
    <div class="container">
        <div class="row">
            <p>Continue where you left off or revise a completed form:</p>
        </div>

        <div class="row">
            <ul style="list-style-type: none;">  
                <?php $i = 0; $j=0;
                while ($i <= $count) {
                    if ($formList[$i] == 'Skip') {
                        $i++;
                        continue;
                    }
                    if ($typeMask[$i] == 1) { 
                        if ($progress[$i] == 1) { ?>

                            <li> <?= Html::icon('check') . '&nbsp;&nbsp;' . Html::a($formList[$i], ['profile-form/form-route',
                                'type' => $profile->type, 'fmNum' => $i-1, 'id' => $profile->id])  ?></li>
                        <?php } else { ?>

                            <?php if ($j == 0) {      // Create link for next form after last form completed ?>
                                <li> <?= Html::icon('unchecked') . '&nbsp;&nbsp;' . Html::a($formList[$i], ['profile-form/form-route',
                                'type' => $profile->type, 'fmNum' => $i-1, 'id' => $profile->id]) ?></li>
                                <?php $j++; 
                            } else { ?>
                                <li> <?= Html::icon('unchecked') . '&nbsp;&nbsp;' . $formList[$i] ?></li>
                            <?php }
                        }
                    }
                    $i++;
                } ?>
                <?php if ($j == 0) {       // Create link for activate page if last form has been completed ?>
                    <li> <?= Html::icon('unchecked') . '&nbsp;&nbsp;' . Html::a('Activate', ['preview/view-preview', 'id' => $profile->id]) ?></li>
                <?php } else { ?>
                    <li> <?= Html::icon('unchecked') . '&nbsp;&nbsp; Activate' ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>

</div>
