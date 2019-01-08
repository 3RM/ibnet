<?php
namespace common\models;

use common\models\User;
use common\models\profile\Profile;
use yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;


class Mail extends Model
{
    /**
     * User: Sends user an email with a link to verify user email address.
     * 
     * @return boolean
     */
    public function sendVerificationEmail($username)
    {   

        $user = User::findByUsername($username);
        $user->generateNewEmailToken();
        $link = Yii::$app->urlManager->createAbsoluteUrl([
            'site/registration-complete', 
            'token' => $user->new_email_token]);
        Yii::$app->mailer
            ->compose(
                ['html' => 'notification-html'],
                [
                    'title' => 'Complete your registration with IBNet.org', 
                    'message' => 'Follow this link to complete your registration: ' . $link,
                ])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($user->new_email)
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        return true;
    }

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

    /**
     * Admin: send admin notice of new user registration
     * 
     * @return boolean
     */
    public function sendAdminNewUser($username)
    {   
        $user = User::findByUsername($username);
        $title = 'New User Registration';
        $msg = 'A new user has registered at IBNet: ' . $user->first_name . ' ' . $user->last_name;

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['adminEmail']])
            ->setTo([\yii::$app->params['adminEmail']])
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        return true;
    }

    /**
     * User: send weekly notification of new blog postings
     * 
     * @return boolean
     */
    public function sendWeeklyBlog($user, $posts)
    {   
        $title = 'This Week\'s Blog Posts';
        $msg = 'Read the latest articles from the IBNet Blog: ';
        $emailPrefLink = Html::a('click here', \Yii::$app->params['frontendUrl'] . '/site/login?url=' . \Yii::$app->params['frontendUrl'] . '/site/settings/#account-settings', ['target' => '_blank']);

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'weekly-blog-html'], 
                [
                    'title' => $title, 
                    'message' => $msg, 
                    'posts' => $posts, 
                    'emailPrefLink' => $emailPrefLink
                ]
            )
            ->setFrom([\Yii::$app->params['blogDigestEmail']])
            ->setTo($user->email)
            ->setSubject('IBNet Blog')
            ->send();

        return true;
    }

    /**
     * Admin: send admin notice of newly activated profile
     * 
     * @return boolean
     */
    public function sendAdminActiveProfile($id)
    {   
        $profile = Profile::findOne($id);
        $title = 'Newly Activated Profile';
        $name = $profile->category == Profile::CATEGORY_IND ?
            $profile->ind_first_name . ' ' . $profile->ind_last_name :
            $profile->org_name;
        $msg = 'A profile was just activated: ' . $name;

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['adminEmail']])
            ->setTo([\yii::$app->params['adminEmail']])
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        return true;
    }

}
