<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "sub_type".
 *
 * @property string $id
 * @property string $type
 * @property string $sub_type
 */
class SubType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sub_type';
    }

}
