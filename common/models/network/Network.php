<?php
/**
 * Model class for table "network".
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\network;

use common\models\profile\Profile;
use common\models\Utility;
use common\models\network\NetworkCalendarEvent;
use common\models\network\NetworkMember;
use common\models\network\NetworkKeyword;
use common\models\network\NetworkPlace;
use common\models\missionary\MissionaryUpdate;
use common\rbac\PermissionNetwork;
use common\models\network\PrayerTag;
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
 * @property string $network_image
 * @property int $permit_user
 * @property int $privacy
 * @property int $hide_on_profiles
 * @property int $not_searchable
 * @property string $network_level
 * @property string $level_description
 *
 * @property NetworkHasPermitProfileType[] $networkHasPermitProfileTypes
 * @property Type[] $types
 */


class Network extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network';
    }

    /**
     * @var string $keyword search keywords on network information form
     */
    public $keyword;

    /**
     * @var string $newUser user email in the transfer network form.
     */
    public $newUserEmail;

    /**
     * @const int $STATUS_* The status of the network.
     */
    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20;
    const STATUS_TRASH = 30;

    /**
     * @const int $RESOURCE_* Identify the source of a calendar event.
     */
    const RESOURCE_NETWORK = 10;
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
            'network_image' => [
                'class' => CutterBehavior::className(),
                'attributes' => ['network_image'],
                'baseDir' => '/uploads/image',
                'basePath' => '@webroot',
            ],
        ];
    }

    public function scenarios() {
        return[
            'information' => ['name', 'description', 'network_image', 'ministry_id'],
            'update' => ['name', 'description', 'network_image', 'ministry_id'],
            'options' => ['permit_user', 'private', 'hide_on_profiles', 'not_searchable'],
            'location' => ['network_level'],
            'features' => ['feature_prayer', 'feature_calendar', 'feature_document', 'feature_chat', 'feature_forum', 'feature_notification', 'feature_update', 'feature_donation'],
            'transfer' => ['newUserEmail'],
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
            [['network_image'], 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'information'],
            [['ministry_id'], 'safe', 'on' => 'information'],

            [['name', 'description'], 'required', 'on' => 'update'],
            [['name'], 'string', 'max' => 60, 'on' =>'update'],
            [['name'], 'validateName', 'on' => 'update'],
            [['description'], 'string',  'max' => 1500, 'on' => 'update', 'message' => 'Your text exceeds 1500 characters.'],
            [['place'], 'string', 'on' => 'update'],
            [['keyword'], 'string',  'max' => 12, 'on' => 'update'],
            [['name', 'description', 'place', 'keyword'], 'filter', 'filter' => 'strip_tags', 'on' => 'update'],
            [['network_image'], 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'update'],
            [['ministry_id'], 'safe', 'on' => 'update'],

            [['network_level'], 'required', 'on' => 'location'],
            [['network_level'], 'string', 'on' => 'location'],

            [['permit_user', 'private', 'hide_on_profiles', 'not_searchable'], 'safe', 'on' => 'options'],

            [['feature_prayer', 'feature_calendar', 'feature_document', 'feature_chat', 'feature_forum', 'feature_notification', 'feature_update', 'feature_donation'], 'safe', 'on' => 'features'],
        
            [['newUserEmail'], 'email', 'message' => 'Please enter a valid email', 'on' => 'transfer'],
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
            'network_image' => '',
            'network_level' => 'Geographical Region',
            'level_description' => 'Comma delimited Keywords',
            'ministry_id' => 'Is this Network Tied to a Ministry?',
            'permit_user' => 'Allow users without public profiles (i.e. non-ministry individuals)',
            'private' => 'Private (Require approval for new members)',
            'hide_on_profiles' => 'Do not show network membership on public profiles',
            'not_searchable' => 'Exclude from search (leave unchecked to allow users to find this network in the search; search results will display network name and description.)',
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
     * Validate Network name
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
     * Process new network information
     *
     * @return mixed;
     */
    public function handleFormInformation()
    {
        if (NULL == $this->status) {
            $this->user_id = Yii::$app->user->identity->id;
            $this->url_name = Inflector::slug($this->name);
            $this->status = self::STATUS_NEW;

            // Create a new network member for network owner
            $user = Yii::$app->user->identity;
            $networkMember = new NetworkMember();
            $networkMember->network_id = $network->id;
            $networkMember->user_id = $user->id;
            if ($profile = $user->indActiveProfile) {
                $networkMember->profile_id = $profile->id;
                if ('Missionary' == $profile->type) {
                    $networkMember->missionary_id = $profile->missionary->id;
                }
            }
            $networkMember->validate();
            $networkMember->save();
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
        return Yii::$app->user->can(PermissionNetwork::UPDATE_OWN, ['Network' => $this]);
    }

    /**
     * @return bool
     */
    public function canAccess()
    {
        return Yii::$app->user->can(PermissionNetwork::ACCESS, ['Network' => $this]);
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
        // delete network members? / delete prayer lists?  et. al. / update name to '' so others can use it
        return $this->updateAttributes(['status' => self::STATUS_TRASH]);
    }

    /**
     * Generate new network transfer token
     * @return string
     */
    public function generateNetworkTransferToken($userId)
    {
        $token = $userId . '+' . Yii::$app->security->generateRandomString() . '_' . time();
        $this->updateAttributes(['transfer_token' => $token]);
        return $this;
    }

    /**
     * Check for valid network transfer token
     * @return boolean
     */
    public function checkNetworkTransferToken($token)
    {   
        // Does the token match the db token?
        if ($token == $this->transfer_token) {                                       
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['tokenExpire.networkTransfer'];
            return $timestamp + $expire >= time();
        }         
        return false;                                      
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveNetworkIds()
    {
        return static::find()->select('id')->where(['status' => self::STATUS_ACTIVE])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveNetworks()
    {
        return static::find()->where(['status' => self::STATUS_ACTIVE])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaces()
    {
        return $this->hasMany(NetworkPlace::className(), ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKeywords()
    {
        return $this->hasMany(NetworkKeyword::className(), ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypes()
    {
        return $this->hasMany(Type::className(), ['id' => 'type_id'])->viaTable('network_has_permit_profile_type', ['network_id' => 'id']);
    }

    /**
     * Network member of current user
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkMember()
    {
        return NetworkMember::find()->where(['network_id' => $this->id, 'user_id' => Yii::$app->user->identity->id, 'left_network' => 0])->one();
    }

    /**
     * All current network members
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkMembers()
    {
        return $this->hasMany(NetworkMember::className(), ['network_id' => 'id'])->where(['left_network' => 0]);
    }

    /**
     * Ministry connected with network
     * @return \yii\db\ActiveQuery
     */
    public function getMinistry()
    {
        return $this->hasOne(Profile::className(), ['id' => 'ministry_id']);
    }

    /**
     * Updates belonging to network missionaries
     * @return \yii\db\ActiveQuery
     */
    public function getUpdates()
    {
        return $this->hasMany(MissionaryUpdate::className(), ['missionary_id' => 'missionary_id'])
            ->via('networkMembers');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkMemberId()
    {
        return NetworkMember::find()->where(['network_id' => $this->id, 'user_id' => Yii::$app->user->identity->id])->one()->id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerTagList()
    {
        return $this->hasMany(PrayerTag::className(), ['network_id' => 'id'])->where(['deleted' => 0])->orderBy('tag');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerListNames()
    {
        $prayerList = Prayer::find()->with(['networkUser'])->where(['prayer.network_id' => $this->id, 'prayer.answered' => NULL, 'prayer.deleted' => 0])->all();
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
        $updateList = MissionaryUpdate::find()->joinWith('networkMember')->where(['network_id' => $this->id])->all();
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
        return $this->hasMany(NetworkCalendarEvent::className(), ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwniCals()
    {
        $nmId = $this->networkMember->id;
        return $this->hasMany(NetworkIcalendarUrl::className(), ['network_id' => 'id'])
            ->where(['network_member_id' => $nmId, 'deleted' => 0]);
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
