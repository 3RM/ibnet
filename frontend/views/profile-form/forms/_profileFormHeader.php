<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use yii\bootstrap\Html;

?>

<div class="account-header-container">
    <div class="account-header acc-forms-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($profile->status != Profile::STATUS_ACTIVE) { ?>
            <div class = "forms-header-menu">
                <div class="progress-menu"><?= Html::a(Html::icon('menu-hamburger'), ['forms-menu', 'id' => $profile->id]); ?></div>
                <div class="progress">
                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?= $pp ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $pp ?>%">
                        <span style="color:#444"><?= $pp; ?>% Complete</span>
                        <span class="sr-only"><?= $pp ?>% Complete</span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?= Alert::widget() ?>