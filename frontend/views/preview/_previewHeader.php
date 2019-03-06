<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use frontend\controllers\ProfileController;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
?>

<div class="account-preview-header-container">
    <div class="account-header acc-preview-header">
        <div class="preview-header-container">
            <?= $activate ? 
                '<h1>' . Html::icon('edit') . ' Preview & Activate</h1>' :
                '<h1>' . Html::icon('edit') . ' Preview & Edit</h1>' ?>
            <div id="open" class="preview-edit-menu"><?= Html::a(Html::icon('menu-hamburger') . ' Open Edit Menu', '#') ?></div>
            <br />
            <br />
            <?php $profile->status == Profile::STATUS_ACTIVE ? 
                print('<p class="progress-menu">' . Html::a(Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'id' => $profile->id, 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name], 'https') . ' ' . Html::icon('new-window'), ['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name, 'id' => $profile->id], ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '</p>') :
                NULL; ?>
            <?php $form = ActiveForm::begin(); ?>
            <?= $activate ?
                Html::submitButton('Activate', [
                    'method' => 'POST',
                    'class' => 'btn preview-btn pull-right',
                    'name' => 'activate',
                ]) :
                Html::submitButton('Finished', [
                    'method' => 'POST',
                    'class' => 'btn preview-btn pull-right',
                    'name' => 'finished',
                ]) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?= Alert::widget() ?>