<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "profile_has_like".
 *
 * @property string $id
 * @property string $polity
 */
class ProfileHasLike extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile_has_like';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}
