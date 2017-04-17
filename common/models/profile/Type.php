<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "type".
 *
 * @property string $id
 * @property string $type
 */
class Type extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'type';
    }
}
