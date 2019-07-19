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
use common\models\group\GroupNotification;
use common\models\group\GroupKeyword;
use common\models\group\GroupPlace;
use common\models\group\GroupAlertQueue;
use common\models\missionary\MissionaryUpdate;
use common\models\Subscription;
use common\models\Utility;
use common\models\User;
use common\rbac\PermissionGroup;
use common\models\group\PrayerTag;
use GuzzleHttp\Client;
use sadovojav\cutter\behaviors\CutterBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
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
     * @var string $emails list of emails on invite modal
     */
    public $emails;

    /**
     * @var string $subject subject line of contact member form
     */
    public $subject;

    /**
     * @var string $message message line of contact member form
     */
    public $message;

    /**
     * @var string $categoryName Discourse forum category name
     */
    public $categoryName;

    /**
     * @var string $oldCategoryName Discourse forum old category name
     */
    public $oldCategoryName;

    /**
     * @var string $categoryName Discourse forum category name
     */
    public $_categoryDescription;

    /**
     * @var string $categoryBannerColor Discourse forum category banner color
     */
    public $categoryBannerColor;

    /**
     * @var string $categoryTextColor Discourse forum category text color
     */
    public $categoryTitleColor;

    /**
     * @var string $cid Discourse forum category id
     */
    public $cid;

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
            'invite-member' => ['emails', 'message'],
            'contact-member' => ['id', 'user_id', 'name', 'subject', 'message'],
            'category-new' => ['categoryName', '_categoryDescription', 'categoryBannerColor', 'categoryTitleColor'],
            'category-edit' => ['cid', 'categoryName', 'oldCategoryName', '_categoryDescription', 'categoryBannerColor', 'categoryTitleColor'],
            'send-notice' => ['subject', 'message'],
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

            [['emails'], 'required', 'on' => 'invite-member'],
            [['emails', 'message'], 'string', 'on' => 'invite-member'],

            [['subject', 'message'], 'required', 'on' => 'contact-member'],
            [['subject', 'message'], 'string', 'on' => 'contact-member'],
            [['id', 'user_id', 'name'], 'safe', 'on' => 'contact-member'],

            [['categoryName', '_categoryDescription'], 'required', 'on' => 'category-new'],
            [['_categoryDescription'], 'string', 'length' => [20, 200], 'on' => 'category-new'],
            [['categoryName'], 'string', 'max' => 60, 'on' => 'category-new'],
            [['categoryBannerColor', 'categoryTitleColor'], 'string', 'on' => 'category-new'],
            [['categoryName'], 'validateUniqueCategoryName', 'on' => 'category-new'],

            [['categoryName', '_categoryDescription'], 'required', 'on' => 'category-edit'],
            [['_categoryDescription'], 'string', 'length' => [20, 200], 'on' => 'category-edit'],
            [['categoryName'], 'string', 'max' => 60, 'on' => 'category-edit'],
            [['categoryBannerColor', 'categoryTitleColor'], 'string', 'on' => 'category-edit'],
            [['categoryName'], 'validateUniqueCategoryName', 'on' => 'category-edit'],
            [['cid', 'oldCategoryName'], 'safe', 'on' => 'category-edit'],

            [['subject', 'message'], 'required', 'on' => 'send-notice'],
            [['subject', 'message'], 'string', 'on' => 'send-notice'],
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
            'not_searchable' => 'Exclude from search (Users join by invitation only)',
            'feature_prayer' => '',
            'feature_calendar' => '',
            'feature_notification' => '',
            'feature_document' => '', 
            'feature_chat' => '', 
            'feature_forum' => '', 
            'feature_update' => '', 
            'feature_donation' => '',
            'newUserEmail' => '',
            'categoryName' => 'Category Name',
            '_categoryDescription' => 'Description'
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
     * @return mixed;
     */
    public function handleFormInformation()
    {
        if ($this->status == NULL) {
            $this->user_id = Yii::$app->user->identity->id;
            $this->url_name = Inflector::slug($this->name);
            $this->status = self::STATUS_NEW;
        }

        // Notify admin
        if ($this->isNewRecord) {
            $mail = Subscription::getSubscriptionByEmail(Yii::$app->params['email.admin']);
            $mail->to = Yii::$app->params['email.admin'];
            $mail->subject = 'New Group';
            $mail->title = 'New Group';
            $mail->message = 'Group ' . $group->name . ' was just created by ' . $group->owner->fullName;
            $mail->sendNotification();
        }

        $this->save();

        // Create a new group member for group owner
        if (!$this->owner) {
            $user = Yii::$app->user->identity;
            $groupMember = new GroupMember();
            $groupMember->group_id = $this->id;
            $groupMember->user_id = $user->id;
            $groupMember->group_owner = 1;
            $groupMember->status = GroupMember::STATUS_ACTIVE;
            if ($profile = $user->indActiveProfile) {
                $groupMember->profile_id = $profile->id;
                if ($profile->type == Profile::TYPE_MISSIONARY) {
                    $groupMember->missionary_id = $profile->missionary->id;
                }
            } 
            $groupMember->save();
        }

        return $this;
    }

    /**
     * Send new user notice of profile transfer request
     * @return boolean
     */
    public static function sendGroupTransfer($group, $newUser, $oldUser, $complete=false)
    {   
        $mail = Subscription::getSubscriptionByEmail($newUser->email) ?? new Subscription();
        $mail->headerColor = Subscription::COLOR_GROUP;
        $mail->headerImage = Subscription::IMAGE_GROUP;
        $mail->headerText = $complete ? 'Transfer Complete' : 'Transfer Request';
        $mail->to = $complete ? $oldUser->email : $newUser->email;
        $mail->subject = $complete ? 'IBNet Group Transfer Complete' : 'IBNet Group Transfer Request';
        $mail->title = $complete ? 'Goodbye old friend...' : 'Take Ownership of '. $group->name;
        $mail->message = $complete ? 
            'Your IBNet group ' . $group->name . ' has been successfully transferred to ' . $newUser->fullName . '.' :
            $oldUser->fullName . ' requests that you assume ownership of IBNet group "' . $group->name . '".  Click the link 
            below to complete the transfer and take ownership of this group. This link will remain valid for one week.';
        $resetLink = $complete ? NULL : Yii::$app->urlManager->createAbsoluteUrl(['group/transfer-complete', 'id' => $group->id, 'token' => $group->transfer_token]);
        $mail->link = $complete ? NULL : Html::a(Html::encode($resetLink), $resetLink);
        $mail->sendNotification();

        return true;
    }

    /**
     * Create a new group in the Discourse forum
     * @return boolean;
     */
    public function createForumGroup()
    {
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);

        // Generate unique group name
        $this->discourse_group_name = Utility::generateUniqueRandomString($this, 'discourse_group_name', 20, true);

        // Create group
        $response = $client->post('/admin/groups', [
            'headers' => $this->headers,
            'form_params' => [
                'group[name]' => $this->discourse_group_name,
                'group[full_name]' => $this->name,
            ]
        ]); 

        // Save group id
        $response = $client->get('/groups/' . $this->discourse_group_name . '.json', ['headers' => $this->headers]); 
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $this->discourse_group_id = $decoded->group->id;

        // Set interaction levels 
        $response = $client->put('/groups/' . $this->discourse_group_id, [
            'headers' => $this->headers,
            'form_params' => [
                'group[visibility_level]' => 1,
                'group[grant_trust_level]' => 1,
                // 'group[flair_url]' => Yii::$app->params['url.frontend'] . $this->image ?? '',  
                'group[bio_raw]' => $this->description ?? '',
            ]
        ]);

        // Check if default category exists; if not, add new
        $response = $client->get('/categories.json', ['headers' => $this->headers]); 
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $categories = ArrayHelper::getColumn($decoded->category_list->categories, 'name');
        if (!in_array($this->name, $categories)) {
            // Add default group category
            $response = $client->post('/categories.json', [
                'headers' => $this->headers,
                'form_params' => [
                    'name' => $this->name,
                    'color' => 'green',
                    'text_color' => 'black'
                ]
            ]); 

            // Save default category id
            $response = $client->get('/categories.json', ['headers' => $this->headers]); 
            $json = $response->getBody()->getContents();
            $decoded = json_decode($json);
            $cids = ArrayHelper::getColumn($decoded->category_list->categories, 'id');
            $this->discourse_category_id = end($cids);
        }

        // Set default category permissions (assign to group)
        $response = $client->put('/categories/' . $this->discourse_category_id, [
            'headers' => $this->headers,
            'form_params' => [
                'name' => $this->name,
                'color' => 'green',
                'text_color' => 'black',
                'permissions[' . $this->discourse_group_name . ']' => 1,
            ]
        ]);

        // Create and/or add users to group
        $members = $this->getGroupMembers()->with('user')->all();
        // Get all discourse users
        $response = $client->get('/admin/users/list/active.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json); 
        $discourseUsers = ArrayHelper::getColumn($decoded, 'username');
        // Separate group members into new and current discourse users
        $new = $current = [];
        foreach ($members as $member) {
            in_array($member->user->username, $discourseUsers) ?
                $current[] = $member->user->username :
                $new[] = $member->user;
        }
        // Add existing users to group
        if ($current) {
            $current = implode(',', $current);
            $response = $client->put('/groups/' . $this->discourse_group_id . '/members.json', [
                'headers' => $this->headers,
                'form_params' => ['usernames' => $current]
            ]);
        }
        // Create new users and add to group
        if ($new) {
            foreach ($new as $n) {
                $ssoParams = [
                    'external_id' => $n->id,
                    'email' => $n->email,
                    'username' => $n->username,
                    'add_groups' => $this->discourse_group_name
                ];
                $ssoPayload = base64_encode(http_build_query($ssoParams));
                $sig = hash_hmac('sha256', $ssoPayload, Yii::$app->params['apiKey.discourse-secret']);
                $response = $client->post('/admin/users/sync_sso', [
                    'headers' => $this->headers,
                    'form_params' => [
                        'sso' => $ssoPayload,
                        'sig' => $sig,
                        'api_key' => Yii::$app->params['apiKey.discourse'],
                        'api_username' => Yii::$app->params['apiKey.discourse-username'],
                    ]
                ]); 
            }
        }
        
        return $this;
    }

    /**
     * Remove a group in the Discourse forum
     * @return boolean;
     */
    public function removeForumGroup()
    {   
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
    
        // Check if group exists; if so, remove it
        $response = $client->get('/groups.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $ids = ArrayHelper::getColumn($decoded->groups, 'id');
        if (in_array($this->discourse_group_id, $ids)) {
            $response = $client->delete('/admin/groups/' . $this->discourse_group_id . '.json', ['headers' => $this->headers]);
        }
    }

    /**
     * Get a group parent category in the Discourse forum
     * @return boolean;
     */
    public function getParentCategory()
    {   
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->get('/categories.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        foreach ($decoded->category_list->categories as $cat) {
            if ($cat->id == $this->discourse_category_id) {
                return $cat;
            }
        }
        return false;
    }

    /**
     * Get all group child categories in the Discourse forum
     * @return boolean;
     */
    public function getChildCategories()
    {   
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->get('/site.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $childCategories = [];
        foreach ($decoded->categories as $cat) {
            if (isset($cat->parent_category_id) && ($cat->parent_category_id == $this->discourse_category_id)) {
                $childCategories[] = $cat;
            }
        }

        return $childCategories;
    }

    /**
     * Get a single group child category in the Discourse forum
     * @param  integer $cid Child categegory id
     * @return object;
     */
    public function getChildCategory($cid)
    {
        $categories = $this->childCategories;
        foreach ($categories as $cat) {
            if ($cat->id == $cid) {
                return $cat;
            }
        }
        return false;
    }

    /**
     * Get all category topics
     * return array indexed by category id
     * @return array;
     */
    public function getAllCategoryTopics()
    {   
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->get('/c/' . $this->discourse_category_id . '.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json); 
        $array = $decoded->topic_list->topics;
        // Sort $array by id asc
        usort($array, [$this, 'topic_sort']);
        // reindex array and group by category id
        return ArrayHelper::index($array, NULL, 'category_id');
    }

    /**
     * Custom sort function for usort of category topics
     * @param array $a
     * @param array $b
     * @return array
     */
    private function topic_sort($a,$b) {
       return $a->id > $b->id;
    }

    /**
     * Get a single category topic
     * @param  integer $tid Topic id
     * @return array;
     */
    public function getTopic($tid)
    {   
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->get('/t/' . $tid . '.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        return json_decode($json);
    }

    /**
     * Close or open a topic
     * @param  integer $tid Topic id
     * @param  string $status Whether or not the topic should be closed
     * @return boolean;
     */
    public function closeTopic($tid, $enabled)
    {
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->put('/t/' . $tid . '/status', [
            'headers' => $this->headers,
            'form_params' => [
                'status' => 'closed',
                'enabled' => $enabled,
            ]
        ]);
        return true;
    }

    /**
     * Pin or unpin a topic
     * @param  integer $tid Topic id
     * @param  string $enabled Whether or not the topic should be pinned
     * @return boolean;
     */
    public function pinTopic($tid, $enabled)
    {
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->put('/t/' . $tid . '/status', [
            'headers' => $this->headers,
            'form_params' => [
                'status' => 'pinned',
                'enabled' => $enabled,
                'until' => '3019-07-09' // Forever
            ]
        ]);
        return true;
    }

    /**
     * Remove topic
     * @param  integer $tid Topic id
     * @return boolean;
     */
    public function removeTopic($tid)
    {
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->delete('/t/' . $tid . '.json', ['headers' => $this->headers]);
        return true;
    }

    /**
     * Update a group category in the Discourse forum
     * @return boolean;
     */
    public function updateCategory()
    {
        // Check if name has been updated to already in-use category name
        if (strcmp($this->categoryName, $this->oldCategoryName) != 0) {
            $categories = $this->childCategories;
            $categoryNames = ArrayHelper::getColumn($categories, 'name');
            if (in_array($this->categoryName, $categoryNames)) {
                return false;
            }
        }

        // Update banner and text colors
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->put('/categories/' . $this->cid, [
            'headers' => $this->headers,
            'form_params' => [
                'name' => $this->categoryName,
                'color' => Utility::colorToHex($this->categoryBannerColor, ''),
                'text_color' => Utility::colorToHex($this->categoryTitleColor, ''),
            ]
        ]);

        // Update description
        //      get topic id
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $topicUrl = $decoded->category->topic_url;
        list(,,, $tid) = explode('/', $topicUrl);
        $response = $client->get('/t/-/' . $tid . '.json', ['headers' => $this->headers]);
        //      get first post id in topic
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $pid = $decoded->post_stream->posts[0]->id; 
        $response = $client->put('/posts/' . $pid . '.json', [
            'headers' => $this->headers,
            'form_params' => ['post[raw]' => $this->_categoryDescription]
        ]);
        return true;
    }

    /**
     * Return a group category description in the Discourse forum
     * @return boolean;
     */
    public function getCategoryDescription($categoryUrl)
    {

        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        
        // Get topic
        list(,,, $tid) = explode('/', $categoryUrl);
        $response = $client->get('/t/-/' . $tid . '.json', ['headers' => $this->headers]);
        // Get first post id in topic
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $pid = $decoded->post_stream->posts[0]->id;
        // Get post description
        $response = $client->get('/posts/' . $pid . '.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json); 
        return $decoded->raw;

    }

    /**
     * Add a group category in the Discourse forum
     * @return boolean;
     */
    public function addChildCategory()
    {   
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        
        // Add group category
        $this->categoryBannerColor = $this->categoryBannerColor ?? 'blue';
        $this->categoryTitleColor = $this->categoryTitleColor ?? 'white';
        $response = $client->post('/categories', [
            'headers' => $this->headers,
            'form_params' => [
                'name' => $this->categoryName,
                'color' => Utility::colorToHex($this->categoryBannerColor, ''),
                'text_color' => Utility::colorToHex($this->categoryTitleColor, ''),
                'permissions[' . $this->discourse_group_name . ']' => 1,
                'parent_category_id' => $this->discourse_category_id,
            ]
        ]);

        // Add category description to default topic
        //      get topic id
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $topicUrl = $decoded->category->topic_url;
        list(,,, $tid) = explode('/', $topicUrl);
        $response = $client->get('/t/-/' . $tid . '.json', ['headers' => $this->headers]);
        //      get first post id in topic
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json);
        $pid = $decoded->post_stream->posts[0]->id; 
        $response = $client->put('/posts/' . $pid . '.json', [
            'headers' => $this->headers,
            'form_params' => ['post[raw]' => $this->_categoryDescription]
        ]);
        return true;
    }

    /**
     * Remove a group category in the Discourse forum
     * @return boolean;
     */
    public function removeCategory()
    {   
        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        $response = $client->delete('/categories/' . $this->cid, ['headers' => $this->headers]);
        return true;
    }

    /**
     * Add a new user to the discourse group
     * @return boolean;
     */
    public function addDiscourseUser($uid)
    {
        $user = User::findOne($uid);

        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        
        // Get all discourse users
        $response = $client->get('/admin/users/list/active.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json); 
        $discourseUsers = ArrayHelper::getColumn($decoded, 'username');
        
        // Add user to group
        if (in_array($user->username, $discourseUsers)) {
            $response = $client->put('/groups/' . $this->discourse_group_id . '/members.json', [
                'headers' => $this->headers,
                'form_params' => ['usernames' => $user->username]
            ]);

        // Create new discourse user and add to group
        } else {
            $ssoParams = [
                'external_id' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'add_groups' => $this->discourse_group_name
            ];
            $ssoPayload = base64_encode(http_build_query($ssoParams));
            $sig = hash_hmac('sha256', $ssoPayload, Yii::$app->params['apiKey.discourse-secret']);
            $response = $client->post('/admin/users/sync_sso', [
                'headers' => $this->headers,
                'form_params' => [
                    'sso' => $ssoPayload,
                    'sig' => $sig,
                    'api_key' => Yii::$app->params['apiKey.discourse'],
                    'api_username' => Yii::$app->params['apiKey.discourse-username'],
                ]
            ]); 
        }
    }

    /**
     * Remove a user from the discourse group
     * @return boolean;
     */
    public function removeDiscourseUser($uid)
    {
        $user = User::findOne($uid);

        $client = new Client(['base_uri' => Yii::$app->params['url.forum']]);
        
        // Get all discourse users
        $response = $client->get('/admin/users/list/active.json', ['headers' => $this->headers]);
        $json = $response->getBody()->getContents();
        $decoded = json_decode($json); 
        $discourseUsers = ArrayHelper::getColumn($decoded, 'username');
        
        // Remove from group if current discourse user
        if (in_array($user->username, $discourseUsers)) {
            $response = $client->delete('/groups/' . $this->discourse_group_id . '/members.json', [
                'headers' => $this->headers,
                'form_params' => ['username' => $user->username]
            ]);
        }
    }

    /**
     * Send group invitation emails
     * @return \yii\db\ActiveQuery
     */
    public function sendInvites()
    {
        $user = Yii::$app->user->identity;
        $emails = explode(',', $this->emails);
        $subscriptions = Subscription::find()->all();
        $subscriptions = ArrayHelper::getColumn($subscriptions, 'email');
        $unsubscribed = Subscription::getAllUnsubscribed();
        $validator = new yii\validators\EmailValidator();
        foreach ($emails as $i => $email) {
            $email = trim($email);
            
            // Add to subscriptions
            if (!in_array($email, $subscriptions)) {
                $sub = new Subscription();
                $sub->add($email);
            }
            
            // Add to invite list
            if ($validator->validate($email) && !in_array($email, $unsubscribed)) { 
                $invite = new GroupInvite;
                $invite->add($email, $this->id);
                $mails[$i][0] = $email;
                $mails[$i][1] = $invite->token;
                $mails[$i][2] = Subscription::getToken($email);

            // Not a valid email or unsubscribed
            } else {
                $failed[] = $email;
            }
        }

        // Send emails
        $messages = [];
        foreach ($mails as $mail) {
            $messages[] = 
                Yii::$app->mailer->compose(
                    ['html' => 'group/invite-html', 'text' => 'group/invite-text'], 
                    [
                        'group' => $this, 
                        'user' => $user, 
                        'token' => $mail[1], 
                        'extMessage' => $this->message ?? NULL,
                        'email' => $mail[0],
                        'unsubTok' => $mail[2],
                    ]
                )
                ->setFrom([Yii::$app->params['email.noReply'] => $this->name])
                ->setTo([$mail[0] => $user->fullName])
                ->setSubject('Invitation to join IBNet Group "' . $this->name . '"');
        }
        Yii::$app->mailer->sendMultiple($messages);

        return $failed ?? true;
            
    }

    /**
     * Notification feature: Send a notification email to all group members
     * @return \yii\db\ActiveQuery
     */
    public function sendNotification()
    {
        // Save notification
        $notification = new GroupNotification();
        $notification->group_id = $this->id;
        $notification->user_id = Yii::$app->user->identity->id;
        $notification->subject = $this->subject;
        $notification->message = $this->message;
        $notification->save();      

        // Assemble messages
        $members = $this->memberUsers;
        $messages = [];
        foreach ($members as $member) {
            $notification->toEmail = $member->email;
            $notification->toName = $member->fullName;
            if ($return = $notification->prepareNotification()) {
                $messages[] = $return;
            }
        }
        // Send
        Yii::$app->mailer->sendMultiple($messages);      

        return true;
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
     * Discourse api header params
     * @return boolean
     */
    public function getHeaders()
    {
        return [
            'Api-Key' => Yii::$app->params['apiKey.discourse'],
            'Api-Username' => Yii::$app->params['apiKey.discourse-username'],
        ];
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
    public function getActivePrayerGroups()
    {
        return static::find()->where(['status' => self::STATUS_ACTIVE])->andWhere(['feature_prayer' => 1])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveUpdateGroups()
    {
        return static::find()->where(['status' => self::STATUS_ACTIVE])->andWhere(['feature_update' => 1])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveNotificationGroups()
    {
        return static::find()->where(['status' => self::STATUS_ACTIVE])->andWhere(['feature_notification' => 1])->all();
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
        return $this->hasOne(GroupMember::className(), ['group_id' => 'id'])->where(['user_id' => Yii::$app->user->identity->id]);
    }

    /**
     * Group pending member of current user
     * @return \yii\db\ActiveQuery
     */
    public function getPendingGroupMember()
    {
        return $this->hasOne(GroupMember::className(), ['group_id' => 'id'])->where(['user_id' => Yii::$app->user->identity->id])->andWhere(['group_member.status' => GroupMember::STATUS_PENDING]);
    }

    /**
     * All current group members
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMembers()
    {
        return $this->hasMany(GroupMember::className(), ['group_id' => 'id'])->where(['group_member.status' => GroupMember::STATUS_ACTIVE]);
    }

    /**
     * User models for all current group members
     * @return \yii\db\ActiveQuery
     */
    public function getMemberUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->where(['user.status' => User::STATUS_ACTIVE])
            ->via('groupMembers');
    }

    /**
     * User model of group owner
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * User model of group owner
     * @return \yii\db\ActiveQuery
     */
    public function getOwnerMember()
    {
        return $this->hasOne(GroupMember::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMemberId()
    {
        return GroupMember::find()->where(['group_id' => $this->id, 'user_id' => Yii::$app->user->identity->id])->one()->id;
    }

    /**
     * All current group members with updates active
     * @return \yii\db\ActiveQuery
     */
    public function getMembersUpdatesActive()
    { 
        return $this->hasMany(GroupMember::className(), ['group_id' => 'id'])->where(['and', ['group_member.status' => GroupMember::STATUS_ACTIVE], ['show_updates' => 1]]);
    }

    /**
     * Pending member approvals
     * @return \yii\db\ActiveQuery
     */
    public function getPendingMembers()
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
            ->via('membersUpdatesActive');
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
        $prayerList = Prayer::find()->with(['groupUser'])->where(['prayer.group_id' => $this->id, 'prayer.answered' => 0, 'prayer.deleted' => 0])->all();
        $nameList = [];
        foreach ($prayerList as $prayer) {
            array_push($nameList, $prayer->fullName);
        }
        return array_unique($nameList);
    }

    /**
     * All current group members receiving immediate prayer alerts
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerAlertMembers()
    {
        return $this->hasMany(GroupMember::className(), ['group_id' => 'id'])
            ->joinWith('user')
            ->where(['group_member.status' => GroupMember::STATUS_ACTIVE])
            ->andWhere(['group_member.email_prayer_alert' => 1]);
    }

    /**
     * All current group members receiving immediate missionary update alerts
     * @return \yii\db\ActiveQuery
     */
    public function getUpdateAlertMembers()
    {
        return $this->hasMany(GroupMember::className(), ['group_id' => 'id'])
            ->joinWith('user')
            ->where(['group_member.status' => GroupMember::STATUS_ACTIVE])
            ->andWhere(['group_member.email_update_alert' => 1]);
    }

    /**
     * New prayers that haven't been alerted
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerImmediateAlertQueue()
    {
        return $this->hasMany(GroupAlertQueue::className(), ['group_id' => 'id'])->where(['<>', 'prayer_id', 0])->andWhere(['alerted' => 0]);
    }

    /**
     * Prayers that haven't been alerted on the weekly list
     * Only pull requests that have been alerted to avoid sending out and deleting brand new alerts
     * @return \yii\db\ActiveQuery
     */
    public function getPrayerWeeklyAlertQueue()
    {
        return $this->hasMany(GroupAlertQueue::className(), ['group_id' => 'id'])->where(['<>', 'prayer_id', 0])->andWhere(['<>', 'alerted', 0])
            ->with('prayerWithUpdates');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdateListNames()
    {
        $updateList = MissionaryUpdate::find()->joinWith('groupMemberSharingUpdates')->where(['group_id' => $this->id])->all();
        $nameList = [];
        foreach ($updateList as $update) {
            array_push($nameList, $update->realName);
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

    /**
     * Ensure unique category name
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUniqueCategoryName($attribute, $params)
    {
        $categories = $this->childCategories;
        $categoryNames = ArrayHelper::getColumn($categories, 'name');
        if (in_array($this->$attribute, $categoryNames)) {
            $this->addError($attribute, 'This category name already exists.');
            return false;
        }
        return $this->$attribute;
    }
}
