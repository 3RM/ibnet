<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "fellowship".
 *
 * @property int $id
 * @property string $name
 * @property string $acronym
 * @property int $profile_id FOREIGN KEY (profile_id) REFERENCES profile(id)
 */
class Fellowship extends \yii\db\ActiveRecord
{
    /**
     * @const int $STATUS_* The status of the fellowship
     */
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20; // No longer active
    const STATUS_BLOCKED = 30; // Admin blocked

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
            'name' => 'Fellowship',
            'acronym' => 'Fellowship Acronym',
        ];
    }

    /**
     * Individual profiles that have linked to this fellowship as members
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_fellowship', ['flwship_id' => 'id'])
            ->where(['profile.status' => Profile::STATUS_ACTIVE]);
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
