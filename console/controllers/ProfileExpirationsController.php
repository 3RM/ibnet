<?php
namespace console\controllers;

use common\models\User;
use common\models\profile\Profile;
use console\models\ProfileExpirations;
use console\models\Mail;
use fedemotta\cronjob\models\CronJob;
use yii\console\Controller;

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
        
                $twoWeeksProfiles = ProfileExpirations::getTwoWeeksProfiles();
                $graceProfiles = ProfileExpirations::getGraceProfiles();
                $expiredProfiles = ProfileExpirations::getExpiredProfiles();

                // Send two weeks notice
                foreach ($twoWeeksProfiles as $profile) {
                    $user = $profile->user;
                    $params = '?url=' . Yii::$app->params['frontendUrl'] . '/preview/view-preview?id=' . $profile->id;
                    $link = Html::a('profile edit page', Yii::$app->params['frontendUrl'] . '/site/login' . $params);
                    $msg = 'Your IBNet profile "' . $profile->profile_name . '" is set to expire in two weeks.  Visit your ' . 
                        $link . ' and make any necessary updates.  When you are finished, press the "Finsihed" button 
                        to reset your expiration date and keep your profile active in the directory.';
                    Yii::$app
                        ->mailer
                        ->compose(
                            ['html' => 'notification-html', 'text' => 'notification-text'], 
                            [
                                'title' => 'Your IBNet Profile Expires Soon', 
                                'message' => $msg,
                            ])
                        ->setFrom([Yii::$app->params['email.admin']])
                        ->setTo([$user->email])
                        ->setSubject(Yii::$app->params['email.systemSubject'])
                        ->send();
                }

                // Send grace period notice
                foreach ($graceProfiles as $profile) {
                    $user = $profile->user;
                    $params = '?url=' . Yii::$app->params['frontendUrl'] . '/preview/view-preview?id=' . $profile->id;
                    $link = Html::a('profile edit page', Yii::$app->params['frontendUrl'] . '/site/login' . $params);
                    $msg = 'Your IBNet profile "' . $profile->profile_name . '" has expired, but we have added a one week grace period
                        before final expiration.  Please visit your ' . $link . ' right away and make any necessary updates.  When you are 
                        finished, press the "Finsihed" button to reset your expiration date and keep your profile active in the 
                        directory.';
                    Yii::$app
                        ->mailer
                        ->compose(
                            ['html' => 'notification-html', 'text' => 'notification-text'], 
                            [
                                'title' =>  'Your IBNet Profile is About to Expire.', 
                                'message' => $msg
                            ])
                        ->setFrom([Yii::$app->params['email.admin']])
                        ->setTo([$user->email])
                        ->setSubject(Yii::$app->params['email.systemSubject'])
                        ->send();
                }

                // Send profile expired notice
                foreach ($expiredProfiles as $profile) {
                    $user = $profile->user;
                    $profile->inactivate();
                    $profile->updateAttributes(['profile_status' => Profile::STATUS_EXPIRED]);
                    $params = '?url=' . Yii::$app->params['frontendUrl'] . '/profile-mgmt/my-profiles';
                    $link = Html::a('profiles page', Yii::$app->params['frontendUrl'] . '/site/login' . $params);
                    $msg = 'Your IBNet profile "' . $profile->profile_name . '" has expired and is no longer visible in the public directory. 
                        But you can reactivate it at any time.  Simply visit your ' . $link . ', click the activate link, and follow the 
                        instructions to reactivae your profile.';
                    Yii::$app
                        ->mailer
                        ->compose(
                            ['html' => 'notification-html', 'text' => 'notification-text'], 
                            [
                                'title' => 'Your IBNet Profile Has Expired.',
                                'message' => $msg,
                            ])
                        ->setFrom([Yii::$app->params['email.admin']])
                        ->setTo([$user->email])
                        ->setSubject(Yii::$app->params['email.systemSubject'])
                        ->send();
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
