<?php

namespace common\models\profile;

use common\models\profile\Profile;
use Yii;

/**
 * This is the model class for table "type".
 *
 * @property string $id
 * @property string $type
 */
class Staff extends \yii\db\ActiveRecord
{
    public $staffNames;                     // Names in the format "First (& Spouse) Last" or "First Last"

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'staff';
    }

    /**
     * Return ind_names in format "First (& Spouse) Last" if spouse
     * or "First Last" if no spouse
     * Assumes 'profile' is a sub object resulting from a join
     * 
     * @return string
     */
    public function getStaffNames()
    {
        if ($this->spouse != NULL) {
            $this->staffNames = $this->first . ' (& ' . $this->spouse . ') ' . $this->last;
        } else {
            $this->formattedNames = $this->first . ' ' . $this->last;
        }
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'staff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMinistry()
    {
        return $this->hasOne(Profile::className(), ['id' => 'ministry_id']);
    }
}
