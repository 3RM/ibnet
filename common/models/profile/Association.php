<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "association".
 *
 * @property string $id
 * @property string $association
 * @property string $association_acronym
 * @property string $profile_id
 */
class Association extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'association';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['association', 'required'],
            ['association_acronym', 'default', 'value' => NULL],
            ['association', 'unique'],
            ['association_acronym', 'unique'],
            ['association', 'string', 'max' => 60],
            ['association_acronym', 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'association' => 'Association',
            'association_acronym' => 'Association Acronym',
        ];
    }

    /**
     * Links an association in the association table to its profile in the profile table
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}
