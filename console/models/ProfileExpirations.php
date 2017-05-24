<?php

namespace console\models;

use common\models\profile\Profile;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ProfileExpirations extends Model
{

    /**
     * Return profiles that are set to expire in two weeks
     * @param string $id
     * @return Profile the loaded model
     */
    public function getTwoWeeksProfiles()
    {
        return Profile::find()
            ->select('*')
            ->where(['status' => PROFILE::STATUS_ACTIVE])
            ->andwhere('renewal_date=DATE_ADD(CURDATE(), INTERVAL 14 DAY)')
            ->all();
    }

    /**
     * Return profiles that are on first day of one week grace period
     * @param string $id
     * @return Profile the loaded model
     */
    public function getGraceProfiles()
    {
        return Profile::find()
            ->select('*')
            ->where(['status' => PROFILE::STATUS_ACTIVE])
            ->andwhere('renewal_date=DATE_SUB(CURDATE(), INTERVAL 1 DAY)')
            ->all();
    }

    /**
     * Return profiles that passed the grace period yesterday
     * @param string $id
     * @return Profile the loaded model
     */
    public function getExpiredProfiles()
    { 
        return Profile::find()
            ->select('*')
            ->where(['status' => PROFILE::STATUS_ACTIVE])
            ->andwhere('renewal_date=DATE_SUB(CURDATE(), INTERVAL 8 DAY)')
            ->all();
    }
}
