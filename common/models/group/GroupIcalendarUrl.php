<?php

namespace common\models\group;

use common\models\group\IcalenderMain;
use Yii;

/**
 * This is the model class for table "group_icalendar_url".
 *
 * @property int $id
 * @property int $group_id FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property int $group_member_id
 * @property int $ical_id
 * @property string $url
 * @property string $color
 * @property int $deleted
 *
 * @property groupMember $groupMember
 * @property Group $group
 */
class GroupIcalendarUrl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_icalendar_url';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 20]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'url' => 'iCal Url',
            'color' => 'Color'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMember()
    {
        return $this->hasOne(GroupMember::className(), ['id' => 'group_member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIcalendar()
    {
        return $this->hasOne(IcalenderMain::className(), ['id' => 'ical_id']);
    }
}
