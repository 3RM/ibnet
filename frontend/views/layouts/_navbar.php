<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\ActiveForm;
?>
    <?php
    NavBar::begin([
        'brandLabel' =>  '<span class="abbreviated">IBNet</span><span class="fullname">IBNet | for independent Baptists everywhere</span>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'Browse', 'url' => ['/profile/browse']],
        ['label' => 'Blog', 'url' => 'https://blog.ibnet.org'],
    ];
    
    if (Yii::$app->user->isGuest) {
        $menuItems[] = 
            '<li class="dropdown" id="menuLogin">'.
                Html::a(Html::img('@images/content/user.png'), '#', ['id' => 'navLogin', 'class' => 'dropdown-toggle navbar-guest', 'data-toggle' => 'dropdown']) .
                Html::beginTag('div', ['class' => 'dropdown-menu']) .
                    Html::beginForm('/ajax/nav-login', 'post', ['id' => 'navbar-login-form', 'data-on-done' => "loginDone"]) .
                        Html::textInput('username', null, ['id' => 'username', 'placeholder' => 'Username', 'autocomplete' => 'current-username']) .
                        Html::passwordInput('password', NULL, ['id' => 'password', 'placeholder' => 'Password', 'autocomplete' => 'current-password']) .
                        Html::submitButton('Sign In', ['class' => 'btn btn-primary']) .
                    Html::endForm() .
                    Html::tag('div', '', ['id' => 'login-result']) .
                    Html::a('I forgot my password', 'site/request-password-reset', ['style' => 'font-size:0.8em;']) .'
                    <hr>'.
                    Html::a('Register', 'site/register', ['class' => 'btn btn-primary register-link']) .
                Html::endTag('div') .' 
            </li>';
    } else {
        $menuItems[] =
            '<li class="dropdown" id="menuLogin">'.
                (Yii::$app->user->identity->usr_image ?
                Html::a(Html::img(Yii::$app->user->identity->usr_image), '#', ['id' => 'navLogin', 'class' => 'dropdown-toggle navbar-user', 'data-toggle' => 'dropdown']) : 
                Html::a(Html::img('@images/content/user.png'), '#', ['id' => 'navLogin', 'class' => 'dropdown-toggle navbar-user', 'data-toggle' => 'dropdown'])) .
                Html::beginTag('div', ['id' => 'dropdown-menu-user', 'class' => 'dropdown-menu']) .'
                    <h4>' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '</h4>' .
                    Html::a('My Account', '/site/settings', ['class' => 'btn btn-primary']) .
                    Html::a('My Profiles', '/profile-mgmt/my-profiles', ['class' => 'btn btn-primary']) .'
                    <hr> '.
                    Html::beginForm('/site/logout', 'post') .
                        Html::submitButton('Sign Out', ['class' => 'btn btn-primary btn-small']) .
                    Html::endForm() .'
                </div>
            </li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
    <?php $this->registerJs("$('#navbar-login-form').submit(handleAjaxForm);", \yii\web\View::POS_READY); ?>