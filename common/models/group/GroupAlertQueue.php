<?php

namespace common\models\group;

use common\models\missionary\missionaryUpdate;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "group_alert_queue".
 *
 * @property int $id
 * @property int $created_at
 * @property int $prayer_id
 * @property int $prayer_status
 */
class GroupAlertQueue extends \yii\db\ActiveRecord
{
    /**
     * @const int PRAYER_STATUS_* prayer status
     */
    const PRAYER_STATUS_NEW = 10;
    const PRAYER_STATUS_UPDATE = 20;
    const PRAYER_STATUS_ANSWER = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_alert_queue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'group_id', 'prayer_id', 'update_id', 'prayer_status'], 'integer'],
        ];
    }

    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'prayer_id' => 'Prayer ID',
            'update_id' => 'Update ID',
            'prayer_status' => 'Prayer Status',
        ];
    }

    /**
     * Clear the prayer alert queue; truncate the table
     * @return \yii\db\ActiveQuery
     */
    public function clearQueue()
    {
        self::deleteAll(['<>', 'alerted', 0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayer()
    {
        return $this->hasOne(Prayer::className(), ['id' => 'prayer_id'])->where(['deleted' => 0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerWithUpdates()
    {
        return $this->hasOne(Prayer::className(), ['id' => 'prayer_id'])->where(['deleted' => 0])
            ->joinWith('prayerUpdates');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdateQueue()
    {
        return self::find()
            ->joinWith('missionaryUpdate')
            ->where(['alerted' => 0])
            ->andWhere('((`missionary_update`.`alert_status`=' . MissionaryUpdate::ALERT_USER_SENT . ') 
                OR (FROM_UNIXTIME(`group_alert_queue`.`created_at`) < (NOW() - INTERVAL ' . Yii::$app->params['delay.missionaryUpdate'] . ' SECOND)))')
            ->all();
    }

    /**
     * Missionary updates will not be alerted before a of delay.missionaryUpdate minutes
     * This gives the missionary time to make additional updates
     * The missionary can also pause the alert
     * @return \yii\db\ActiveQuery
     */
    public function getMissionaryUpdate()
    {
        return $this->hasOne(MissionaryUpdate::className(), ['id' => 'update_id'])
            ->where(['deleted' => 0])
            ->andWhere(['or', ['alert_status' => MissionaryUpdate::ALERT_ENABLED], ['alert_status' => MissionaryUpdate::ALERT_USER_SENT]]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMissionary()
    {
        return $this->hasOne(Missionary::className(), ['id' => 'missionary_id']);
    }
}
