<?php
namespace console\controllers;

use common\models\User;
use common\models\profile\Profile;
use common\models\profile\ProfileTracking;
use fedemotta\cronjob\models\CronJob;
use yii;
use yii\console\Controller;
use yii\db\Expression;

class ProfileTrackingController extends Controller
{
    
    /**
     * Check profile expirations
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
                $type = Yii::$app->db->createCommand('SELECT type,COUNT(*) as count FROM profile WHERE status=10 GROUP BY type ORDER BY count DESC')
                    ->queryAll();
                $sub_type = Yii::$app->db->createCommand('SELECT sub_type,COUNT(*) as count FROM profile WHERE status=10 GROUP BY sub_type ORDER BY count DESC')
                    ->queryAll();

                $stat = new ProfileTracking();
                $stat->date = new Expression('NOW()');
                $stat->users = User::find()->where(['status' => User::STATUS_ACTIVE])->count();
                $stat->type_array = serialize($type);
                $stat->sub_type_array = serialize($sub_type);
                $stat->expired = Profile::find()->where(['status' => Profile::STATUS_EXPIRED])->andWhere('inactivation_date=DATE_SUB(CURDATE(), INTERVAL 7 DAY)')->count();
                $stat->save();
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
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));         // php yii profile-tracking/index
    }

    /**
     * Run SomeModel::some_method for yesterday
     * @return int exit code
     */
    public function actionYesterday() {
        return $this->actionInit(date("Y-m-d", strtotime("-1 days")), date("Y-m-d", strtotime("-1 days")));
    }
}
