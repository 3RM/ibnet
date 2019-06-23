<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Join Group';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= '<i class="fas fa-user-plus"></i> ' . $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container">

    <div class="row">
        <div class = "col-md-8">
            <div class="join-picture-name">
                <?= empty($group->image) ? Html::img('@img.profile/profile-logo.png', ['class' => '']) : Html::img($group->image, ['class' => '']) ?>
                <h1><?= $group->name ?></h1>
            </div>
            <p class="group-description"><?= $group->description ?></p>
        </div>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($status == 'guest') { ?>
        <p class="top-margin"><i class="fas fa-info-circle"></i> You must be logged in to accept this invitation.</p>
        <div class="row top-margin">
            <div class = "col-md-6">
                <?= Html::submitButton('Decline', [
                    'method' => 'post',
                    'class' => 'btn btn-primary invite-join',
                    'name' => 'decline',
                    'value' => $invite->id
                ]) ?>
                <?= Html::a('Login', '/site/login', ['class' => 'btn-link invite-join']) ?>
                <?= Html::a('Register', '/site/register', ['class' => 'btn-link invite-join']) ?>
            </div>
        </div>

    <?php } elseif ($status == 'member') { ?>
        <p class="top-margin"><i class="fas fa-info-circle"></i> You are already a member of this group.</p>
        <p class="top-margin"><?= 'Go to ' . Html::a('My Groups', '/group/my-groups') ?></p>

    <?php } elseif ($status == 'expired') { ?>
        <p class="top-margin"><i class="fas fa-info-circle"></i> This invitation has expired. Your request to join will require preapproval.</p>
        <div class="row top-margin">
            <div class = "col-md-4">
                <?= Html::submitButton('Join', [
                    'method' => 'post',
                    'class' => 'btn btn-primary invite-join',
                    'name' => 'join',
                    'value' => $invite->id
                ]) ?>
            </div>
        </div>

    <?php } elseif ($status == 'not authorized') { ?>
        <p class="top-margin"><i class="fas fa-info-circle"></i> This feature is locked.  You can unlock it by identifying your home church from the directory on your account settings page.</p>
        <p class="top-margin"><?= 'Go to ' . Html::a('My Settings', '/site/settings') ?></p>

    <?php } else { ?>
        <div class="row top-margin">
            <div class = "col-md-4">
                <?= Html::submitButton('Decline', [
                    'method' => 'post',
                    'class' => 'btn btn-primary invite-join',
                    'name' => 'decline',
                    'value' => $invite->id
                ]) ?>
                <?= Html::submitButton('Join', [
                    'method' => 'post',
                    'class' => 'btn btn-primary invite-join',
                    'name' => 'join',
                    'value' => $invite->id
                ]) ?>
            </div>
        </div>

    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>