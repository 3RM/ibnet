<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "school_level".
 *
 * @property string $id
 * @property string $school
 * @property string $school_acronym
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
    public function getProfile()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_school_level', ['school_level_id' => 'id']);
    }
}
