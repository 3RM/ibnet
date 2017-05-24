<?php
namespace console\models;

use yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;


class Mail extends Model
{

    /**
     * User: send profile expiration two week notice
     * 
     * @return boolean
     */
    public function sendTwoWeeksNotice($user, $profile)
    {   
        $params = '?url=' . Yii::$app->params['frontendUrl'] . '/preview/view-preview?id=' . $profile->id;
        $link = Html::a('profile edit page', Yii::$app->params['frontendUrl'] . '/site/login' . $params);

        $title = 'Your IBNet Profile Expires Soon';
        $msg = 'Your IBNet profile "' . $profile->profile_name . '" is set to expire in two weeks.  Visit your ' . 
            $link . ' and make any necessary updates.  When you are finished, press the "Finsihed" button 
            to reset your expiration date and keep your profile active in the directory.';

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['adminEmail']])
            ->setTo([$user->email])
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        return true;
    }

    /**
     * User: send profile grace period notice
     * 
     * @return boolean
     */
    public function sendGraceNotice($user, $profile)
    {   
        $params = '?url=' . Yii::$app->params['frontendUrl'] . '/preview/view-preview?id=' . $profile->id;
        $link = Html::a('profile edit page', Yii::$app->params['frontendUrl'] . '/site/login' . $params);

        $title = 'Your IBNet Profile is About to Expire.';
        $msg = 'Your IBNet profile "' . $profile->profile_name . '" has expired, but we have added a one week grace period
            before final expiration.  Please visit your ' . $link . ' right away and make any necessary updates.  When you are 
            finished, press the "Finsihed" button to reset your expiration date and keep your profile active in the 
            directory.';

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['adminEmail']])
            ->setTo([$user->email])
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        return true;
    }

    /**
     * User: send profile expiration notice
     * 
     * @return boolean
     */
    public function sendExpiredNotice($user, $profile)
    {   
        $params = '?url=' . Yii::$app->params['frontendUrl'] . '/profile-mgmt/my-profiles';
        $link = Html::a('profiles page', Yii::$app->params['frontendUrl'] . '/site/login' . $params);

        $title = 'Your IBNet Profile Has Expired.';
        $msg = 'Your IBNet profile "' . $profile->profile_name . '" has expired and is no longer visible in the public directory. 
            But you can reactivate it at any time.  Simply visit your ' . $link . ', click the activate link, and follow the 
            instructions to reactivae your profile.';

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['adminEmail']])
            ->setTo([$user->email])
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send();

        return true;
    }

}
