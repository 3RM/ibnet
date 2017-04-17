<?php

use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Activation';
?>

<div class="profile-form">

    <h1><?= Html::icon('thumbs-up') ?> Congratulations!</h1>

    <p>&nbsp;</p>
    <p>Your profile "<?= $profile->profile_name ?>" is now active and will be searchable in the directory shortly.</p>

    <p>
        Return to your account at any time to update your profile. Come back within one year and update or confirm 
        the accuracy of your information to keep your profile active.
    </p>

    <p>Profile Url: <?= Html::a(Url::base('http') . Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 'city' => $profile->url_city, 'name' => $profile->url_name, 'id' => $profile->id]) . ' ' . Html::icon('new-window'), ['/profile/' . ProfileController::$profilePageArray[$profile->type],   'city' => $profile->url_city, 'name' => $profile->url_name, 'id' => $profile->id], ['target' => '_blank']) ?></p>

    <br />
    <br />

   <?= HTML::a('My Profiles', ['my-profiles'], ['class' => 'btn btn-primary']) ?>
   <p>&nbsp;</p>

</div>