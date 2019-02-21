<?php
namespace console\controllers;

use common\models\Utility;
use common\models\missionary\MissionaryUpdate;
use fedemotta\cronjob\models\CronJob;
use yii\console\Controller;

class VideoAccessibleController extends Controller
{
    
    /**
     * Send request to video APIs to check if videos are accessible and mark them in db
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
                
                $updates = MissionaryUpdate::find()
                    ->where(['deleted' => 0])
                    ->andWhere('to_date >= NOW()')
                    ->andWhere(['IS NOT', 'vimeo_url', NULL])
                    ->orWhere(['IS NOT', 'youtube_url', NULL])
                    ->all();
                foreach ($updates as $update) {
                    $update->getVideo();
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
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));                     // php yii video-accessible/index
    }

}
