<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "school".
 *
 * @property string $id
 * @property string $school
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
     * Links a list of schools to individual profiles
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
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
