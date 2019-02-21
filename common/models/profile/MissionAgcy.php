<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use common\models\missionary\Missionary;
use Yii;

/**
 * This is the model class for table "mission_agcy"
 *
 * @property string $id
 * @property string $mission
 * @property string $mission_acronym 
 * @property int $id
 * @property string $mission
 * @property string $mission_acronym
 * @property int $profile_id FOREIGN KEY (profile_id) REFERENCES profile (id)
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
    public function getProfiles()
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

    /**
     * Missionaries linked to a missionary
     * @return \yii\db\ActiveQuery
     */
    public function getMissionaries()
    {
        return $this->hasMany(Missionary::className(), ['mission_agcy_id' => 'id'])
            ->joinWith('profile');
    }

}
