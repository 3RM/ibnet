<?php

use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use frontend\controllers\ProfileFormController;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
Url::Remember();
?>

<?= $this->render('../site/_userAreaHeader', ['active' => 'profiles']) ?>
<div class="container">
    <?= $this->render('../site/_userAreaLeftNav', ['active' => 'profiles', 'joinedGroups' => $joinedGroups]) ?>

    <div class="right-content">
        <h2>My Profiles</h2>
    
        <?= Html::a(Html::icon('plus') . ' New Profile', ['profile-mgmt/terms'], ['class' => 'btn btn-primary']) ?>

        <?php if ($profiles) { ?>
            <?php foreach($profiles as $profile) { ?>
                <div class="profile-card">
                    <div class="profile-card-header">
                        <div class="profile-title">
                            <?= Profile::$icon[$profile->type] . '"' . $profile->profile_name . '"' ?>
                        </div>
                        <div class="profile-status">
                            <?php if ($profile->status == Profile::STATUS_NEW) {
                                echo '<p><span style="color:#337ab7"> New</p>';
                            } elseif ($profile->status == Profile::STATUS_ACTIVE) {
                                echo '<p><span style="color:green"> Active</span></p>';
                            } elseif ($profile->status == Profile::STATUS_INACTIVE) {
                                echo '<p><span style="color:orange"> Inactive</p>';
                            } elseif ($profile->status == Profile::STATUS_EXPIRED) {
                                echo '<p><span style="color:red"> Expired</p>';
                            } ?> 
                        </div>
                    </div>
                    <div class="profile-card-body">
                        <div class="profile-info">
                            <div class="picture-name">
                                <?= empty($profile->image2) ? Html::img('@img.profile/profile-logo.png', ['class' => '', 'alt' => 'Logo Image']) : Html::img($profile->image2, ['class' => '', 'alt' => 'Logo image']) ?>
                                <?= $profile->status == Profile::STATUS_ACTIVE ?
                                    '<h1>' . Html::a($profile->formatName . '&nbsp' . Html::icon('new-window', ['class' => 'internal-link']), ['profile/' . ProfileController::$profilePageArray[$profile->type], 'id' => $profile->id, 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name], ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '</h1>' :
                                    '<h1>' . $profile->formatName . '</h1>';
                                ?>
                            </div>
                            <?php if ((time() > (strtotime($profile->renewal_date) - 1209600)) && (time() < strtotime($profile->renewal_date)) && ($profile->status == Profile::STATUS_ACTIVE)) {  // Profile will expire within two weeks
                                echo '<div class="notification"><p>This profile is set to expire soon. ' . Html::a('Click here', ['preview/view-preview', 'id' => $profile->id]) . ' and hit the "Finished" button to keep it active.</p></div>';
                            } elseif (time() > strtotime($profile->renewal_date) && ($profile->status == Profile::STATUS_ACTIVE)) {               // Profile is in grace period
                                echo '<div class="notification"><p>This profile is in the expiration grace period. ' . Html::a('Click here', ['preview/view-preview', 'id' => $profile->id]) . ' and hit the "Finished" button to keep it active.</p></div>';
                            } elseif ($profile->status == Profile::STATUS_EXPIRED) {
                                echo '<div class="notification"><p>Your profile is expired due to inactivity.  To keep it active, simply review and update as necessary at least once per year. ' .  Html::a('Click here', ['profile-mgmt/continue-activate', 'id' => $profile->id]) . ' to reactive it.</p></div>';
                            } elseif ($profile->unconfirmed) {
                                echo '<div class="notification"><p>You have unconfirmed staff. ' . Html::a('Click here', ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['sf']-1, 'id' => $profile->id]) . ' to review.</p></div>';
                            } elseif (!$profile->events) {
                                echo '<div class="notification"><p>Add ministry highlights to your timeline ' . Html::a('here', ['profile-mgmt/history', 'id' => $profile->id]) . '.</p></div>';
                            } ?>
                        </div>
                        <div class="profile-links">
                            <?php ActiveForm::begin(); ?>
                            <?= $profile->created_at == NULL ?
                                '<p class="date"><b>Created</b>: --</p>' :
                                '<p class="date"><b>Created</b>: ' . Yii::$app->formatter->asDate($profile->created_at) . '</p>';
                            ?>
                            <?php if (($profile->renewal_date == NULL) || ($profile->status == Profile::STATUS_INACTIVE)) {
                                echo '<p class="date last"><b>Update by</b>: --</p>';
                            } else {
                                echo '<p class="date last"><b>Update by</b>: ';
                                if (time() > strtotime($profile->renewal_date)) {                                        // Profile is in grace period
                                    echo '<span style="color:red">' . Yii::$app->formatter->asDate($profile->renewal_date) . '</span></p>';
                                } elseif (time() > (strtotime($profile->renewal_date) - 1209600)) {                      // Profile will expire in two weeks
                                     echo '<span style="color:orange">' . Yii::$app->formatter->asDate($profile->renewal_date) . '</span></p>';
                                } else {                                                                                 // Profile is active
                                    echo Yii::$app->formatter->asDate($profile->renewal_date) . '</p>';
                                }
                            } ?>
                            <?= (($profile->status == Profile::STATUS_NEW) || ($profile->status == Profile::STATUS_INACTIVE) || ($profile->status == Profile::STATUS_EXPIRED)) ?
                                '<p>' . Html::a(Html::icon('ok-circle') . ' Activate', ['profile-mgmt/continue-activate', 'id' => $profile->id]) . '</p>': 
                                '<p>' . Html::a(Html::icon('edit') . ' Edit', ['preview/view-preview', 'id' => $profile->id]) . '</p>';
                            ?>
                            <?= ($profile->status == Profile::STATUS_ACTIVE) ?
                                '<p>' . Html::a(Html::icon('ban-circle') . ' Disable', ['profile-mgmt/disable', 'id' => $profile->id]) . '</p>': 
                                HTML::submitButton(Html::icon('trash') . ' Trash', [
                                    'method' => 'post',
                                    'onclick' => 'return confirm("Are you sure you want to permanently delete this profile?")',
                                    'class' => 'link-btn',
                                    'name' => 'trash',
                                    'value' => $profile->id
                                ]);
                            ?>
                            <?= $profile->category == Profile::CATEGORY_IND ?
                                NULL : 
                                '<p>' . Html::a(Html::icon('transfer') . ' Transfer', ['profile-mgmt/transfer', 'id' => $profile->id]) . '</p>';
                            ?>
                            <?= '<p>' . Html::a('<span class="glyphicons glyphicons-history"></span> Ministry Timeline', ['profile-mgmt/history', 'id' => $profile->id]) . '</p>' ?>
                        
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else {
            echo '<h4 class="top-margin-60"><em>You don\'t have any profiles yet...</em></h4>';
        } ?>
    </div>
</div>