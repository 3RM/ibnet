<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "bible".
 *
 * @property string $id
 * @property string $bible
 */
class Bible extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bible';
    }
}
