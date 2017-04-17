<?php
namespace console\controllers;

use fedemotta\cronjob\models\CronJob;
use console\\models\CheckProfileExpirations;
use yii\console\Controller;


/**
 * Cron controller
 */
class CheckProfileRenewalsController extends Controller
{
    
    /**
     * Run CheckProfileExpirations::RunCheck for a period of time
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
                //this is the function to execute for each day
                CheckProfileRenewals::some_method((string) $date);
            }
            $command->finish();
            return Controller::EXIT_CODE_NORMAL;
        }
    }

    /**
     * Run SomeModel::some_method for today only as the default action
     * @return int exit code
     */
    public function actionIndex(){
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
