<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "miss_housing_visibility".
 *
 * @property string $description
 * @property string $contact
 */

class missHousingVisibility extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'miss_housing_visibility';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMissHousing()
    {
        return $this->hasMany(MissHousing::className(), ['id' => 'miss_housing_id'])
        	->viaTable('miss_housing_has_miss_housing_visibility', ['miss_housing_visibility_id' => 'id']);
    }

}