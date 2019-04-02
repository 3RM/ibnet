<?php
use yii\helpers\Html; 
?>
    <div class="left-nav">
        <ul>
            <?= Html::a('<li class="settings ' . ($active == "settings" ? "active" : NULL) . '"><span class="glyphicons glyphicons-settings"></span><span class="left-nav-linktext">&nbsp;Settings</span></li>', '/site/settings') ?>
            <?= Html::a('<li class="profiles ' . ($active == "profiles" ? "active" : NULL) . '"><span class="glyphicons glyphicons-vcard"></span><span class="left-nav-linktext">&nbsp;Profiles</span></li>', '/profile-mgmt/my-profiles') ?>
            <?= Html::a('<li class="networks ' . ($active == "networks" ? "active" : NULL) . '"><span class="glyphicons glyphicons-cluster"></span><span class="left-nav-linktext">&nbsp;Networks</span></li>', '/network/my-networks') ?>
            <?= Yii::$app->user->identity->isMissionary ? Html::a('<li class="updates ' . ($active == "updates" ? "active" : NULL) . '"><span class="glyphicons glyphicons-direction"></span><span class="left-nav-linktext">&nbsp;Updates</span></li>', '/missionary/update-repository') : NULL ?>
        </ul>
    </div>