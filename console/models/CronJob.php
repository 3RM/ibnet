<?php

namespace console\models;

use Yii;

/**
 * This is the model class for table "cron_job".
 *
 * @property string $id_cron_job
 * @property string $crontroller
 * @property string $action
 * @property string $limit
 * @property string $offset
 * @property string $running
 * @property string $success
 * @property string $started_at
 * @property string $ended_at
 * @property string $last_execution_time
 */
class CronJob extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cron_job';
    }
}
