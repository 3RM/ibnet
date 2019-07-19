<?php
namespace console\controllers;

use common\models\group\Group;
use console\models\GroupMail;
use fedemotta\cronjob\models\CronJob;
use yii\console\Controller;

class GroupPrayerMailController extends Controller
{
    
    /**
     * Process group alerts and retrieve and process group emails
     * Cron job can be found at crontab -e
     * @param string $from
     * @param string $to
     * @return int exit code
     */
    public function actionInit($from, $to) {
        $dates  = CronJob::getDateRange($from, $to);
        $command = CronJob::run($this->id, $this->action->id, 0, CronJob::countDateRange($dates));
        if ($command === false) {
            return Controller::EXIT_CODE_ERROR;
        } else {
            foreach ($dates as $date) {
                
                // Process email prayer requests and send alerts
                $groups = Group::getActivePrayerGroups();
                foreach ($groups as $group) {
                    GroupMail::processPrayer($group);
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
    public function actionIndex() {
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));                     // php yii group-mail/index
    }

}
