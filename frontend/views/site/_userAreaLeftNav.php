<?php
use common\models\User;
use yii\bootstrap\Html;
?>
    <div class="left-nav">
        <ul class="main-links">
            <?= Html::a('
            	<li class="settings ' . ($active == "settings" ? "active" : NULL) . '">
            		<span class="glyphicons glyphicons-settings"></span>
            		<span class="left-nav-linktext">&nbsp;Settings</span>
            	</li>
            ', '/site/settings') ?>
            <?= Html::a('
            	<li class="profiles ' . ($active == "profiles" ? "active" : NULL) . '">
            		<span class="glyphicons glyphicons-vcard"></span>
            		<span class="left-nav-linktext">&nbsp;Profiles</span>
            	</li>
            ', '/profile-mgmt/my-profiles') ?>
            <?= (($role == User::ROLE_SAFEUSER) || ($role == User::ROLE_ADMIN)) ? 
                Html::a('
                	<li class="groups ' . ($active == "groups" ? "active" : NULL) . '">
                		<span class="glyphicons glyphicons-cluster"></span>
                		<span class="left-nav-linktext">&nbsp;Groups</span>
                	</li>
                ', '/group/my-groups') :
                NULL
            ?>
            <?= Yii::$app->user->identity->isMissionary ? Html::a('
            	<li class="updates ' . ($active == "updates" ? "active" : NULL) . '">
            		<span class="glyphicons glyphicons-direction"></span>
            		<span class="left-nav-linktext">&nbsp;Updates</span>
            	</li>
            ', '/missionary/update-repository') : NULL ?>
        </ul>
        <?php if ((($role == User::ROLE_SAFEUSER) || ($role == User::ROLE_ADMIN)) && is_array($joinedGroups) && count($joinedGroups) > 0) { ?>
            <div class="group-nav-links">
                <h3>Groups</h3>
                <?php foreach ($joinedGroups as $group) { ?>
                    <p <?= 'id="group-' . $group->id . '"' ?>><?= $group->name ?></p>
                    <ul <?= 'id="list-' . $group->id . '"' ?> <?= $gid == $group->id ? NULL : 'class="group-list"' ?>>
                        <?= $group->feature_prayer ? 
                            Html::a('<li class="' . (($active == "prayer" && $gid == $group->id) ? "active-page" : NULL) . '"><i class="fas fa-praying-hands"></i> &nbsp;&nbsp;Prayer List</li>', [
                                '/group/prayer', 'id' => $group->id]) : NULL 
                        ?>
                        <?= $group->feature_calendar ? 
                            Html::a('<li class="' . (($active == "calendar" && $gid == $group->id) ? "active-page" : NULL) . '">' . Html::icon('calendar') . ' &nbsp;&nbsp;Calendar</li>', [
                                '/group/calendar', 'id' => $group->id]) : NULL 
                        ?>
                        <?= $group->feature_forum ? 
                            Html::a('<li class="' . (($active == "forum" && $gid == $group->id) ? "active-page" : NULL) . '"><i class="fa fa-comments"></i> &nbsp;&nbsp;Forum</li>', [
                                '/group/forum', 'id' => $group->id]) : NULL 
                        ?>
                        <?= $group->feature_update ? 
                            Html::a('<li class="' . (($active == "update" && $gid == $group->id) ? "active-page" : NULL) . '">' . Html::icon('send') . ' &nbsp;&nbsp;Updates</li>', [
                                '/group/update', 'id' => $group->id]) : NULL 
                        ?>
                        <?= $group->feature_notification ? 
                            Html::a('<li class="' . (($active == "notification" && $gid == $group->id) ? "active-page" : NULL) . '"><i class="far fa-envelope"></i> &nbsp;&nbsp;Notification</li>', [
                                '/group/notification', 'id' => $group->id]) : NULL 
                        ?>
                    </ul>
                    <?php $this->registerJs(
                        "$('#group-" . $group->id . "').click(function(e) {
                            $('#list-" . $group->id . "').toggle('1000');
                            $('#list-" . $group->id . "').siblings('ul').hide('1000');
                        });", \yii\web\View::POS_READY
                    ); ?>
                <?php } ?>
            </div>
        <?php } ?>        
    </div>