<?php
use common\models\Utility;
use common\models\group\Group;
use common\models\profile\Profile;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">' . html::img('@bimages/ibnet-header-sm.png') . '</span><span class="logo-lg">' . html::img('@bimages/ibnet-header.png') . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <!-- Messages: style can be found in dropdown.less-->
                <!-- <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success">4</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 4 messages</li>
                        <li>-->
                            <!-- inner menu: contains the actual data -->
                            <!--<ul class="menu">
                                <li>--><!-- start message -->
                                    <!--<a href="#">
                                        <div class="pull-left">
                                            <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle"
                                                 alt="User Image"/>
                                        </div>
                                        <h4>
                                            Support Team
                                            <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>-->
                                <!-- end message -->
                                <!--<li>
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?= $directoryAsset ?>/img/user3-128x128.jpg" class="img-circle"
                                                 alt="user image"/>
                                        </div>
                                        <h4>
                                            AdminLTE Design Team
                                            <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?= $directoryAsset ?>/img/user4-128x128.jpg" class="img-circle"
                                                 alt="user image"/>
                                        </div>
                                        <h4>
                                            Developers
                                            <small><i class="fa fa-clock-o"></i> Today</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?= $directoryAsset ?>/img/user3-128x128.jpg" class="img-circle"
                                                 alt="user image"/>
                                        </div>
                                        <h4>
                                            Sales Department
                                            <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?= $directoryAsset ?>/img/user4-128x128.jpg" class="img-circle"
                                                 alt="user image"/>
                                        </div>
                                        <h4>
                                            Reviewers
                                            <small><i class="fa fa-clock-o"></i> 2 days</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="#">See All Messages</a></li>
                    </ul>
                </li>-->
                <!--<li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">10</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 10 notifications</li>
                        <li> -->
                            <!-- inner menu: contains the actual data -->
                            <!--<ul class="menu">
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-warning text-yellow"></i> Very long description here that may
                                        not fit into the page and may cause design problems
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users text-red"></i> 5 new members joined
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-user text-red"></i> You changed your username
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="#">View all</a></li>
                    </ul>
                </li> -->
                <!-- Forwarding Email Request: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu">
                    <?= ($cntH = Profile::find()->where(['email_pvt_status' => Profile::PRIVATE_EMAIL_PENDING])->count()) ?
                        Html::a('<i class="glyphicon glyphicon-eye-close"></i><span class="label label-info">' . $cntH . '</span>', '/directory/forwarding') :
                        Html::a('<i class="glyphicon glyphicon-eye-close"></i>', '/directory/forwarding') ?>
                </li>
                <!-- Flagged Profiles: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu">
                    <?= ($cntF = Profile::find()->where(['inappropriate' => 1])->count()) ?
                        Html::a('<i class="fa fa-flag-o"></i><span class="label label-danger">' . $cntF . '</span>', '/directory/flagged') :
                        Html::a('<i class="fa fa-flag-o"></i>', '/directory/flagged') ?>
                </li>
                <!-- Pending Group Emails: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu">
                    <?= ($cntP = Group::find()
                            ->where(['and',
                                ['feature_prayer' => 1],
                                ['prayer_email' => NULL],
                                ['prayer_email_pwd' => NULL],
                            ])
                            ->orWhere(['and',
                                ['feature_notification' => 1],
                                ['notice_email' => NULL],
                                ['notice_email_pwd' => NULL],
                            ])
                            ->andWhere(['status' => Group::STATUS_ACTIVE])
                            ->count()) ?
                        Html::a('<i class="fa fa-send"></i><span class="label label-danger">' . $cntP . '</span>', '/groups/pending-emails') :
                        Html::a('<i class="fa fa-send"></i>', '/groups/pending-emails') ?>
                </li>
                <!-- User Account: style can be found in dropdown.less -->

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?= empty($user->usr_image) ? 
                            Html::img('@web/images/user.png', ['class' => 'user-image img-circle', 'alt' => 'User image']) :
                            Html::img(\Yii::$app->params['url.frontend'] . $user->usr_image, ['class' => 'user-image img-circle', 'alt' => 'User image']) ?>
                        <span class="hidden-xs"><?= $user->first_name . ' ' . $user->last_name ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <?= empty($user->usr_image) ? 
                                Html::img('@web/images/user.png', ['class' => 'img-circle', 'alt' => 'User image']) :
                                Html::img(\Yii::$app->params['url.frontend'] . $user->usr_image, ['class' => 'img-circle', 'alt' => 'User image']) ?>
                            <p>
                                <?php $assignment = $user->assignment ?>
                                <?= $user->first_name . ' ' . $user->last_name . ' - ' . $assignment->item_name ?>
                                <small>Member since <?= Yii::$app->formatter->asDate($user->created_at, 'php:F Y') ?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-4 text-center">
                                <a href="#">Followers</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Sales</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Friends</a>
                            </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
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
