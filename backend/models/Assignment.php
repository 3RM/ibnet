<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auth_assignment".
 *
 * @property string $item_id
 * @property string $user_id
 * @property integer $created_at
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['item_name'], 'required'],
        	[['user_id', 'created_at'], 'safe'],
        ];
    }
}
