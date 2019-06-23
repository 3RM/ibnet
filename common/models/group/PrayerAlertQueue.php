<?php

namespace common\models\group;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "prayer_alert_queue".
 *
 * @property int $id
 * @property int $created_at
 * @property int $prayer_id
 * @property int $status
 */
class PrayerAlertQueue extends \yii\db\ActiveRecord
{
    /**
     * @const int STATUS_* User account status
     */
    const STATUS_NEW = 10;
    const STATUS_UPDATE = 20;
    const STATUS_ANSWER = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prayer_alert_queue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'group_id', 'prayer_id', 'status'], 'integer'],
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
            'status' => 'Status',
        ];
    }

    /**
     * Clear the prayer alert queue; truncate the table
     * @return \yii\db\ActiveQuery
     */
    public function clearQueue()
    {
        return self::deleteAll(['<>', 'alerted', 0]);
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
            ->with('prayerUpdates');
    }
}
