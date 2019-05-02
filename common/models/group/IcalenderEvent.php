<?php

namespace common\models\group;

use Yii;

/**
 * This is the model class for table "icalender_event".
 *
 * @property int $id
 * @property int $icalender_id
 * @property int $special_id
 * @property int $UID
 * @property string $DTSTART
 * @property string $DTEND
 * @property string $SUMMARY
 * @property string $RESOURCES
 */
class IcalenderEvent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'icalender_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['icalender_id', 'special_id', 'UID'], 'integer'],
            [['DTSTART', 'DTEND', 'SUMMARY', 'RESOURCES'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'icalender_id' => 'Icalender ID',
            'special_id' => 'Special ID',
            'UID' => 'U I D',
            'DTSTART' => 'D T S T A R T',
            'DTEND' => 'D T E N D',
            'SUMMARY' => 'S U M M A R Y',
            'RESOURCES' => 'R E S O U R C E S',
        ];
    }
}