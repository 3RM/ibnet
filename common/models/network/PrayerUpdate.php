<?php

namespace common\models\network;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "prayer_update".
 *
 * @property int $id
 * @property int $prayer_id FOREIGN KEY (prayer_id) REFERENCES prayer (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property string $update
 * @property int $created_at
 *
 * @property Prayer $prayer
 */
class PrayerUpdate extends \yii\db\ActiveRecord
{
    /**
     * @var array $select Stores tag selections.
     */
    public $select;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prayer_update';
    }

    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['update'], 'string'],
            [['update'], 'trim'],
            [['update'], 'filter', 'filter' => 'strip_tags'],
            [['select'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'update' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayer()
    {
        return $this->hasOne(Prayer::className(), ['id' => 'prayer_id']);
    }
}
