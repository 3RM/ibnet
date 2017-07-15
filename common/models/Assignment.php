<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "assignment".
 *
 * @property string $id
 * @property string $role
 */
class Assignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_assignment';
    }
}
