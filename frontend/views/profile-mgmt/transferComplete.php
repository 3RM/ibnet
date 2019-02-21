<?php

use common\widgets\Alert;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Transfer Complete';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= Html::icon('transfer') . ' ' . $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container">

    <h1><?= Html::icon('thumbs-up') ?> Congratulations!</h1>

    <p>&nbsp;</p>
    <p>You now own the IBNet profile "<?= $profile->profile_name ?>".</p>

    <p>Be sure to update the profile at least once per year in order to keep it active.</p>

    <p>Profile Url: <?= Html::a(Url::base('http') . Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name, 'id' => $profile->id]), ['/profile/' . ProfileController::$profilePageArray[$profile->type],   'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name, 'id' => $profile->id]) ?></p>

</div>