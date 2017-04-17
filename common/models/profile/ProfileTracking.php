<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "state".
 *
 * @property string $state
 * @property string $abbreviation
 */
class ProfileTracking extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile_tracking';
    }
}