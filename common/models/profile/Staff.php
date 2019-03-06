<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use common\models\profile\Profile;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "type".
 *
 * @property int $id
 * @property int $staff_id FOREIGN KEY (staff_id) REFERENCES profile (id)
 * @property string $staff_type
 * @property string $staff_title
 * @property int $ministry_id FOREIGN KEY (ministry_id) REFERENCES profile (id)
 * @property int $home_church
 * @property int $church_pastor
 * @property int $ministry_of
 * @property int $ministry_other
 * @property int $sr_pastor
 * @property int $confirmed
 * @property int $reviewed
 */
class Staff extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'staff';
    }

    /**
     * @var string $type profile or ministry type
     */
    public $type;

    /**
     * @var string $name profile or ministry org_name
     */
    public $name;

    /**
     * @var string $urlLoc profile or ministry urlLoc
     */
    public $urlLoc;

    /**
     * @var string $urlName profile or ministry urlName
     */
    public $urlName;


    /**
     * @return array
     */
    public static function checkUnconfirmed($id)
    {
        return Staff::find()
            ->where(['ministry_id' => $id])
            ->andWhere(['confirmed' => NULL])
            ->exists();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'staff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMinistry()
    {
        return $this->hasOne(Profile::className(), ['id' => 'ministry_id']);
    }
}
