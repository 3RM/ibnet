<?php
use yii\helpers\Html;

?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?= empty($user->usr_image) ? 
                    Html::img('@web/images/user.png', ['class' => 'img-circle', 'alt' => 'User image']) :
                    Html::img(\Yii::$app->params['url.frontend'] . $user->usr_image, ['class' => 'img-circle', 'alt' => 'User image']) ?>
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
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => [
                    ['label' => 'Tools Menu', 'options' => ['class' => 'header']],
                    ['label' => 'Stats', 'icon' => 'bar-chart', 'url' => ['stats/stats']],
                    [
                        'label' => 'Accounts', 
                        'icon' => 'user', 
                        'url' => '#',
                        'items' => [
                            ['label' => 'Users', 'icon' => 'address-card', 'url' => ['/accounts/users'],],
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
                                    ['label' => 'Missionary Update', 'icon' => 'sitemap', 'url' => ['/directory/missionary-update'],],
                                    ['label' => 'Mission Housing', 'icon' => 'sitemap', 'url' => ['/directory/housing'],],
                                    ['label' => 'Association', 'icon' => 'sitemap', 'url' => ['/directory/association'],],
                                    ['label' => 'Fellowship', 'icon' => 'sitemap', 'url' => ['/directory/fellowship'],],
                                    ['label' => 'History', 'icon' => 'sitemap', 'url' => ['/directory/history'],],
                                ],
                            ],
                            ['label' => 'Forwarding Email Requests', 'icon' => 'send', 'url' => ['/directory/forwarding'],],
                            ['label' => 'Flagged', 'icon' => 'flag', 'url' => ['/directory/flagged'],],
                        ],
                    ],
                    [
                        'label' => 'Groups', 
                        'icon' => 'users', 
                        'url' => '#',
                        'items' => [
                            ['label' => 'Groups', 'icon' => 'users', 'url' => ['/groups/groups'],],
                            ['label' => 'Tables', 
                                'icon' => 'table', 
                                'items' => [
                                    ['label' => 'Group Member', 'icon' => 'sitemap', 'url' => ['/groups/group-member'],],
                                    ['label' => 'Prayer', 'icon' => 'sitemap', 'url' => ['/groups/prayer'],],
                                    ['label' => 'Prayer Update', 'icon' => 'sitemap', 'url' => ['/groups/prayer-update'],],
                                    ['label' => 'Prayer Tag', 'icon' => 'sitemap', 'url' => ['/groups/prayer-tag'],],
                                    ['label' => 'Calendar Event', 'icon' => 'sitemap', 'url' => ['/groups/calendar-event'],],
                                    ['label' => 'iCalendar Url', 'icon' => 'sitemap', 'url' => ['/groups/icalendar-url'],],
                                    ['label' => 'Notification', 'icon' => 'sitemap', 'url' => ['/groups/notification'],],
                                    ['label' => 'Group Place', 'icon' => 'sitemap', 'url' => ['/groups/group-place'],],
                                    ['label' => 'Group Keyword', 'icon' => 'sitemap', 'url' => ['/groups/group-keyword'],],
                                    ['label' => 'Group Invite', 'icon' => 'sitemap', 'url' => ['/groups/group-invite'],],
                                    ['label' => 'Group Alert Queue', 'icon' => 'sitemap', 'url' => ['/groups/group-alert-queue'],],
                                ],
                            ],
                            ['label' => 'Pending New Emails', 'icon' => 'send', 'url' => ['/groups/pending-emails'],],
                        ],
                    ],
                    ['label' => 'Mail', 'icon' => 'envelope', 'url' => '/campaign/mailchimp'],
                    ['label' => 'Solr', 'icon' => 'sun-o', 'url' => '/solr/panel'],
                    ['label' => 'Database', 'icon' => 'database', 'url' => '/database/db'],
                    [
                        'label' => 'Application', 
                        'icon' => 'server', 
                        'url' => ['#'],
                        'items' => [
                            ['label' => 'Logs', 'icon' => 'file-text-o', 'url' => '/logreader'],
                            ['label' => 'Cron Jobs', 'icon' => 'clock-o', 'url' => ['/server/cron'],],
                            ['label' => 'Clear Yii Cache', 'icon' => 'minus-circle', 'url' => ['/server/cache'],],
                        ],
                    ],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                ],
            ]
        ) ?>

    </section>

</aside>
