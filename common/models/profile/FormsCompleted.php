<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "forms_completed".
 *
 * @property string $id
 * @property string $form_array
 */
class FormsCompleted extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'forms_completed';
    }
}
