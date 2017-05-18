<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property string $id
 * @property string $role
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role';
    }
}
