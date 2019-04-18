<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ban_meta".
 *
 * @property int $id
 * @property int $user_id FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property int $profile_id FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property int $created_at
 * @property int $profile_previous_status
 * @property string $description
 * @property int $action
 */
class BanMeta extends \yii\db\ActiveRecord
{
    /**
     * @const int $ACTION_* Whether the related item is being banned or restored
     */
    const ACTION_BAN = 10;
    const ACTION_RESTORE = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ban_meta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'profile_id', 'created_at', 'profile_previous_status', 'action'], 'integer'],
            [['description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'profile_id' => 'Profile ID',
            'created_at' => 'Created At',
            'description' => 'Description',
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
}
