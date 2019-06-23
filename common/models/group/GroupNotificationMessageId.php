<?php

namespace common\models\group;

use common\models\group\GroupNotification;
use Yii;

/**
 * This is the model class for table "group_notification_message_id".
 *
 * @property int $notification_id
 * @property string $message_id
 */
class GroupNotificationMessageId extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_notification_message_id';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_id', 'message_id'], 'required'],
            [['notification_id'], 'integer'],
            [['message_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'message_id' => 'Message ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(GroupNotification::className(), ['id' => 'notification_id']);
    }
}
