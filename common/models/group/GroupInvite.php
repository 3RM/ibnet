<?php

namespace common\models\group;

use common\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "group_invite".
 *
 * @property int $id
 * @property string $email
 * @property int $created_at
 * @property int $group_id
 * @property string $token
 */
class GroupInvite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_invite';
    }

    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [ActiveRecord::EVENT_BEFORE_INSERT => 'created_at'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'group_id', 'token'], 'required'],
            [['group_id'], 'integer'],
            [['email'], 'email'],
            [['token'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'created_at' => 'Created At',
            'group_id' => 'Group ID',
            'token' => 'Token',
        ];
    }

    /**
     * Add invite
     * @return \yii\db\ActiveQuery
     */
    public function add($email, $gid)
    {
        $this->email = $email;
        $this->group_id = $gid;
        $this->token = Yii::$app->security->generateRandomString(32);
        $this->save();
        
        return true;
    }

    /**
     * Decline a group invititation to join 
     * @return boolean
     */
    public function decline()
    {
        $group = $this->group;
        $owner = $group->owner;
        $mail = $owner->subscription ?? new Subscription();
        $mail->headerColor = Subscription::COLOR_GROUP;
        $mail->headerImage = Subscription::IMAGE_GROUP;
        $mail->headerText = 'Group Invitation';
        $mail->to = $owner->email;
        $mail->subject = 'Invitation Declined';
        $mail->title = 'Message from IBNet group ' . $group->name;
        $mail->message = Yii::$app->user->isGuest ?
            'The user with email ' . $this->email . ' has declined your invitation to join ' . $group->name :
            Yii::$app->user->identity->fullName . ' has declined your invitation to join ' . $group->name;
        $mail->sendNotification();

        $this->delete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }
}
