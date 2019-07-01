<?php
namespace console\controllers;

use common\models\Utility;
use common\models\group\IcalenderMain;
use common\models\group\GroupIcalendarUrl;
use fedemotta\cronjob\models\CronJob;
use uguranyum\icalender\iCalender;
use yii\console\Controller;

class CalendarController extends Controller
{
    
    /**
     * Clean iCalendar tables and do a fresh import for all icalendar urls
     * Cron job can be found at ~/crontab -e
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
                iCalender::cleanTables();
                $icalUrls = GroupIcalendarUrl::find()->where(['deleted' => 0])->all();
                foreach ($icalUrls as $icalUrl) { 
                    if (Utility::remoteFileExists($icalUrl->url) == false) {       // check if file exists
                        if ($icalUrl->error_on_import) {
                            $icalUrl->updateAttributes(['error_on_import' => 1]); 
                        }
                        continue;
                    } else {
                        if ($icalUrl->error_on_import == 1) {
                            $icalUrl->updateAttributes(['error_on_import' => 0]);
                        }
                    }
                    // Fetch remote iCalendar
                    try {
                        $icalender  = new iCalender($icalUrl->url);
                    } catch (\Exception $e) {
                        $icalender->cleanTemp();
                        echo $e->getMessage();
                        exit();
                    }
                    $icalender->cleanTemp();
                    $icalMain = iCalenderMain::find()->orderBy(['id' => SORT_DESC])->one();
                    $icalUrl->link('icalendar', $icalMain);
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
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));                     // php yii calendar/index
    }

}
