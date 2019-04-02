<?php

namespace common\models\network;

use Yii;

/**
 * This is the model class for table "icalender_main".
 *
 * @property int $id
 * @property int $special_id
 * @property string $METHOD
 * @property string $VERSION
 * @property string $PRODID
 * @property string $X-WR-CALNAME
 * @property string $X-WR-TIMEZONE
 * @property string $CALSCALE
 * @property string $PREFERRED_LANGUAGE
 * @property int $created_at
 */
class IcalenderMain extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'icalender_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['special_id', 'created_at'], 'integer'],
            [['METHOD', 'VERSION', 'PRODID', 'X-WR-CALNAME', 'X-WR-TIMEZONE', 'CALSCALE', 'PREFERRED_LANGUAGE'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'special_id' => 'Special ID',
            'METHOD' => 'M E T H O D',
            'VERSION' => 'V E R S I O N',
            'PRODID' => 'P R O D I D',
            'X-WR-CALNAME' => 'X W R C A L N A M E',
            'X-WR-TIMEZONE' => 'X W R T I M E Z O N E',
            'CALSCALE' => 'C A L S C A L E',
            'PREFERRED_LANGUAGE' => 'P R E F E R R E D L A N G U A G E',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(IcalenderEvent::className(), ['icalender_id' => 'id']);
    }
}
