<?php
use common\models\profile\Profile;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">' . html::img('@img.network/network-logo-sm.png') . '</span><span class="logo-lg">' . html::img('@img.network/network-logo.png') . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <!-- Forwarding Email Request: style can be found in dropdown.less -->
                <li class="header-nav-link">
                    <?= Html::a('Home', '/site/index') ?>
                </li class="header-nav-link">
                <li>
                    <?= Html::a('Browse', '/profile/browse') ?>
                </li class="header-nav-link">
                <li>
                    <?= Html::a('Blog', 'https://blog.ibnet.org') ?>
                </li>

                <!-- User Account: style can be found in dropdown.less -->

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?= empty($user->usr_image) ? 
                            Html::img('@img.site/user.png', ['class' => 'user-image img-circle', 'alt' => 'User image']) :
                            Html::img($user->usr_image, ['class' => 'user-image img-circle', 'alt' => 'User image']) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- Menu Body -->
                        <li class="user-body">
                            <p><?= $user->fullName ?></p>
                            <?= Html::a('My Account',
                                ['/site/settings'],
                                ['class' => 'btn btn-dropdown']
                            ) ?>
                            <?= Html::a('My Profiles',
                                ['/profile-mgmt/my-profiles'],
                                ['class' => 'btn btn-dropdown']
                            ) ?>
                            <hr>
                            <?= Html::a('Sign out',
                                ['/site/logout'],
                                ['data-method' => 'post', 'class' => 'btn btn-dropdown']
                            ) ?>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
