<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property string $iso
 * @property string $name
 * @property string $printable_name
 * @property string $iso3
 * @property string $numcode
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['printable_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'printable_name' => 'Country',
        ];
    }
}
