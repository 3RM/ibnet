<?php

namespace common\models\group;

use common\models\group\GroupMember;
use common\models\group\PrayerTag;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "prayer".
 *
 * @property int $id
 * @property int $group_id
 * @property int $group_member_id
 * @property string $request
 * @property string $description
 * @property string $answer_description
 * @property int $duration
 * @property string $created_at
 *
 */
class Prayer extends \yii\db\ActiveRecord
{

    /**
     * @const array $duration The duration of the prayer request.
     */
    public static $duration = [
        10 => 'Urgent',
        20 => 'Short-term',
        30 => 'Long-term',
        40 => 'Permanent',
    ];

    /**
     * @var array $select Stores tag selections.
     */
    public $select;

    /**
     * @var array $fDuration Stores filture duration selection.
     */
    public $fDuration;

    /**
     * @var array $fSelect Stores filture tag selections.
     */
    public $fSelect;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prayer';
    }

    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'last_update'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['last_update'],
                ],
            ],
        ];
    }

    public function scenarios() {
        return[
            'prayer' => ['request', 'description', 'duration', 'group_id', 'group_member_id', 'select'],
            'update' => [],
            'answer' => ['answer_description', 'answer_date', 'answered'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request'], 'required', 'on' => 'prayer'],
            [['request'], 'string', 'max' => 255, 'on' => 'prayer'],
            [['request'], 'trim'],
            [['request'], 'filter', 'filter' => 'strip_tags'],
            [['duration'], 'integer', 'on' => 'prayer'],
            [['description'], 'string', 'on' => 'prayer'],
            [['description'], 'trim'],
            [['description'], 'filter', 'filter' => 'strip_tags'],
            [['group_id', 'group_member_id', 'select'], 'safe', 'on' => 'prayer'],

            [['answer_description'], 'string', 'on' => 'answer'],
            [['answer_description'], 'trim'],
            [['answer_description'], 'filter', 'filter' => 'strip_tags'],
            [['answer_date', 'answered'], 'safe', 'on' => 'answer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fullName' => 'Requested by',
            'request' => 'Request',
            'description' => 'Description',
            'answer_description' => 'Description',
            'duration' => 'Duration',
            'select' => 'Tag(s)',
            'fDuration' => '',
            'fSelect' => '',
            'tagNames' => 'Tags',
            'created_at' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function handleTags()
    {
        // process tags
        $oldSelect = arrayHelper::map($this->prayerTags, 'id', 'id');
        
        // handle case of new selection
        if (empty($oldSelect) && !empty($select = $this->select)) {
            foreach ($select as $tagId) {
                $t = PrayerTag::findOne($tagId);
                $this->link('prayerTags', $t);
            }
        }
        // handle case of all unselected
        if (!empty($oldSelect) && empty($this->select))  {
            $t = $this->prayerTag;
            foreach($t as $model) {
                $model->unlink('prayers', $this, $delete = TRUE);
            }
        }
        // handle all other cases of change in selection
        if (!empty($oldSelect) && !empty($select = $this->select)) {
            // link any new selections
            foreach($select as $tagId) {    
                if(!in_array($tagId, $oldSelect)) {
                    $t = PrayerTag::findOne($tagId);
                    $this->link('prayerTags', $t);
                }
            }
            // unlink any selections that were removed
            foreach($oldSelect as $tagId) {
                if(!in_array($tagId, $select)) {
                    $t = PrayerTag::findOne($tagId);
                    $this->unlink('prayerTags', $t, $delete = TRUE);
                }
            }
        }
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMember()
    {
        return $this->hasOne(GroupMember::className(), ['id' => 'group_member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
                    ->via('groupMember');
    }

    /* Getter for requester full name */
    public function getFullName()
    {
        return $this->groupUser->fullName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerUpdates()
    {
        return $this->hasMany(PrayerUpdate::className(), ['prayer_id' => 'id'])->where(['deleted' => 0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerTags()
    {
        return $this->hasMany(PrayerTag::className(), ['id' => 'prayer_tag_id'])->viaTable('prayer_has_prayer_tag', ['prayer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagNames()
    {
        return $this->prayerTags->tag;
    }

    /** 
     * Html for prayer list pdf export
     * @return string
     */
    public function getHtml()
    {
        return 
            ' <html lang="en-US">
                <head>
                  <meta charset="UTF-8" />
                  <title>Prayer List</title>
                  <style type="text/css" media="all">
                    @page { margin: 25.4mm; }
                    body { font-size: 16px; font-family: "Open Sans" !important; line-height: 1.2; color: #000; }
                    h1 { font-size: 30px; }
                    h2 { font-size: 24px; margin:0; }
                    p.request { margin:0 0 0 20px; overflow:hidden; }
                    div.description.single { margin:0 0 0 20px; overflow:hidden; }
                    div.description.double { margin:0 0 0 28px; overflow:hidden; }
                    div.description.triple { margin:0 0 0 36px; overflow:hidden; }
                    ul { margin:0 0 0 12px; }              
                  </style>
                </head>
                <body> ' . 
                    $_SESSION['html'] .
                '</body>
            </html>';
    }
}