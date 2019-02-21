<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "school_level".
 *
 * @property int $id
 * @property string $school_level
 * @property string $level_group
 */
class SchoolLevel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'school_level';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['school_level', 'required'],
            [['school_level'], 'string', 'max' => 40],
            [['school_level'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'school_level' => 'School Levels',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_school_level', ['school_level_id' => 'id']);
    }
}
