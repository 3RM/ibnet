<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "worship_style".
 *
 * @property string $id
 * @property string $style
 */
class WorshipStyle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worship_style';
    }
}
