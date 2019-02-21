<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property string $geoname
 * @property string $name
 * @property string $asciiname
 * @property string $alternatenames
 * @property string $latitude
 * @property string $longitude
 * @property string $feature_class
 * @property string $feature_code
 * @property string $country_code
 * @property string $cc2
 * @property string $admin1_code
 * @property string $admin2_code
 * @property string $admin3_code
 * @property string $admin4_code
 * @property string $population
 * @property string $elevation
 * @property string $dem
 * @property string $timezone
 * @property string $modification_date
 */
class Geonames extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geonames';
    }


}
