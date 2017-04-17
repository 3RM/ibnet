<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "accreditation".
 *
 * @property string $id
 * @property string $association
 * @property string $acronym
 * @property string $website
 */
class Accreditation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accreditation';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_accreditation', ['accreditation_id' => 'id']);
    }

}
