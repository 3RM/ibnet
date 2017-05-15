<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "fellowship".
 *
 * @property string $id
 * @property string $fellowship
 * @property string $fellowship_acronym
 * @property string $profile_id
 */
class Fellowship extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fellowship';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['fellowship', 'required'],
            ['fellowship_acronym', 'default', 'value' => NULL],
            ['fellowship', 'unique'],
            ['fellowship_acronym', 'unique'],
            ['fellowship', 'string', 'max' => 60],
            ['fellowship_acronym', 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fellowship' => 'Fellowship',
            'fellowship_acronym' => 'Fellowship Acronym',
        ];
    }

    /**
     * Links a list of fellowships to individual profiles
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_fellowship', ['flwship_id' => 'id']);
    }

    /* 
     * Links a fellowship in the fellowship table to its profile in the profile table
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}
