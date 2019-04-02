<?php

namespace common\models\network;

use common\models\User; use common\models\Utility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "network_calendar_event".
 *
 * @property int $id
 * @property int $network_id FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property string $title
 * @property string $color
 * @property string $description
 * @property int $created_at
 * @property int $start
 * @property int $end
 * @property int $all_day
 * @property int $deleted
 *
 * @property Network $network
 */
class NetworkCalendarEvent extends ActiveRecord
{
    public $dates;

    const TIME_ZONE_UTC = 'UTC';
    const UNIX_MIN_YEAR = 1970;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_calendar_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['start', 'end'], 'integer'],
            [['description', 'color'], 'string'],
            [['title'], 'string', 'max' => 60],
            [['network_member_id', 'all_day'], 'safe'],
            [['network_id'], 'exist', 'skipOnError' => true, 'targetClass' => Network::className(), 'targetAttribute' => ['network_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Event Title',
            'description' => 'Description',
        ];
    }

    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::className(), ['id' => 'network_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkMember()
    {
        return $this->hasOne(NetworkMember::className(), ['id' => 'network_member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
                    ->via('networkMember');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFullName()
    {
        return $this->networkUser->fullName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function allEvents($id)
    {
        $events = self::find()->where(['network_id' => $id, 'deleted' => 0])->all();
        $eventList = [];
        foreach ($events as $event) {
            $item = new \yii2fullcalendar\models\Event();
            $item->id = $event->id;
            $item->title = $event->title;
            $item->color = $event->color;
            $item->start = $event->start * 1000;                                                    // *1000 converts to milliseconds required by FullCalendar
            $item->end = $event->end * 1000;
            $item->allDay = $event->all_day ? true : false;
            $item->resourceId = Network::RESOURCE_NETWORK;
            $eventList[] = $item;
        }
        return $eventList;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function upcomingEvents($id)
    {
        $events = self::find()
            ->where(['network_id' => $id])
            ->andWhere('end>=' . time())
            ->limit(5)
            ->orderBy('start DESC')
            ->all();
        foreach ($events as $event) {
            $event->formatDates();
        }
        return $events;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function formatDateTimes($resourceId)
    {
        if ($resourceId == Network::RESOURCE_NETWORK) {
            $start = $this->start;
            $end = $this->end;
            $allDay = $this->all_day ? true : false;
        } else {
            $start = self::iCalDateToDateTime($this->start)->getTimestamp();
            $end = self::iCalDateToDateTime($this->end)->getTimestamp();
            $allDay = (Yii::$app->formatter->asDate($start, 'php:His') == '000000') && (Yii::$app->formatter->asDate($end, 'php:His') == '000000') ? true : false;
        }
        if ($allDay) {                 // remove day for inclusive all day format of fullCalendar
            $end -= 24*3600;
        }

        $startYear = Yii::$app->formatter->asDate($start, 'php:Y');
        $endYear = Yii::$app->formatter->asDate($end, 'php:Y');
        $startMonth = Yii::$app->formatter->asDate($start, 'php:F');
        $endMonth = Yii::$app->formatter->asDate($end, 'php:F');
        $startDay = Yii::$app->formatter->asDate($start, 'php:j');
        $endDay = Yii::$app->formatter->asDate($end, 'php:j');
        $thisYear = date('Y');
        $startMins = Yii::$app->formatter->asDate($start, 'php:i') == '00' ? 'a' : ':ia';
        $endMins = Yii::$app->formatter->asDate($end, 'php:i') == '00' ? 'a' : ':ia';
        $dates = Yii::$app->formatter->asDate($start, 'php:F j');
        $dates .= $allDay ?
            NULL :
            '<span class="time">' . Yii::$app->formatter->asDate($start, 'php: g' . $startMins . 'a') . '</span>';
        if ($startYear == $thisYear) {
            if ($end) {                   
                if ($endYear == $thisYear) {
                    if ($startMonth == $endMonth) {
                        if ($startDay == $endDay) {
                            $dates .= $allDay ? 
                                '<span class="time"> ALL DAY</span>' :
                                '<span class="time">-' . Yii::$app->formatter->asDate($end, 'php: g' . $endMins) . '</span>';
                        } else { 
                            $dates .= $allDay ?
                                '-' . Yii::$app->formatter->asDate($end, 'php:j') . '<span class="time"> ALL DAY</span>' :
                                '-' . Yii::$app->formatter->asDate($end, 'php:j') . '<span class="time">' . Yii::$app->formatter->asDate($end, 'php: g' . $endMins) . '</span>';
                        }
                    } else {
                        $dates .= $allDay ?
                            '-' . Yii::$app->formatter->asDate($end, 'php:F j') . '<span class="time"> ALL DAY</span>' :
                            '-' . Yii::$app->formatter->asDate($end, 'php:F j') . '<span class="time">' . Yii::$app->formatter->asDate($end, 'php: g' . $endMins) . '</span>';
                    }
                } else {
                   $dates .= $allDay ?
                        '-' . Yii::$app->formatter->asDate($end, 'php:F j, Y') . '<span class="time"> ALL DAY</span>' :
                        '-' . Yii::$app->formatter->asDate($end, 'php:F j') . '<span class="time">' . Yii::$app->formatter->asDate($end, 'php: g' . $endMins) . '</span>' . Yii::$app->formatter->asDate($end, 'php:, Y');
                }
            }
        } else {
            if ($end) {
                if ($startYear != $endYear) {
                    $dates .= $allDay ?
                        '-' . Yii::$app->formatter->asDate($end, 'php:F j, Y') :
                        '-' . Yii::$app->formatter->asDate($end, 'php:F j')  . '<span class="time">' .  Yii::$app->formatter->asDate($end, 'php: g' . $endMins) . '</span>' . Yii::$app->formatter->asDate($end, 'php:, Y');
                } else {
                    if ($startMonth == $endMonth) {
                        if ($startDay == $endDay) {
                            $dates .= $allDay ?
                                Yii::$app->formatter->asDate($end, 'php:, Y') :
                                '<span class="time">-' . Yii::$app->formatter->asDate($end, 'php:g' . $endMins) . '</span>' . Yii::$app->formatter->asDate($end, 'php:, Y');
                        } else {
                            $dates .= $allDay ?
                                '-' . Yii::$app->formatter->asDate($end, 'php:j, Y') :
                                '-' . Yii::$app->formatter->asDate($end, 'php:j') . '<span class="time">' .  Yii::$app->formatter->asDate($end, 'php: g' . $endMins) . '</span>' . Yii::$app->formatter->asDate($end, 'php:, Y');
                        }
                    } else {
                        $dates .= $allDay ?
                            '-' . Yii::$app->formatter->asDate($end, 'php:F j, Y') :
                            '-' . Yii::$app->formatter->asDate($end, 'php:F j') . '<span class="time">' . Yii::$app->formatter->asDate($end, 'php: g' . $endMins) . '</span>' . Yii::$app->formatter->asDate($end, 'php:, Y');
                    }
                }
            }
        }

        $this->dates = $dates;
        return $this;
    }

    /**
     * Returns a `DateTime` object from an iCal date time format
     * credit: https://github.com/u01jmg3/ics-parser/blob/master/src/ICal/ICal.php
     *
     * @param  string  $icalDate
     * @param  boolean $forceTimeZone
     * @param  boolean $forceUtc
     * @return DateTime
     * @throws \Exception
     */
    public function iCalDateToDateTime($icalDate, $forceTimeZone = false, $forceUtc = false)
    {
        /**
         * iCal times may be in 3 formats, (https://www.kanzaki.com/docs/ical/dateTime.html)
         *
         * UTC:      Has a trailing 'Z'
         * Floating: No time zone reference specified, no trailing 'Z', use local time
         * TZID:     Set time zone as specified
         *
         * Use DateTime class objects to get around limitations with `mktime` and `gmmktime`.
         * Must have a local time zone set to process floating times.
         */
        $pattern  = '/\AT?Z?I?D?=?(.*):?'; // [1]: Time zone
        $pattern .= '([0-9]{4})';          // [2]: YYYY
        $pattern .= '([0-9]{2})';          // [3]: MM
        $pattern .= '([0-9]{2})';          // [4]: DD
        $pattern .= 'T?';                  //      Time delimiter
        $pattern .= '([0-9]{0,2})';        // [5]: HH
        $pattern .= '([0-9]{0,2})';        // [6]: MM
        $pattern .= '([0-9]{0,2})';        // [7]: SS
        $pattern .= '(Z?)/';               // [8]: UTC flag

        preg_match($pattern, $icalDate, $date);

        if (empty($date)) {
            throw new \Exception('Invalid iCal date format.');
        }

        // A Unix timestamp cannot represent a date prior to 1 Jan 1970
        $year  = $date[2];
        $isUtc = false;

        if ($year <= self::UNIX_MIN_YEAR) {
            $eventTimeZone = ltrim(strstr($icalDate, ':', true), 'TZID=');

            if (empty($eventTimeZone)) {
                $dateTime = new \DateTime($icalDate, new \DateTimeZone($this->defaultTimeZone));
            } else {
                $icalDate = ltrim(strstr($icalDate, ':'), ':');
                $dateTime = new \DateTime($icalDate, new \DateTimeZone($eventTimeZone));
            }
        } else {
            if ($forceTimeZone) {
                
                // TZID={Time Zone}:
                if (isset($date[1])) {
                    $eventTimeZone = rtrim($date[1], ':');
                }

                if ($date[8] === 'Z') {
                    $isUtc    = true;
                    $dateTime = new \DateTime('now', new \DateTimeZone(self::TIME_ZONE_UTC));
                } elseif (isset($eventTimeZone) && $this->isValidIanaTimeZoneId($eventTimeZone)) {
                    $dateTime = new \DateTime('now', new \DateTimeZone($eventTimeZone));
                } elseif (isset($eventTimeZone) && $this->isValidCldrTimeZoneId($eventTimeZone)) {
                    $dateTime = new \DateTime('now', new \DateTimeZone($this->isValidCldrTimeZoneId($eventTimeZone, true)));
                } else {
                    $dateTime = new \DateTime('now', new \DateTimeZone($this->defaultTimeZone));
                }
            } else {
                if ($forceUtc) {
                    $dateTime = new \DateTime('now', new \DateTimeZone(self::TIME_ZONE_UTC));
                } else {
                    $dateTime = new \DateTime('now');
                }
            }

            $dateTime->setDate((int) $date[2], (int) $date[3], (int) $date[4]);
            $dateTime->setTime((int) $date[5], (int) $date[6], (int) $date[7]);
        }

        if ($forceTimeZone && $isUtc) {
            $dateTime->setTimezone(new \DateTimeZone($this->defaultTimeZone));
        } elseif ($forceUtc) {
            $dateTime->setTimezone(new \DateTimeZone(self::TIME_ZONE_UTC));
        }

        return $dateTime;
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function formatDates()
    {
        $startYear = Yii::$app->formatter->asDate($this->start, 'php:Y');
        $endYear = Yii::$app->formatter->asDate($this->end, 'php:Y');
        $startMonth = Yii::$app->formatter->asDate($this->start, 'php:F');
        $endMonth = Yii::$app->formatter->asDate($this->end, 'php:F');
        $startDay = Yii::$app->formatter->asDate($this->start, 'php:j');
        $endDay = $this->all_day ? 
            Yii::$app->formatter->asDate($this->end - 24*3600, 'php:j') :
            Yii::$app->formatter->asDate($this->end, 'php:j');
        $thisYear = date('Y');
        $dates = Yii::$app->formatter->asDate($this->start, 'php:F j');
        if ($startYear == $thisYear) {
            if ($this->end) {          
                if ($endYear == $thisYear) {
                    if ($startMonth == $endMonth) {
                        if ($startDay != $endDay) {
                            $dates .= $this->all_day ?
                                '-' . Yii::$app->formatter->asDate($this->end - 24*3600, 'php:j') :
                                '-' . Yii::$app->formatter->asDate($this->end, 'php:j');
                        }
                    } else {
                        $dates .= $this->all_day ?
                            '-' . Yii::$app->formatter->asDate($this->end - 24*3600, 'php:F j') :
                            '-' . Yii::$app->formatter->asDate($this->end, 'php:F j');
                    }
                } else {
                    $dates .= $this->all_day ?
                        '-' . Yii::$app->formatter->asDate($this->end - 24*3600, 'php:F j, Y') :
                        '-' . Yii::$app->formatter->asDate($this->end, 'php:F j, Y');
                }
            }
        } else {
            if ($this->end) {
                if ($startYear != $endYear) {
                    $dates .= $this->all_day ?
                        '-' . Yii::$app->formatter->asDate($this->end - 24*3600, 'php:F j, Y') :
                        '-' . Yii::$app->formatter->asDate($this->end, 'php:F j, Y');
                } else {
                    if ($startMonth == $endMonth) {
                        if ($startDay == $endDay) {
                            $dates .= $this->all_day ?
                                Yii::$app->formatter->asDate($this->end - 24*3600, 'php:, Y') :
                                Yii::$app->formatter->asDate($this->end, 'php:, Y');
                        } else {
                            $dates .= $this->all_day ?
                                '-' . Yii::$app->formatter->asDate($this->end - 24*3600, 'php:j, Y') :
                                '-' . Yii::$app->formatter->asDate($this->end, 'php:j, Y');
                        }
                    } else {
                        $dates .= $this->all_day ?
                            '-' . Yii::$app->formatter->asDate($this->end - 24*3600, 'php:F j, Y') :
                            '-' . Yii::$app->formatter->asDate($this->end, 'php:F j, Y');
                    }
                }
            }
        }

        $this->dates = $dates;
        return $this;
    }
}
