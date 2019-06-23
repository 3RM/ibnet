<?php

namespace common\models\group;

use common\models\Subscription;
use common\models\User;
use common\models\group\GroupNotificationMessageID; use common\models\Utility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "group_notification".
 *
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property int $created_at
 * @property int $reply_to
 * @property string $subject
 * @property string $message
 */
class GroupNotification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_notification';
    }

    /**
     * @var string $toEmail to address
     */
    public $toEmail;

    /**
     * @var string $toName to name
     */
    public $toName;


    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'user_id', 'subject', 'message'], 'required'],
            [['group_id', 'user_id', 'reply_to'], 'integer'],
            [['message'], 'string'],
            [['subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'Group ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'reply_to' => 'Reply To',
            'subject' => 'Subject',
            'message' => 'Message',
        ];
    }

    /**
     * Prepare a group notification message
     * @param  $reply whether the message is a reply to previous notice
     * @return \yii\db\ActiveQuery
     */
    public function prepareNotification($reply=NULL)
    {
        // Check subscription
        $sub = Subscription::getSubscriptionByEmail($this->toEmail) ?? new Subscription();
        if ($sub->token && $sub->unsubscribe) {
            return false;
        } elseif (!$sub->token) {
            $sub->add($this->toEmail);
        }

        $view = $reply ? 'group/notice-reply' : 'group/notice';
        $subject = $reply ? 'Re: ' . $this->subject : $this->group->name . ': ' . $this->subject;

        // Assemble message;
        $mailer = Yii::$app->mailer->compose(
                ['html' => $view . '-html', 'text' => $view . '-text'], 
                ['notification' => $this, 'unsubTok' => $sub->token]
            )
            ->setFrom([$this->group->notice_email => $this->group->name])
            ->setTo([$this->toEmail => $this->toName])
            ->setSubject($subject);

        // Save message id for matching up reply emails
        $messageId = new GroupNotificationMessageID();
        $messageId->attributes = ['message_id' => $mailer->getSwiftMessage()->getId(), 'notification_id' => $this->id];
        $messageId->save();

        return $mailer;
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'reply_to']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTopParent()
    {
        $parent = $this;
        while ($parent->reply_to != NULL) {
            $parent = $parent->parent;
        }
        return $parent;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(self::className(), ['reply_to' => 'id'])->joinWith('user')->orderBy('id DESC');
    }
}
