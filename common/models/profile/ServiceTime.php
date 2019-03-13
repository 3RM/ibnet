<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "service_time".
 *
 * @property int $id
 * @property string $day_1
 * @property string $time_1
 * @property string $description_1
 * @property string $day_2
 * @property string $time_2
 * @property string $description_2
 * @property string $day_3
 * @property string $time_3
 * @property string $description_3
 * @property string $day_4
 * @property string $time_4
 * @property string $description_4
 * @property string $day_5
 * @property string $time_5
 * @property string $description_5
 * @property string $day_6
 * @property string $time_6
 * @property string $description_6
 * @property int $reviewed
 */

class ServiceTime extends \yii\db\ActiveRecord
{

    /**
     * @const string $TYPE_* The profile types
     */
    const DAY = [
        1 => 'Sun',
        2 => 'Mon',
        3 => 'Tue',
        4 => 'Wed',
        5 => 'Thu',
        6 => 'Fri',
        7 => 'Sat',
    ];

    // Containers for collecting hours and minutes from form
    Public $hour_1;
    Public $minutes_1;
    Public $hour_2;
    Public $minutes_2;
    Public $hour_3;
    Public $minutes_3;
    Public $hour_4;
    Public $minutes_4;
    Public $hour_5;
    Public $minutes_5;
    Public $hour_6;
    Public $minutes_6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['day_1', 'hour_1', 'minutes_1', 'description_1'], 'required', 'message' => 'At least one Service Time is required.  Ensure all fields are completed.'],
            [['hour_1', 'minutes_1', 'hour_2', 'minutes_2', 'hour_3', 'minutes_3', 'hour_4', 'minutes_4', 'hour_5', 'minutes_5', 'hour_6', 'minutes_6'], 'safe'],
            [['day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'day_6'], 'string'],
            [['time_1', 'time_2', 'time_3', 'time_4', 'time_5', 'time_6'], 'safe'],
            [['description_1', 'description_2', 'description_3', 'description_4', 'description_5', 'description_6'], 'string', 'max' => 30],
            [['description_1', 'description_2', 'description_3', 'description_4', 'description_5', 'description_6'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'day_1' => NULL,
            'hour_1' => '',
            'minutes_1' => '',
            'description_1' => '',
            'day_2' => NULL,
            'hour_2' => '',
            'minutes_2' => '',
            'description_2' => '',
            'day_3' => NULL,
            'hour_3' => '',
            'minutes_3' => '',
            'description_3' => '',
            'day_4' => NULL,
            'hour_4' => '',
            'minutes_4' => '',
            'description_4' => '',
            'day_5' => NULL,
            'hour_5' => '',
            'minutes_5' => '',
            'description_5' => '',
            'day_6' => NULL,
            'hour_6' => '',
            'minutes_6' => '',
            'description_6' => '',
        ];
    }

    public function handleFormST($profile)
    {
        $this->concatTime();
        if ($this->validate() && $this->save()) {
            $this->link('profile', $profile);
            return $this;
        }
        return false;
    }


    /**
    * Convert hours (AM/PM) and minutes into time with format 7:45PM.
    */
    public function concatTime() 
    {
        if(NULL !== ($this->hour_1 && $this->minutes_1 && $this->day_1 && $this->description_1)) {
            if($this->hour_1<12) {
                $this->time_1 = $this->hour_1 . ':' . $this->minutes_1 . 'AM';
            } elseif($this->hour_1 == 12) {
                $this->time_1 = '12:' . $this->minutes_1 . 'PM';
            } else {
                $this->time_1 = $this->hour_1-12 . ':' . $this->minutes_1 . 'PM';
            }
        } else {
        // If hour or minutes is NULL, ensure that all fields are NULL to avoid saving an incomplete record
            $this->time_1 = NULL;
            $this->day_1 = NULL;
            $this->description_1 = NULL; 
        }
        
        if(NULL != ($this->hour_2 && $this->minutes_2 && $this->day_2 && $this->description_2)) {
            if($this->hour_2<12) {
                $this->time_2 = $this->hour_2 . ':' . $this->minutes_2 . 'AM';
            } elseif($this->hour_2 == 12) {
                $this->time_2 = '12:' . $this->minutes_2 . 'PM';
            } else {
                $this->time_2 = $this->hour_2-12 . ':' . $this->minutes_2 . 'PM';
            }
        } else {
        // If hour or minutes is NULL, ensure that all fields are NULL to avoid saving an incomplete record
            $this->time_2 = NULL;
            $this->day_2 = NULL;
            $this->description_2 = NULL;
        }
       
        if(NULL != ($this->hour_3 && $this->minutes_3 && $this->day_3 && $this->description_3)) {
            if($this->hour_3<12) {
                $this->time_3 = $this->hour_3 . ':' . $this->minutes_3 . 'AM';
            } elseif($this->hour_3 == 12) {
                $this->time_3 = '12:' . $this->minutes_3 . 'PM';
            } else {
                $this->time_3 = $this->hour_3-12 . ':' . $this->minutes_3 . 'PM';
            }
        } else {
        // If hour or minutes is NULL, ensure that all fields are NULL to avoid saving an incomplete record
            $this->time_3 = NULL;
            $this->day_3 = NULL;
            $this->description_3 = NULL;
        }
        
        if(NULL != ($this->hour_4 && $this->minutes_4 && $this->day_4 && $this->description_4)) {
            if($this->hour_4<12) {
                $this->time_4 = $this->hour_4 . ':' . $this->minutes_4 . 'AM';
            } elseif($this->hour_4 == 12) {
                $this->time_4 = '12:' . $this->minutes_4 . 'PM';
            } else {
                $this->time_4 = $this->hour_4-12 . ':' . $this->minutes_4 . 'PM';
            }
        } else {
        // If hour or minutes is NULL, ensure that all fields are NULL to avoid saving an incomplete record
            $this->time_4 = NULL;
            $this->day_4 = NULL;
            $this->description_4 = NULL;
        }
         
        if(NULL != ($this->hour_5 && $this->minutes_5 && $this->day_5 && $this->description_5)) {
            if($this->hour_5<12) {
                $this->time_5 = $this->hour_5 . ':' . $this->minutes_5 . 'AM';
            } elseif($this->hour_5 == 12) {
                $this->time_5 = '12:' . $this->minutes_5 . 'PM';
            } else {
                $this->time_5 = $this->hour_5-12 . ':' . $this->minutes_5 . 'PM';
            }
        } else {
        // If hour or minutes is NULL, ensure that all fields are NULL to avoid saving an incomplete record
            $this->time_5 = NULL;
            $this->day_5 = NULL;
            $this->description_5 = NULL;
        }

        if(NULL != ($this->hour_6 && $this->minutes_6 && $this->day_6 && $this->description_6)) {
            if($this->hour_6<12) {
                $this->time_6 = $this->hour_6 . ':' . $this->minutes_6 . 'AM';
            } elseif($this->hour_6 == 12) {
                $this->time_6 = '12:' . $this->minutes_6 . 'PM';
            } else {
                $this->time_6 = $this->hour_6-12 . ':' . $this->minutes_6 . 'PM';
            }
        } else {
        // If hour or minutes is NULL, ensure that all fields are NULL to avoid saving an incomplete record
            $this->time_6 = NULL;
            $this->day_6 = NULL;
            $this->description_6 = NULL;
        }
        return;
    }

    /**
    * Convert time in format 7:45PM back to hours (AM/PM) and minutes.
    */
    public function explodeTime() 
    {
        if(isset($this->day_1)) {
            if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$3', $this->time_1) == 'PM') {
                if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2$3', $this->time_1) == '12PM') {
                    $this->hour_1 = '12';
                } else {
                    $this->hour_1 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_1)+12;
                }
            } else {
                $this->hour_1 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_1);
            }
            $this->minutes_1 = preg_replace('/[0-9]?[0-9]:([0134])([05])[AP][M]/', '$1$2', $this->time_1);
        } 
        if(isset($this->day_2)) {
            if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$3', $this->time_2) == 'PM') {
                if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2$3', $this->time_2) == '12PM') {
                    $this->hour_2 = '12';
                } else {
                    $this->hour_2 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_2)+12;
                }
            } else {
                $this->hour_2 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_2);
            }
            $this->minutes_2 = preg_replace('/[0-9]?[0-9]:([0134])([05])[AP][M]/', '$1$2', $this->time_2);
        } 
        if(isset($this->day_3)) {
            if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$3', $this->time_3) == 'PM') {
                if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2$3', $this->time_3) == '12PM') {
                    $this->hour_3 = '12';
                } else {
                    $this->hour_3 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_3)+12;
                }
            } else {
                $this->hour_3 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_3);
            }
            $this->minutes_3 = preg_replace('/[0-9]?[0-9]:([0134])([05])[AP][M]/', '$1$2', $this->time_3);
        } 
        if(isset($this->day_4)) {
            if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$3', $this->time_4) == 'PM') {
                if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2$3', $this->time_4) == '12PM') {
                    $this->hour_4 = '12';
                } else {
                    $this->hour_4 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_4)+12;
                }
            } else {
                $this->hour_4 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_4);
            }
            $this->minutes_4 = preg_replace('/[0-9]?[0-9]:([0134])([05])[AP][M]/', '$1$2', $this->time_4);
        } 
        if(isset($this->day_5)) {
            if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$3', $this->time_5) == 'PM') {
                if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2$3', $this->time_5) == '12PM') {
                    $this->hour_5 = '12';
                } else {
                    $this->hour_5 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_5)+12;
                }
            } else {
                $this->hour_5 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_5);
            }
            $this->minutes_5 = preg_replace('/[0-9]?[0-9]:([0134])([05])[AP][M]/', '$1$2', $this->time_5);
        } 
       if(isset($this->day_6)) {
            if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$3', $this->time_6) == 'PM') {
                if(preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2$3', $this->time_6) == '12PM') {
                    $this->hour_6 = '12';
                } else {
                    $this->hour_6 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_6)+12;
                }
            } else {
                $this->hour_6 = preg_replace('/([0-9]?)([0-9]):[0134][05]([AP][M])/', '$1$2', $this->time_6);
            }
            $this->minutes_6 = preg_replace('/[0-9]?[0-9]:([0134])([05])[AP][M]/', '$1$2', $this->time_6);
        } 
        return;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}
