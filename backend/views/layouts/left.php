<?php
use yii\helpers\Html;

?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?= empty($user->image) ? 
                                Html::img('@web/images/user.png', ['class' => 'img-circle', 'alt' => 'User image']) : 
                                Html::img($user->image, ['class' => 'img-circle', 'alt' => 'User image']) ?>
            </div>
            <div class="pull-left info">
                <p><?= $user->first_name . ' ' . $user->last_name ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <!-- <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div> -->
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    ['label' => 'Tools', 'options' => ['class' => 'header']],
                    ['label' => 'Stats', 'icon' => 'bar-chart', 'url' => ['/debug']],
                    [
                        'label' => 'Users', 
                        'icon' => 'user', 
                        'items' => [
                            ['label' => 'Accounts', 'icon' => 'id-badge', 'url' => ['/accounts/users'],],
                            ['label' => 'RBAC', 
                                'icon' => 'sitemap', 
                                'items' => [
                                    ['label' => 'Assignments', 'icon' => 'sitemap', 'url' => ['/accounts/assignments'],],
                                    ['label' => 'Roles', 'icon' => 'sitemap', 'url' => ['/accounts/roles'],],
                                    ['label' => 'Permissions', 'icon' => 'sitemap', 'url' => ['/accounts/permissions'],],
                                    ['label' => 'Rules', 'icon' => 'sitemap', 'url' => ['/accounts/rules'],],
                                ],
                            ],
                        ],
                    ],
                    [
                        'label' => 'Directory',
                        'icon' => 'address-book',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Profiles', 'icon' => 'address-card', 'url' => ['/directory/profiles'],],
                            ['label' => 'Tables', 
                                'icon' => 'table', 
                                'items' => [
                                    ['label' => 'Social', 'icon' => 'sitemap', 'url' => ['/directory/social'],],
                                    ['label' => 'Staff', 'icon' => 'sitemap', 'url' => ['/directory/staff'],],
                                    ['label' => 'Missionary', 'icon' => 'sitemap', 'url' => ['/directory/missionary'],],
                                    ['label' => 'Mission Housing', 'icon' => 'sitemap', 'url' => ['/directory/housing'],],
                                    ['label' => 'Association', 'icon' => 'sitemap', 'url' => ['/directory/association'],],
                                    ['label' => 'Fellowship', 'icon' => 'sitemap', 'url' => ['/directory/fellowship'],],
                                ],
                            ],
                            ['label' => 'Forwarding Email Requests', 'icon' => 'send', 'url' => ['/directory/forwarding'],],
                            ['label' => 'Flagged', 'icon' => 'flag', 'url' => ['/directory/flagged'],],
                        ],
                    ],
                    ['label' => 'Database', 'icon' => 'database', 'url' => ['/debug']],
                    [
                        'label' => 'Server', 
                        'icon' => 'server', 
                        'url' => ['#'],
                        'items' => [
                            ['label' => 'Cron Jobs', 'icon' => 'clock-o', 'url' => ['/server/cron'],],
                        ],
                    ],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                ],
            ]
        ) ?>

    </section>

</aside>
