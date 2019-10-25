<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\ActiveForm;
?>
    <?php
    NavBar::begin([
        'brandLabel' =>  '<span class="abbreviated">IBNet</span><span class="fullname">IBNet | for independent Baptists everywhere<br>',
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
                Html::a(Html::img('@img.site/user.png'), '#', ['id' => 'navLogin', 'class' => 'dropdown-toggle navbar-guest', 'data-toggle' => 'dropdown']) .
                '<div class="dropdown-menu">' .
                    Html::beginForm('/ajax/nav-login', 'post', ['id' => 'navbar-login-form', 'data-on-done' => "loginDone"]) .
                        Html::textInput('username', null, ['id' => 'username', 'placeholder' => 'Username', 'autocomplete' => 'current-username']) .
                        Html::passwordInput('password', NULL, ['id' => 'password', 'placeholder' => 'Password', 'autocomplete' => 'current-password']) .
                        Html::submitButton('Sign In', ['class' => 'btn btn-primary']) .
                    Html::endForm() .
                    '<div id="login-result"></div>' .
                    Html::a('I forgot my password', '/site/request-password-reset', ['style' => 'font-size:0.8em;']) .'
                    <hr>'.
                    Html::a('Register', 'site/register', ['class' => 'btn btn-primary register-link']) .
                '</div> 
            </li>';
            $this->registerJs("$('#navbar-login-form').submit(handleLoginForm);", \yii\web\View::POS_READY);
    } else {
        $menuItems[] =
            '<li class="dropdown" id="menuLogin">'.
                (Yii::$app->user->identity->usr_image ?
                    Html::a(Html::img(realpath(ltrim(Yii::$app->user->identity->usr_image, '/')) ? Yii::$app->user->identity->usr_image : '@img.site/user.png'), '#', ['id' => 'navLogin', 'class' => 'dropdown-toggle navbar-user', 'data-toggle' => 'dropdown']) : 
                    Html::a(Html::img('@img.site/user.png'), '#', ['id' => 'navLogin', 'class' => 'dropdown-toggle navbar-user', 'data-toggle' => 'dropdown'])) .
               '<div id="dropdown-menu-user" class="dropdown-menu">
                    <h4>' . Yii::$app->user->identity->fullName . '</h4>' .
                    Html::a('Account', '/site/settings', ['class' => 'btn btn-primary']) . '<br>' .
                    Html::a('Profiles', '/profile-mgmt/my-profiles', ['class' => 'btn btn-primary']) . '<br>' .
                    (Yii::$app->user->identity->isMissionary ? Html::a('Updates', '/missionary/update-repository', ['class' => 'btn btn-primary']) . '<br>' : NULL) . 
                    (Yii::$app->user->identity->isSafeUser ? Html::a('Groups', '/group/my-groups', ['class' => 'btn btn-primary']) . '<br>' : NULL) . '
                    <hr> '.
                    Html::beginForm('/site/logout', 'post') .
                        Html::submitButton('Sign Out', ['class' => 'btn btn-primary']) .
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