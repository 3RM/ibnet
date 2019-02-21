<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "association".
 *
 * @property int $id
 * @property string $name
 * @property string $acronym
 * @property int $profile_id FOREIGN KEY (profile_id) REFERENCES profile (id)
 * @property int $status
 * @property int $reviewed
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
            ['name', 'required'],
            ['acronym', 'default', 'value' => NULL],
            ['name', 'unique'],
            ['acronym', 'unique'],
            ['name', 'string', 'max' => 60],
            ['acronym', 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Association',
            'acronym' => 'Association Acronym',
        ];
    }

    /**
     *  Individual profiles that have linked to this association as members
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_association', ['ass_id' => 'id'])
            ->where(['profile.status' => Profile::STATUS_ACTIVE]);
    }

    /* 
     * Links an association in the association table to its profile in the profile table
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}
