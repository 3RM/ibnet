<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "polity".
 *
 * @property string $id
 * @property string $polity
 */
class Polity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'polity';
    }
}
