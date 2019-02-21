<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "state".
 *
 * @property int $id
 * @property string $date
 * @property int $users
 * @property string $type_array
 * @property string $sub_type_array
 * @property int $expired
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