<?php

namespace common\models\profile;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "miss_housing".
 *
 * @property string $description
 * @property string $contact
 */

class missHousing extends \yii\db\ActiveRecord
{

    public $select;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'miss_housing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'contact'], 'required'],
            ['description', 'string', 'max' => 1000],
            ['contact', 'string', 'max' => 300],
            [['description', 'contact'], 'filter', 'filter' => 'strip_tags'],
            ['select', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description' => 'Description',
            'contact' => 'Contact Instructions',
        ];
    }

    /**
     *  @return mixed
     */
    public function handleFormMH($profile)
    {
        if ($this->validate() && $this->save()) {
   
            if ($profile->miss_housing_id != $this->id) {
                $this->link('profile', $profile);
            } 
            $oldSelect = arrayHelper::map($this->missHousingVisibility, 'id', 'id');
            if (empty($oldSelect) && ($select = $this->select) != NULL) {                           // handle case of new selection
                foreach ($select as $value) {
                    $v = MissHousingVisibility::findOne($value);
                        $this->link('missHousingVisibility', $v);
                }
            }          
            if (!empty($oldSelect) && empty($this->select))  {                                       // handle case of all unselected
                $v = $this->missHousingVisibility;
                foreach($v as $model) {
                    $model->unlink('missHousing', $this, $delete = TRUE);
                }
            }
            if (!empty($oldSelect) && ($select = $this->select) != NULL) {                           // handle all other cases of change in selection
                foreach($select as $value) {                                                         // link any new selections
                    if(!in_array($value, $oldSelect)) {
                        $v = MissHousingVisibility::findOne($value);
                        $this->link('missHousingVisibility', $v);
                    }
                }
                foreach($oldSelect as $value) {                                                     // unlink any selections that were removed
                    if(!in_array($value, $select)) {
                        $v = MissHousingVisibility::findOne($value);
                        $this->unlink('missHousingVisibility', $v, $delete = TRUE);
                    }
                }
            }
            return true;            
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['miss_housing_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMissHousingVisibility()
    {
        return $this->hasMany(MissHousingVisibility::className(), ['id' => 'miss_housing_visibility_id'])
            ->viaTable('miss_housing_has_miss_housing_visibility', ['miss_housing_id' => 'id']);
    }
}