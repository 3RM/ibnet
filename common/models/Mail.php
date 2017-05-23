<?php
namespace common\models;

use yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;


class Mail extends \yii\db\ActiveRecord
{

    /**
     * User: send new email link
     * 
     * @return boolean
     */
    public function sendEmailConfLink($email, $new_email, $new_email_token)
    {   
        $link =  Yii::$app->urlManager->createAbsoluteUrl(['site/email-confirmed', 'token' => $new_email_token]);

        $title = 'Confirm Your Email Address';
        $msg = 'Follow this link to confirm your new email address: ' . $link;

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['adminEmail']])
            ->setTo([$new_email])
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        $title = 'Your Account Has Changed';
        $msg = 'We recieved a request to update your account email.  If you did not
            request this change, please contact us at <a href="mailto:admin@ibnet.org">admin@ibnet.org</a>.';

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['adminEmail']])
            ->setTo([$email])
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        return true;
    }

    /**
     * User: send password change notification
     * 
     * @return boolean
     */
    public function sendNewPwd($email)
    {   

        $title = 'Your Account Has Changed';
        $msg = 'Your password has been changed.  If you did not change your password,   
            or if you feel that this message is in error, please contact us at     
            <a href="mailto:admin@ibnet.org">admin@ibnet.org</a>.';

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['adminEmail']])
            ->setTo([$email])
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        return true;
    }

}