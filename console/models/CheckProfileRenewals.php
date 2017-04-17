<?php

namespace console\models;

use common\models\profile\Profile;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class CheckProfileExpirations extends Model
{
   
   /**
     * Finds all profiles that expire in one week and sends notification to each.
     *
     * @param string $email the target email address
     * @return boolean whether the email was sent
     */
    public function checkRenewals($date)
    {
        $renewal = date("Y-m-d", strtotime("+8 days"));
        $profiles = Profile::find()
            ->select('profile.*')
            ->leftJoin('user', '`user`.`id` = `profile`.`user_id`')
            ->where(['profile.renewal_date' => $renewal])
            ->andWhere(['profile.status' => Profile::STATUS_ACTIVE])
            ->with('user')
            ->all();

        foreach ($profiles as $profile) {
            $userFirstName = $profile['user']['first_name'];
            $userEmail = $profile['user']['email'];
            $profileId = $profile['id'];
            $this->sendNotification($userFirstName, $userEmail, $profileId);
        }

        return $this;
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return boolean whether the email was sent
     */
    public function sendNotification($userFirstName, $userEmail, $profileId)
    {
        $profile = Profile::findModel($profileId);
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'profileRenewal-html', 'text' => 'profileRenewal-text'],
                ['profile' => $profile, 'name' => $userFirstName]
            )
            ->setFrom(Yii::$app->params['no-replyEmail'])
            ->setTo($userEmail)
            ->setSubject("It's time to renew your profile")
            ->send();
    }
}
