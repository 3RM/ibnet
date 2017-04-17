<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "mission_agcy".
 *
 * @property string $id
 * @property string $mission
 * @property string $mission_acronym
 */

class MissionAgcy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mission_agcy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mission', 'mission_acronym'], 'required'],
            [['mission'], 'string', 'max' => 60],
            [['mission_acronym'], 'string', 'max' => 20],
            [['mission'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mission' => 'Mission Agency',
            'mission_acronym' => 'Acronym',
        ];
    }

    /**
     * Links a list of mission agcys to a church profile
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_mission_agcy', ['mission_agcy_id' => 'id']);
    }

    /*
     * Links a mission agency in the mission_agcy table to its profile in the profile table
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }

}
