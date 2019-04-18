<?php

use common\models\User;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
?>
    <p>ID: <?= $user->id ?></p>

    <p>Current Role: <?= array_keys(Yii::$app->authManager->getRolesByUser($user->id))[0] ?></p>

    <table class="role">
        <tr>
            <td>User</td>
            <td>Default role. User is allowed to create profiles, but cannot access any additional featues.</td>
        </tr>
        <tr>
            <td>SafeUser</td>
            <td>
                SafeUser is a user who has identified their home church in the directory.  In addition to creating profiles, 
                SafeUsers have access to all features that pertain to their self-identified primary role (e.g. missionary features).
            </td>
        </tr>
        <tr>
            <td>Admin</td>
            <td>Admins have full frontend and backend access to user accounts, profiles, etc.</td>
        </tr>
    </table>

    <?php $form = ActiveForm::begin(['action' => '/accounts/update']); ?>

    <?= $form->field($user, 'select')->dropDownList(
        [
            User::ROLE_USER => User::ROLE_USER, 
            User::ROLE_SAFEUSER => User::ROLE_SAFEUSER, 
            User::ROLE_ADMIN => User::ROLE_ADMIN
        ], ['class' => 'select'])->label(false) ?> 

    <?= Html::submitButton('Change Role', [
        'name' => 'role',
        'value' => $user->id,
        'method' => 'post',
        'class' => 'btn-main',
        'onclick' => 'return confirm("Are you sure you want to change this user\'s role? Click to confirm.")'
    ]); ?>   

    <?php $form = ActiveForm::end(); ?>
