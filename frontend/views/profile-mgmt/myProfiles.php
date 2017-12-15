<?php

use common\models\Utility;
use common\models\profile\Profile;
use common\widgets\Alert;
use frontend\controllers\ProfileFormController;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'My Account';
?>
<div class="wrap my-profiles">
    <div class="container">
        <div class="row">
        <h1><?= $this->title ?></h1>

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'Dashboard',
                    'url' => ['/site/dashboard'],
                ],
                [
                    'label' => 'Profiles',  
                    'active' => true,
                ],
            ],
        ]); ?>
        </div>
    </div>
</div>
<div class="clearprofiles"></div>
<?= Alert::widget() ?>

<div class="profile-owner-index">

    <div class="row">
        <div class="col-md-10">
            <div class="top-margin">
                <p><?= Html::a(Html::icon('plus') . ' New Profile', ['profile-mgmt/terms'], ['class' => 'btn btn-primary']) ?></p>
            </div>
        </div>
    </div>
    </br>

    <div class="row">
        <div class="col-md-10">
            <div class="panel panel-default top-margin">
                <div class="panel-heading">My Ministry Profiles (<?= isset($profileArray) ? count($profileArray) : '0' ?>)</div>

                <table class="table table-profile">
                    <?php foreach($profileArray as $profile) {
                        echo '<tr>
                            <td class="middle center"><div class="cell-padding"><span class="icon-lg">' . Profile::$icon[$profile->type] . '</span><br>' . $profile->type . '</div></td>
                            <td class="middle center"><div class="cell-padding lead">"' . $profile->profile_name . '"</div></td>';
                    ?>            
                    <?=    '<td class="middle">' ?>
                                <div class="cell-padding">
                                <?= $profile->created_at == NULL ?
                                    '<p>Created: --</p>' :
                                    '<p>Created: ' . Yii::$app->formatter->asDate($profile->created_at) . '</p>';
                                ?>
                                <?php if ($profile->renewal_date == NULL) {
                                    echo '<p>Update by: --</p>';
                                } else {
                                    echo '<p>Update by: ';
                                    if (time() > strtotime($profile->renewal_date)) {                                        // Profile is in grace period
                                        echo '<span style="color:red">' . Yii::$app->formatter->asDate($profile->renewal_date) . '</span></p>';
                                    } elseif (time() > (strtotime($profile->renewal_date) - 1209600)) {                      // Profile will expire in two weeks
                                         echo '<span style="color:orange">' . Yii::$app->formatter->asDate($profile->renewal_date) . '</span></p>';
                                    } else {                                                                                 // Profile is active
                                        echo Yii::$app->formatter->asDate($profile->renewal_date) . '</p>';
                                    }
                                } ?>
                                <?php if ($profile->status == 0) {
                                    echo '<p>Status:<span style="color:#337ab7"> New</p>';
                                } elseif ($profile->status == 10) {
                                    echo '<p>Status:<span style="color:green"> Active</span></p>';
                                } elseif ($profile->status == 20) {
                                    echo '<p>Status:<span style="color:orange"> Inactive</p>';
                                } ?> 
                                </div>
                    <?=     '</td>' ?>
                    <?=     '<td class="middle">' ?>
                                <div class="cell-padding">
                                <?= (($profile->status == Profile::STATUS_NEW) || ($profile->status == Profile::STATUS_INACTIVE)) ?
                                    '<p>' . Html::a(Html::icon('ok-circle') . ' Activate', ['profile-mgmt/continue-activate', 'id' => $profile->id]) . '</p>' : 
                                    '<p>' . Html::a(Html::icon('edit') . ' Edit', ['preview/view-preview', 'id' => $profile->id]) . '</p>';
                                ?>
                                <?= ($profile->status == Profile::STATUS_ACTIVE) ?
                                    '<p>' . Html::a(Html::icon('ban-circle') . ' Disable', ['profile-mgmt/disable', 'id' => $profile->id]) . '</p>' : 
                                    '<p>' . Html::a(Html::icon('trash') . ' Trash', ['profile-mgmt/trash', 'id' => $profile->id]) . '</p>';
                                ?>
                                <?= $profile->category == Profile::CATEGORY_IND ?
                                    NULL : 
                                    '<p>' . Html::a(Html::icon('transfer') . ' Transfer', ['profile-mgmt/transfer', 'id' => $profile->id]) . '</p>';
                                ?>
                                <?= '<p>' . Html::a(Html::icon('cog') . ' Settings', ['profile-mgmt/settings', 'id' => $profile->id]) . '</p>' ?>
                                <?= $profile->unconfirmed ? 
                                    '<p>' . Html::a('<span style="color:#d92335">' . Html::icon('flag') . '</span> Unconfirmed Staff', ['profile-form/form-route', 'type' => $profile->type, 'fmNum' => ProfileFormController::$form['sf']-1, 'id' => $profile->id]) . '</p>' : NULL; ?>
                                </div>
                    <?=     '</td>
                        </tr>';
                    
                    } ?>
                </table>
            </div>
        </div>
    </div>
    <p>&nbsp;</p>
  
</div>