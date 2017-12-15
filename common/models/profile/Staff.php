<?php

namespace common\models\profile;

use common\models\profile\Profile;
use Yii;
use yii\helpers\ArrayHelper;

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
     * @return array
     */
    public function checkUnconfirmed($id)
    {
        return Staff::find()
            ->where(['ministry_id' => $id])
            ->andWhere(['confirmed' => NULL])
            ->exists();
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOtherMinistries($id)
    {
        if ($ministries = self::find()
            ->where(['staff_id' => $id])
            ->andWhere(['ministry_other' => 1])
            ->andWhere(['confirmed' => 1])
            ->orderBy('id Asc')
            ->all()) {
            $i = 0;
            foreach ($ministries as $mstry) {                                                          // Combine multiple staff titles for same ministry
                if ($i > 0 && ($mstry['ministry_id'] == $ministries[$i-1]['ministry_id'])) {
                    $ministries[$i-1]['staff_title'] .= ' & ' . $mstry['staff_title'];
                    unset($ministries[$i]);
                    $ministries = array_values($ministries);
                    continue;
                }
                $i++;
            }
            $ids = ArrayHelper::getColumn($ministries, 'ministry_id');
            $names = ArrayHelper::getColumn($ministries, 'staff_title');
            $otherMinistryArray = Profile::findAll($ids);

            $i = 0;
            foreach ($otherMinistryArray as $min) {
                $min->titleM = $names[$i];
                $i++;
            }
            return $otherMinistryArray;
        }
        return NULL;
    }    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrPastor($id)
    {
       
        if ($staff = self::find()
            ->where(['ministry_id' => $id])
            ->andWhere(['sr_pastor' => 1])
            ->andWhere(['confirmed' => 1])
            ->one()) {
            return $pastor = $staff->profile;
        } else {
            return NULL;
        }
    }
}
