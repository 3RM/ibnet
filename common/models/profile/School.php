<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "school".
 *
 * @property int $id
 * @property string $school
 * @property string $school_acronym
 * @property string $city
 * @property string $st_prov_reg
 * @property string $country
 * @property int $ib
 * @property int $closed
 * @property int $profile_id FOREIGN KEY (profile_id) REFERENCES profile(id)
 */
class School extends \yii\db\ActiveRecord
{

    public $formattedNames;
    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'school';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['school'], 'unique'],
            [['school'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'school' => 'School',
        ];
    }

    /**
     * Individual profiles that have listed this school as their alma mater
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_school', ['school_id' => 'id']);
    }

    /* 
     * Links a school in the school table to its profile in the profile table
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}
