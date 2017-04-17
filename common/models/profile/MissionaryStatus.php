<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "miss_status".
 *
 * @property string $id
 * @property string $status
 */
class MissionaryStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'miss_status';
    }

}
