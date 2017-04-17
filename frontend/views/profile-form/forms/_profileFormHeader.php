<?php

use common\models\profile\Profile;
use common\widgets\Alert;
use yii\bootstrap\Html;

?>

    <div class="my-profiles">
        <div class="row">
            <div class="container">
                <h1><?= $this->title ?></h1>
                <?php if ($profile->status != Profile::STATUS_ACTIVE) { ?>
                    <div class = "col-md-4">
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
    </div>
    <div class="clearprofiles"></div>
    <?= Alert::widget() ?>