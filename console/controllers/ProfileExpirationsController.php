<?php
namespace console\controllers;

use common\models\User;
use common\models\profile\Profile;
use console\models\ProfileExpirations;
use console\models\Mail;
use fedemotta\cronjob\models\CronJob;
use yii\console\Controller;


/**
 * Profile expirations controller
 */
class ProfileExpirationsController extends Controller
{
    
    /**
     * Run ProfileExpirations::RunCheck for a period of time
     * Cron job can be found at ~/crontab -e
     * @param string $from
     * @param string $to
     * @return int exit code
     */
    public function actionInit($from, $to){
        $dates  = CronJob::getDateRange($from, $to);
        $command = CronJob::run($this->id, $this->action->id, 0, CronJob::countDateRange($dates));
        if ($command === false){
            return Controller::EXIT_CODE_ERROR;
        } else {
            foreach ($dates as $date) {
        
                $twoWeeksProfiles = ProfileExpirations::getTwoWeeksProfiles();                      // Profiles that expire in two weeks
                $graceProfiles = ProfileExpirations::getGraceProfiles();                            // Profiles that entered the one week grace period yesterday                           
                $expiredProfiles = ProfileExpirations::getExpiredProfiles();                        // Profiles that passed the grace period yesterday

                foreach ($twoWeeksProfiles as $profile) {
                    $user = User::findOne($profile->user_id);
                    Mail::sendTwoWeeksNotice($user, $profile);
                    echo $profile->id;
                }
                foreach ($graceProfiles as $profile) {
                    $user = User::findOne($profile->user_id);
                    Mail::sendGraceNotice($user, $profile);
                    echo $profile->id;
                }
                foreach ($expiredProfiles as $profile) {
                    $user = User::findOne($profile->user_id);
                    $profile->inactivate();                                                         // Set profile inactive
                    $profile->updateAttributes(['profile_status' => Profile::STATUS_EXPIRED]);
                    Mail::sendExpiredNotice($user, $profile);
                }
                
            }
            $command->finish();
            return Controller::EXIT_CODE_NORMAL;
        }
    }

    /**
     * Run SomeModel::some_method for today only as the default action
     * @return int exit code
     */
    public function actionIndex(){                                                                  // php yii profile-expirations/index
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));
    }

    /**
     * Run SomeModel::some_method for yesterday
     * @return int exit code
     */
    public function actionYesterday(){
        return $this->actionInit(date("Y-m-d", strtotime("-1 days")), date("Y-m-d", strtotime("-1 days")));
    }
}
