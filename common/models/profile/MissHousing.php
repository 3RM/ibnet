<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "miss_housing".
 *
 * @property int $id
 * @property string $description
 * @property string $contact
 * @property int $trailer
 * @property int $water
 * @property int $electric
 * @property int $sewage
 * @property int $reviewed
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
            [['select', 'trailer', 'water', 'electric', 'sewage'], 'safe'],
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
            'trailer' => 'Parking',
            'water' => 'Water Hookup',
            'electric' => 'Electric Hookup',
            'sewage' => 'Sewage Hookup',
        ];
    }

    /**
     *  @return mixed
     */
    public function handleFormMH($profile)
    {
        if ($this->validate() && $this->save()) {
            if (!$profile->missHousing) {
                $this->link('profile', $profile);
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
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}