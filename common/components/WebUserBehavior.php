<?php
namespace common\components;

use GuzzleHttp\Client;
use Yii;
use yii\db\Expression; use common\models\Utility;
use yii\web\User;

class WebUserBehavior extends \yii\base\Behavior
{
    public function events()
    {
        return [
            User::EVENT_AFTER_LOGIN => 'afterLogin',
        ];
    }
    public function afterLogin($event)
    {
        /** @var $user User */
        $user = $event->sender;
        $ip = Yii::$app->request->userIP;
        $user->identity->updateAttributes([
            'last_login' => new Expression('NOW()'),
            // TODO this should probably go into DB session storage to list all active sessions
            'ip' => $ip,
            // 'login_attempts' => 0,
        ]);
    }
}