<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auth_item_child".
 *
 */
class Permission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item_child';
    }

}
