<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auth_item".
 *
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
    }

}
