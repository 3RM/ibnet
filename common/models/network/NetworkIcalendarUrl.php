<?php

namespace common\models\network;

use common\models\network\IcalenderMain;
use Yii;

/**
 * This is the model class for table "network_icalendar_url".
 *
 * @property int $id
 * @property int $network_id FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property int $network_member_id
 * @property int $ical_id
 * @property string $url
 * @property string $color
 * @property int $deleted
 *
 * @property NetworkMember $networkMember
 * @property Network $network
 */
class NetworkIcalendarUrl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_icalendar_url';
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
    public function getNetworkMember()
    {
        return $this->hasOne(NetworkMember::className(), ['id' => 'network_member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::className(), ['id' => 'network_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIcalendar()
    {
        return $this->hasOne(IcalenderMain::className(), ['id' => 'ical_id']);
    }
}
