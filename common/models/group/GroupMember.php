<?php

namespace common\models\group;

use common\models\group\Group;
use common\models\group\Prayer;
use common\models\profile\Profile;
use common\models\User;
use Yii;

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
     * Approve member for group
     * @return boolean
     */
    public function approveMember()
    {
        $group = Group::findOne($this->group_id);
        $user = User::findOne($this->user_id);
        
        // Add to Discourse group
        
        // Email member
        $msg = 'Your request to join the group ' . $group->name . ' has been approved.';
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'site/notification-html', 'text' => 'site/notification-text'], 
                ['title' => '', 'message' => $msg]
            )
            ->setFrom(Yii::$app->params['email.no-reply'])
            ->setTo($user->email)
            ->setSubject('Notice from ' . $group->name . ' (IBNet group)')
            ->send();
        
        $this->updateAttributes(['status' => self::STATUS_ACTIVE, 'approval_date' => time()]);
        return true;
    }

    /**
     * Decline member for group
     * @return boolean
     */
    public function declineMember($extMsg=NULL)
    { 
        $group = Group::findOne($this->group_id);
        $user = User::findOne($this->user_id);
        
        // Email user
        $msg = 'Your request to join IBnet group "' . $group->name . '" has been declined.';
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'group/notification-html', 'text' => 'group/notification-text'], 
                ['title' => '', 'message' => $msg, 'extMsg' => $extMsg],
            )
            ->setFrom(Yii::$app->params['email.no-reply'])
            ->setTo($user->email)
            ->setSubject('Notice from ' . $group->name . ' (IBNet group)')
            ->send();
        
        $this->delete();
        return true;
    }

    /**
     * Remove member from group
     * @return boolean
     */
    public function removeMember($extMsg=NULL)
    {
        $group = Group::findOne($this->group_id);
        $user = User::findOne($this->user_id);
        
        // Remove from Discourse group
        
        // Email member
        $msg = 'You have been removed from IBnet group "' . $group->name . '."';
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'group/notification-html', 'text' => 'group/notification-text'], 
                ['title' => '', 'message' => $msg, 'extMsg' => $extMsg],
            )
            ->setFrom(Yii::$app->params['email.no-reply'])
            ->setTo($user->email)
            ->setSubject('Notice from ' . $group->name . ' (IBNet group)')
            ->send();

        $this->updateAttributes(['status' => self::STATUS_REMOVED, 'inactivate_date' => time()]);
        return true;
    }

    /**
     * Remove ban from group member
     * @return boolean
     */
    public function restore($extMsg=NULL)
    {
        $group = Group::findOne($this->group_id);
        $user = User::findOne($this->user_id);
        
        // Email member
        $msg = 'Your have unbanned from IBnet group "' . $group->name . '."  You now have the option to rejoin the group.';
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'group/notification-html', 'text' => 'group/notification-text'], 
                ['title' => '', 'message' => $msg, 'extMsg' => $extMsg],
            )
            ->setFrom(Yii::$app->params['email.no-reply'])
            ->setTo($user->email)
            ->setSubject('Notice from ' . $group->name . ' (IBNet group)')
            ->send();

        $this->delete();
        
        return true;
    }

    /**
     * Ban member for group
     * @return boolean
     */
    public function banMember($extMsg=NULL)
    {
        $group = Group::findOne($this->group_id);
        $user = User::findOne($this->user_id);
        
        // Remove from Discourse group
        
        // Email member
        $msg = 'You have been banned from IBnet group "' . $group->name . '."';
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'group/notification-html', 'text' => 'group/notification-text'], 
                ['title' => '', 'message' => $msg, 'extMsg' => $extMsg],
            )
            ->setFrom(Yii::$app->params['email.no-reply'])
            ->setTo($user->email)
            ->setSubject('Notice from ' . $group->name . ' (IBNet group)')
            ->send();

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
