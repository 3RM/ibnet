<?php

namespace common\models\group;

use common\models\Subscription;
use common\models\group\Group;
use common\models\group\Prayer;
use common\models\profile\Profile;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior; use common\models\Utility;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "group_member".
 *
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property int $profile_id
 * @property string $created_at
 */
class GroupMember extends \yii\db\ActiveRecord
{

    /**
     * @const int $STATUS_* The status of membership.
     */
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_LEFT = 20;
    const STATUS_REMOVED = 30;
    const STATUS_BANNED = 40;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_member';
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id'], 'required'],
            [['group_id', 'user_id', 'profile_id'], 'integer'],
            [['created_at'], 'safe'],
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
            'profile_id' => 'Profile ID',
            'created_at' => 'Created At',
            'email_prayer_alert' => '',
            'email_prayer_summary' => '',
            'email_update_alert' => '',
        ];
    }

    /**
     * Join a group
     * @return boolean
     */
    public static function joinGroup($gid, $invite=false)
    {
        $group = Group::findOne($gid);
        $user = Yii::$app->user->identity;

        // Add member
        $member = $group->groupMember ?? New GroupMember();
        $member->group_id = $_POST['join'];
        $member->user_id = $user->id;
        $member->profile_id = $user->indActiveProfile ?? NULL;
        $member->missionary_id = $user->isMissionary ? $user->missionary->id : NULL;
        if ($invite) {
            $member->status = GroupMember::STATUS_ACTIVE;
        } else {
            $member->status = $group->private == 1 ? GroupMember::STATUS_PENDING : GroupMember::STATUS_ACTIVE;
        }
        $member->save();

        // Notify group owner
        $mail = $user->subscription ?? new Subscription();
        $mail->headerColor = SUBSCRIPTION::COLOR_GROUP;
        $mail->headerImage = SUBSCRIPTION::IMAGE_GROUP;
        $mail->headerText = 'Group Membership';
        $mail->to = $group->owner->email;
        $mail->subject = 'Notice from ' . $group->name;
        $link = Html::a('Click here ', Yii::$app->params['url.loginFirst'] . 'group/group-members?id=' . $group->id);
        $verb = $group->private == 1 ? ' requested to join ' : ' joined ';
        $mail->message = $user->fullName . ' has just ' . $verb . ' your group ' . $group->name . '. ' . $link . ' to manage your group members.';
        $mail->sendNotification(); 

        // Add to forum group
        if ($group->feature_forum && !$group->private) {
            $group->addDiscourseUser($user->id);
        }
        
        return true;
    }

    /**
     * Leave a group
     * @return boolean
     */
    public static function leaveGroup($gid)
    {
        $group = Group::findOne($gid);
        $member = $group->groupMember;
        $member->delete();

        // Notify group owner
        $owner = $group->owner;
        $mail = $owner->subscription ?? new Subscription();
        $mail->headerColor = SUBSCRIPTION::COLOR_GROUP;
        $mail->headerImage = SUBSCRIPTION::IMAGE_GROUP;
        $mail->headerText = 'Group Membership';
        $mail->to = $owner->email;
        $mail->subject = 'Notice from ' . $group->name;
        $mail->message = Yii::$app->user->identity->fullName . ' has left your group ' . $group->name . '.';
        $mail->sendNotification();

        // Remove from forum group
        $group->removeDiscourseUser($user->id);

        return true;
    }

    /**
     * Approve member for group
     * @return boolean
     */
    public function approveMember()
    {
        $group = $this->group;
        $user = $this->user;

        // Add to Discourse group
        $group->addDiscourseUser($this->user_id);
        
        // Email member
        $mail = $user->subscription ?? new Subscription();
        $mail->headerColor = SUBSCRIPTION::COLOR_GROUP;
        $mail->headerImage = SUBSCRIPTION::IMAGE_GROUP;
        $mail->headerText = 'Join Request';
        $mail->to = $user->email;
        $mail->subject = 'Notice from ' . $group->name;
        $mail->message = 'Your request to join the group ' . $group->name . ' has been approved.';
        $mail->sendNotification();       
        
        $this->updateAttributes(['status' => self::STATUS_ACTIVE, 'approval_date' => time()]);
        return true;
    }

    /**
     * Decline member for group
     * @return boolean
     */
    public function declineMember($extMessage=NULL)
    { 
        $group = $this->group;
        $user = $this->user;

        // Email user
        $mail = $user->subscription ?? new Subscription();
        $mail->headerColor = SUBSCRIPTION::COLOR_GROUP;
        $mail->headerImage = SUBSCRIPTION::IMAGE_GROUP;
        $mail->headerText = 'Join Request';
        $mail->to = $user->email;
        $mail->subject = 'Notice from ' . $group->name;
        $mail->message = 'Your request to join IBnet group "' . $group->name . '" has been declined.';
        $mail->extMessage = $extMessage ?? NULL;
        $mail->sendNotification(); 
        
        $this->delete();
        return true;
    }

    /**
     * Remove member from group
     * @param  $extMessage Extended user message beyond system generated message
     * @return boolean
     */
    public function removeMember($extMessage=NULL)
    {
        $group = $this->group;
        $user = $this->user;
        
        // Remove from Discourse group
        $group->removeDiscourseUser($this->user_id);
        
        // Email member
        $mail = $user->subscription ?? new Subscription();
        $mail->headerColor = SUBSCRIPTION::COLOR_GROUP;
        $mail->headerImage = SUBSCRIPTION::IMAGE_GROUP;
        $mail->headerText = 'Group Membership';
        $mail->to = $user->email;
        $mail->subject = 'Notice from ' . $group->name;
        $mail->message = 'You have been removed from IBnet group "' . $group->name . '."';
        $mail->extMessage = $extMessage ?? NULL;
        $mail->sendNotification(); 

        $this->updateAttributes(['status' => self::STATUS_REMOVED, 'inactivate_date' => time()]);
        return true;
    }

    /**
     * Remove ban from group member
     * @return boolean
     */
    public function restore($extMessage=NUL)
    {
        $group = $this->group;
        $user = $this->user;

        // Email member
        $mail = $user->subscription ?? new Subscription();
        $mail->headerColor = SUBSCRIPTION::COLOR_GROUP;
        $mail->headerImage = SUBSCRIPTION::IMAGE_GROUP;
        $mail->headerText = 'Group Membership';
        $mail->to = $user->email;
        $mail->subject = 'Notice from ' . $group->name;
        $mail->message = 'Your have unbanned from IBnet group "' . $group->name . '."  You now have the option to rejoin the group.';
        $mail->extMessage = $extMessage ?? NULL;
        $mail->sendNotification(); 

        $this->delete();
        
        return true;
    }

    /**
     * Ban member for group
     * @return boolean
     */
    public function banMember($extMessage=NULL)
    {
        $group = $this->group;
        $user = $this->user;
        
        // Remove from Discourse group
        $group->removeDiscourseUser($user->id);
        
        // Email member
        $mail = $user->subscription ?? new Subscription();
        $mail->headerColor = SUBSCRIPTION::COLOR_GROUP;
        $mail->headerImage = SUBSCRIPTION::IMAGE_GROUP;
        $mail->headerText = 'Group Membership';
        $mail->to = $user->email;
        $mail->subject = 'Notice from ' . $group->name;
        $mail->message = 'You have been banned from IBnet group "' . $group->name . '."';
        $mail->extMessage = $extMessage ?? NULL;
        $mail->sendNotification(); 

        $this->updateAttributes(['status' => self::STATUS_BANNED, 'inactivate_date' => time()]);
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
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
    public static function groupMemberId($id)
    {
        return self::find()
            ->where(['group_id' => $id, 'user_id' => Yii::$app->user->identity->id])
            ->one()
            ->id;
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
    public function getEmail()
    {
        return $this->user->email;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayers()
    {
        return $this->hasMany(Prayer::className(), ['group_member_id' => 'id']);
    }
}
