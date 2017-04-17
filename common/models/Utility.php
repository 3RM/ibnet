<?php
namespace common\models;

use yii;
use yii\base\Model;

/**
 * Utility Class
 */
class Utility
{
	/**
     * "Pretty Print" an array
     * Use: Utility:pp($array);
     */
    public static function pp($var) {
        echo '<pre>'; var_dump($var); echo '</pre>'; die;
    }

   /**
     * Round to nearest multiple of 5
     * Credit: SW4 @ http://stackoverflow.com/questions/4133859/round-up-to-nearest-multiple-of-five-in-php
     * 
     * @return string
     */
    public function roundUpToAny($n,$x=5)
    {       
        return (round($n)%$x === 0) ? round($n) : round(($n+$x/2)/$x)*$x;
    }

    /**
     * Generate a unique random random
     * Credit: http://www.jamesbarnsley.com/site/2016/04/25/generating-a-unique-random-string-for-model-properties-in-yii2/
     * 
     * @return string
     */
    public function generateUniqueRandomString($model, $attribute, $length = 12) 
    {
            
        $randomString = Yii::$app->getSecurity()->generateRandomString($length);
            
        if(!$model->findOne([$attribute => $randomString])) {
            return $randomString;
        } else {
            return $this->generateUniqueRandomString($attribute, $length);
        }            
    }

}
