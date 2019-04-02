<?php

namespace common\models\network;

use Yii;

/**
 * This is the model class for table "prayer_tag".
 *
 * @property int $id
 * @property string $tag
 */
class PrayerTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prayer_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tag'], 'required'],
            [['tag'], 'string', 'max' => 20],
            ['deleted', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tag' => '',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayers()
    {
        return $this->hasMany(Prayer::className(), ['id' => 'prayer_id'])->viaTable('prayer_has_prayer_tag', ['prayer_tag_id' => 'id']);
    }
}
