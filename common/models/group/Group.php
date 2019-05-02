<?php
/**
 * Model class for table "group".
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\group;

use common\models\profile\Profile;
use common\models\group\GroupCalendarEvent;
use common\models\group\GroupMember;
use common\models\group\GroupKeyword;
use common\models\group\GroupPlace;
use common\models\missionary\MissionaryUpdate;
use common\rbac\PermissionGroup;
use common\models\group\PrayerTag;
use sadovojav\cutter\behaviors\CutterBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * @property int $id
 * @property int $user_id FOREIGN KEY (user_id) REFERENCES  user (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property string $transfer_token
 * @property int $reviewed
 * @property string $url_name
 * @property string $created_at
 * @property int $status
 * @property string $name
 * @property string $description
 * @property string image
 * @property int $permit_user
 * @property int $privacy
 * @property int $hide_on_profiles
 * @property int $not_searchable
 * @property string $group_level
 * @property string $level_description
 *
 * @property GroupHasPermitProfileType[] $groupHasPermitProfileTypes
 * @property Type[] $types
 */


class Group extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group';
    }

    /**
     * @var string $keyword search keywords on group information form
     */
    public $keyword;

    /**
     * @var string $newUser user email in the transfer group form.
     */
    public $newUserEmail;

    /**
     * @var string $subject subject line of contact member form
     */
    public $subject;

    /**
     * @var string $message message line of contact member form
     */
    public $message;

    /**
     * @const int $STATUS_* The status of the group.
     */
    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20;
    const STATUS_TRASH = 30;

    /**
     * @const int $LEVEL_* The geographical area of the group.
     */
    const LEVEL_LOCAL = 10;
    const LEVEL_REGIONAL = 20;
    const LEVEL_STATE = 30;
    const LEVEL_NATIONAL = 40;
    const LEVEL_INTERNATIONAL = 50;

    /**
     * @const int $RESOURCE_* Identify the source of a calendar event.
     */
    const RESOURCE_GROUP = 10;
    const RESOURCE_ICAL = 20;


    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            'image' => [
                'class' => CutterBehavior::className(),
                'attributes' => ['image'],
                'baseDir' => '/uploads/image',
                'basePath' => '@webroot',
            ],
        ];
    }

    public function scenarios() {
        return[
            'information' => ['name', 'description', 'image', 'ministry_id'],
            'update' => ['name', 'description', 'image', 'ministry_id'],
            'options' => ['permit_user', 'private', 'hide_on_profiles', 'not_searchable'],
            'location' => ['group_level'],
            'features' => ['feature_prayer', 'feature_calendar', 'feature_document', 'feature_chat', 'feature_forum', 'feature_notification', 'feature_update', 'feature_donation'],
            'transfer' => ['newUserEmail'],
            'group-member-action' => ['id', 'message'],
            'contact-member' => ['id', 'user_id', 'name', 'subject', 'message'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required', 'on' => 'information'],
            [['name'], 'string', 'max' => 60, 'on' => 'information'],
            [['name'], 'unique', 'on' => 'information'],
            [['description'], 'string',  'max' => 1500, 'on' => 'information', 'message' => 'Your text exceeds 1500 characters.'],
            [['place'], 'string', 'on' => 'information'],
            [['keyword'], 'string',  'max' => 12, 'on' => 'information'],
            [['name', 'description', 'place', 'keyword'], 'filter', 'filter' => 'strip_tags', 'on' => 'information'],
            [['image'], 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'information'],
            [['ministry_id'], 'safe', 'on' => 'information'],

            [['name', 'description'], 'required', 'on' => 'update'],
            [['name'], 'string', 'max' => 60, 'on' =>'update'],
            [['name'], 'validateName', 'on' => 'update'],
            [['description'], 'string',  'max' => 1500, 'on' => 'update', 'message' => 'Your text exceeds 1500 characters.'],
            [['place'], 'string', 'on' => 'update'],
            [['keyword'], 'string',  'max' => 12, 'on' => 'update'],
            [['name', 'description', 'place', 'keyword'], 'filter', 'filter' => 'strip_tags', 'on' => 'update'],
            [['image'], 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'update'],
            [['ministry_id'], 'safe', 'on' => 'update'],

            [['group_level'], 'required', 'on' => 'location'],
            [['group_level'], 'string', 'on' => 'location'],

            [['permit_user', 'private', 'hide_on_profiles', 'not_searchable'], 'safe', 'on' => 'options'],

            [['feature_prayer', 'feature_calendar', 'feature_document', 'feature_chat', 'feature_forum', 'feature_notification', 'feature_update', 'feature_donation'], 'safe', 'on' => 'features'],
        
            [['newUserEmail'], 'email', 'message' => 'Please enter a valid email', 'on' => 'transfer'],

            [['id'], 'safe', 'on' => 'group-member-action'],
            [['message'], 'string', 'on' => 'group-member-action'],

            [['subject', 'message'], 'required', 'on' => 'contact-member'],
            [['subject', 'message'], 'string', 'on' => 'contact-member'],
            [['id', 'user_id', 'name'], 'safe', 'on' => 'contact-member']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'description' => 'Description',
            'image' => '',
            'group_level' => 'Geographical Region',
            'level_description' => 'Comma delimited Keywords',
            'ministry_id' => 'Is this Group Tied to a Ministry?',
            'permit_user' => 'Allow users without public profiles (i.e. non-ministry individuals)',
            'private' => 'Private (Require approval for new members)',
            'hide_on_profiles' => 'Do not show group membership on public profiles',
            'not_searchable' => 'Exclude from search (leave unchecked to allow users to find this group in the search; search results will display group name and description.)',
            'feature_prayer' => '',
            'feature_calendar' => '',
            'feature_notification' => '',
            'feature_document' => '', 
            'feature_chat' => '', 
            'feature_forum' => '', 
            'feature_update' => '', 
            'feature_donation' => '',
            'newUserEmail' => '',
        ];
    }

    /**
     * Validate group name
     * Throws an error if name is not own name or unused name.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateName($attribute, $params)
    {
        if ($dupName = self::findOne(['name' => $this->name])) {
            if ($dupName->id != $this->id) {
                $this->addError($attribute, 'This name is already taken.');
                return false;
            }
        }
        return $this->$attribute;
    }

    /**
     * Process new group information
     *
     * @return mixed;
     */
    public function handleFormInformation()
    {
        if (NULL == $this->status) {
            $this->user_id = Yii::$app->user->identity->id;
            $this->url_name = Inflector::slug($this->name);
            $this->status = self::STATUS_NEW;

            // Create a new group member for group owner
            $user = Yii::$app->user->identity;
            $groupMember = new GroupMember();
            $groupMember->group_id = $group->id;
            $groupMember->user_id = $user->id;
            if ($profile = $user->indActiveProfile) {
                $groupMember->profile_id = $profile->id;
                if ('Missionary' == $profile->type) {
                    $groupMember->missionary_id = $profile->missionary->id;
                }
            }
            $groupMember->validate();
            $groupMember->save();
        }

        if ($this->validate() && $this->save()) {
            return $this;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canUpdateOwn()
    {
        return Yii::$app->user->can(PermissionGroup::UPDATE_OWN, ['Group' => $this]);
    }

    /**
     * @return bool
     */
    public function canAccess()
    {
        return Yii::$app->user->can(PermissionGroup::ACCESS, ['Group' => $this]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function inactivate()
    {
        return $this->updateAttributes(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function trash()
    {
        // delete group members? / delete prayer lists?  et. al. / update name to '' so others can use it
        return $this->updateAttributes(['status' => self::STATUS_TRASH]);
    }

    /**
     * Generate new group transfer token
     * @return string
     */
    public function generateGroupTransferToken($userId)
    {
        $token = $userId . '+' . Yii::$app->security->generateRandomString() . '_' . time();
        $this->updateAttributes(['transfer_token' => $token]);
        return $this;
    }

    /**
     * Check for valid group transfer token
     * @return boolean
     */
    public function checkGroupTransferToken($token)
    {   
        // Does the token match the db token?
        if ($token == $this->transfer_token) {                                       
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['tokenExpire.groupTransfer'];
            return $timestamp + $expire >= time();
        }         
        return false;                                      
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveGroupIds()
    {
        return static::find()->select('id')->where(['status' => self::STATUS_ACTIVE])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveGroups()
    {
        return static::find()->where(['status' => self::STATUS_ACTIVE])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaces()
    {
        return $this->hasMany(GroupPlace::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKeywords()
    {
        return $this->hasMany(GroupKeyword::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypes()
    {
        return $this->hasMany(Type::className(), ['id' => 'type_id'])->viaTable('group_has_permit_profile_type', ['group_id' => 'id']);
    }

    /**
     * Group member of current user
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMember()
    {
        return GroupMember::find()->where(['group_id' => $this->id, 'user_id' => Yii::$app->user->identity->id, 'status' => GroupMember::STATUS_ACTIVE])->one();
    }

    /**
     * All current group members
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMembers()
    {
        return $this->hasMany(GroupMember::className(), ['group_id' => 'id'])->where(['status' => GroupMember::STATUS_ACTIVE]);
    }

    /**
     * Pending member approvals
     * @return \yii\db\ActiveQuery
     */
    public function getPending()
    {
        return GroupMember::find()
            ->where(['group_id' => $this->id])
            ->andWhere(['status' => GroupMember::STATUS_PENDING])
            ->andWhere(['group_owner' => 0])
            ->count();
    }

    /**
     * New members since last visit to my-groups
     * @return \yii\db\ActiveQuery
     */
    public function getNewMembers()
    {
        return GroupMember::find()
            ->where(['group_id' => $this->id])
            ->andWhere(['status' => GroupMember::STATUS_ACTIVE])
            ->andWhere(['>', 'created_at', $this->last_visit])
            ->andWhere(['group_owner' => 0])
            ->count();
    }

    /**
     * Ministry connected with group
     * @return \yii\db\ActiveQuery
     */
    public function getMinistry()
    {
        return $this->hasOne(Profile::className(), ['id' => 'ministry_id']);
    }

    /**
     * Updates belonging to group missionaries
     * @return \yii\db\ActiveQuery
     */
    public function getUpdates()
    {
        return $this->hasMany(MissionaryUpdate::className(), ['missionary_id' => 'missionary_id'])
            ->via('groupMembers');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMemberId()
    {
        return GroupMember::find()->where(['group_id' => $this->id, 'user_id' => Yii::$app->user->identity->id])->one()->id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerTagList()
    {
        return $this->hasMany(PrayerTag::className(), ['group_id' => 'id'])->where(['deleted' => 0])->orderBy('tag');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerListNames()
    {
        $prayerList = Prayer::find()->with(['groupUser'])->where(['prayer.group_id' => $this->id, 'prayer.answered' => NULL, 'prayer.deleted' => 0])->all();
        $nameList = [];
        foreach ($prayerList as $prayer) {
            array_push($nameList, $prayer->fullName);
        }
        return array_unique($nameList);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdateListNames()
    {
        $updateList = MissionaryUpdate::find()->joinWith('groupMember')->where(['group_id' => $this->id])->all();
        $nameList = [];
        foreach ($updateList as $update) {
            array_push($nameList, $update->fullName);
        }
        return array_unique($nameList);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendarEvents()
    {
        return $this->hasMany(GroupCalendarEvent::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwniCals()
    {
        $nmId = $this->groupMember->id;
        return $this->hasMany(GroupIcalendarUrl::className(), ['group_id' => 'id'])
            ->where(['group_member_id' => $nmId, 'deleted' => 0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIcalEvents()
    {
        $icalList = [];
        if ($icals = $this->owniCals) {               // Check if user has imported any calendars         
            foreach ($icals as $ical) {
                if (isset($ical->icalendar) && ($events = $ical->icalendar->events)) {
                    foreach ($events as $event) {
                        $item = new \yii2fullcalendar\models\Event();
                        $item->id = $event->id;
                        $item->title = $event->SUMMARY;
                        $item->start = $event->DTSTART;
                        $item->end = $event->DTEND;
                        $item->color = $ical->color;
                        $item->resourceId = self::RESOURCE_ICAL;
                        $icalList[] = $item;
                    }
                }
            }
        }
        return $icalList;
    }

    
}
