<?php

namespace common\models\group;

use common\models\Subscription;
use common\models\User;
use common\models\group\GroupMember;
use common\models\group\PrayerTag;
use common\models\group\GroupAlertQueue;
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
    const DURATIONLIST = [
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
     * @var string $toEmail to address
     */
    public $toEmail;

    /**
     * @var string $toName to name
     */
    public $toName;

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
            [['description'], 'string', 'on' => 'prayer'],
            [['description'], 'trim'],
            [['description'], 'filter', 'filter' => 'strip_tags'],
            [['group_id', 'group_member_id', 'duration', 'select'], 'safe', 'on' => 'prayer'],

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
     * Send a reply administrative message to requester
     * @return \yii\db\ActiveQuery
     */
    public function sendAdmin()
    {
        // Check subscription
        $sub = Subscription::getSubscriptionByEmail($this->groupUser->email);
        if ($sub->token && $sub->unsubscribe) {
            return false;
        }

        // Assemble message;
        $mailer = Yii::$app->mailer->compose(
                ['html' => 'group/newPrayerRequestAdmin-html', 'text' => 'group/newPrayerRequestAdmin-text'], 
                ['prayer' => $this, 'unsubTok' => $sub->token]
            )
            ->setFrom([$this->group->prayer_email => $this->group->name])
            ->setTo([$this->groupUser->email => $this->groupUser->fullName])
            ->setSubject($this->request);

        // Save message id for matching up reply emails
        $this->updateAttributes(['message_id' => $mailer->getSwiftMessage()->getId()]);

        return $mailer->send();
    }

    /**
     * Send a confirmation reply to a request update or answer
     * @return \yii\db\ActiveQuery
     */
    public function sendConfirmation($status)
    {
        // Check subscriptions
        $sub = Subscription::getSubscriptionByEmail($this->groupUser->email);
        if ($sub->token && $sub->unsubscribe) {
            return false;
        }

        // Assemble message;
        $subject = ($status == 'update') ? 'Prayer update received' : 'Prayer answer received';
        $message = ($status == 'update') ?
            'Your prayer request update was received.' :
            'Your prayer request answer was received.';
        return Yii::$app->mailer->compose(
                ['html' => 'group/prayerConfirmation-html', 'text' => 'group/prayerConfirmation-text'], 
                ['prayer' => $this, 'message' => $message, 'unsubTok' => $sub->token]
            )
            ->setFrom([$this->group->prayer_email => $this->group->name])
            ->setTo([$this->groupUser->email => $this->groupUser->fullName])
            ->setSubject($subject)
            ->send();
    }

    /**
     * Add prayer request to alert queue
     * @param  $status string prayer status (new|update|answer)
     * @return \yii\db\ActiveQuery
     */
    public function addToAlertQueue($status=GroupAlertQueue::PRAYER_STATUS_NEW)
    {
        $queue = GroupAlertQueue::findOne(['prayer_id' => $this->id]) ?? new GroupAlertQueue();
        $queue->group_id = $this->group_id;
        $queue->prayer_id = $this->id;
        $queue->prayer_status = $status;
        $queue->save();
    }

    /**
     * Prepare immediate prayer alert message
     * @param  $status string prayer status (new|update|answer)
     * @return \yii\db\ActiveQuery
     */
    public function prepareAlert($status=GroupAlertQueue::PRAYER_STATUS_NEW)
    {
        // Check subscriptions
        $sub = Subscription::getSubscriptionByEmail($this->toEmail);
        if ($sub->token && $sub->unsubscribe) {
            return false;
        }

        // Assemble message;
        $updates = $this->prayerUpdates;
        $mailer = Yii::$app->mailer->compose(
                ['html' => 'group/prayerAlert-html', 'text' => 'group/prayerAlert-text'], 
                ['prayer' => $this, 'status' => $status, 'updates' => $updates, 'unsubTok' => $sub->token]
            )
            ->setFrom([$this->group->prayer_email => $this->group->name])
            ->setTo([$this->toEmail => $this->toName])
            ->setSubject('Prayer Alert');
            
        return $mailer;
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

    /* Getter for requester email */
    public function getEmail()
    {
        return $this->groupUser->email;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * Updates for prayer
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerUpdates()
    {
        return $this->hasMany(PrayerUpdate::className(), ['prayer_id' => 'id'])->where(['deleted' => 0]);
    }

    /**
     * Updates for prayer that haven't been alerted
     * @return \yii\db\ActiveQuery
     */
    public function getAlertUpdates()
    {
        return $this->hasMany(PrayerUpdate::className(), ['prayer_id' => 'id'])->where(['alerted' => 0])->andWhere(['deleted' => 0]);
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