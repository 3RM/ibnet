<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */
 
namespace common\models;

use backend\models\Assignment;
use common\models\group\Group;
use common\models\group\GroupMember;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use sadovojav\cutter\behaviors\CutterBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

use common\models\Utility;

/**
 * User model
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $new_email
 * @property string $new_email_token
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $created_at
 * @property int $updated_at
 * @property string $last_login
 * @property int $status
 * @property string $usr_image
 * @property string $display_name
 * @property int $home_church
 * @property string $primary_role
 * @property int $emailPrefProfile
 * @property int $emailPrefLinks
 * @property int $emailPrefComments
 * @property int $emailPrefFeatures
 * @property int $emailPrefBlog
 * @property int $reviewed
 */
class User extends ActiveRecord implements 
    \yii\web\IdentityInterface,
    \rmrevin\yii\module\Comments\interfaces\CommentatorInterface
{


    /**
     * @var string $newUsername New username
     */
    public $newUsername;

    /**
     * @var string $newEmail New email
     */
    public $newEmail;

    /**
     * @var string $currentPassword Current password
     */
    public $currentPassword;

    /**
     * @var string $newPassword New password
     */
    public $newPassword;

    /**
     * @var string $emailMaintenance Read-only checkbox for email preference
     */
    public $emailMaintenance = 1;

    /**
     * @const int ROLE_* User assignments
     */
    const ROLE_ADMIN = 'Admin';
    const ROLE_SAFEUSER = 'SafeUser';
    const ROLE_USER = 'User';

    /**
     * @const int STATUS_* User account status
     */
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_BANNED = 20;

    /**
     * @const int PRIMARYROLE_* User selected primary role
     */
    const PRIMARYROLE_CHURCHMEMBER = 'Church Member'; 
    const PRIMARYROLE_ASSOCIATEPASTOR = 'Associate Pastor';
    const PRIMARYROLE_ASSISTANTPASTOR = 'Assistant Pastor';
    const PRIMARYROLE_MUSICPASTOR = 'Music Pastor';
    const PRIMARYROLE_PASTOR = 'Pastor';
    const PRIMARYROLE_PASTOREMERITUS = 'Pastor Emeritus';
    const PRIMARYROLE_SENIORPASTOR = 'Senior Pastor';
    const PRIMARYROLE_YOUTHPASTOR = 'Youth Pastor';
    const PRIMARYROLE_ELDER = 'Elder';
    const PRIMARYROLE_CHURCHPLANTER = 'Church Planter';
    const PRIMARYROLE_BIBLETRANSLATOR = 'Bible Translator';
    const PRIMARYROLE_MEDICALMISSIONARY = 'Medical Missionary';
    const PRIMARYROLE_EVANGELIST = 'Evangelist';
    const PRIMARYROLE_CHAPLAIN = 'Chaplain';
    const PRIMARYROLE_CHURCHSTAFF = 'Church Staff';
    const PRIMARYROLE_MINISTRYSTAFF = 'Ministry Staff';

    /**
     * @const int IS_MISSIONARY Indicates if user has missionary profile
     */
    const IS_MISSIONARY = 1;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'usr_image' => [
                'class' => CutterBehavior::className(),
                'attributes' => ['usr_image'],
                'baseDir' => '/uploads/image',
                'basePath' => '@webroot',
            ],
        ];
    }

    public function scenarios() {
        return[
            'passwordReset' => ['password_reset_token'],
            'personal' => ['display_name', 'home_church', 'primary_role', 'usr_image'],
            'account' => ['newUsername', 'newEmail', 'newPassword', 'emailPrefProfile', 'emailPrefLinks', 'emailPrefComments',   'emailPrefFeatures', 'emailPrefBlog'],
            'emailPref' => ['emailPrefProfile', 'emailPrefLinks', 'emailPrefComments',   'emailPrefFeatures', 'emailPrefBlog'],
            'backend' => ['first_name', 'last_name', 'email', 'new_email', 'new_email_token', 'username', 'auth_key', 'password_hash', 'password_reset_token', 'created_at', 'updated_at', 'last_login', 'status', 'display_name', 'home_church', 'primary_role', 'email_pref_links', 'emailPrefComments', 'emailPrefFeatures', 'emailPrefBlog', 'reviewed'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password_reset_token', 'safe', 'on' => 'passwordReset'],

            ['usr_image', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'personal'],
            [['display_name', 'home_church', 'primary_role'], 'default', 'value' => NULL,'on' => 'personal'],
            ['display_name', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This name is already in use.', 'on' => 'personal'],
            ['display_name', 'string', 'max' => 60, 'on' => 'personal'],
            ['display_name', 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'personal'],

            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'on' => 'account'],
            ['username', 'string', 'min' => 4, 'max' => 255, 'on' => 'account'],
            ['newEmail', 'email', 'message' => 'Please provide a valid email address.', 'on' => 'account'],
            ['newPassword', 'string', 'max' => 20, 'on' => 'account'],
            ['currentPassword', 'validateCurrentPass', 'on' => 'account'],
            [['emailPrefProfile', 'emailPrefLinks', 'emailPrefComments', 'emailPrefFeatures', 'emailPrefBlog'], 'safe', 'on' => 'account'],
            [['emailPrefProfile', 'emailPrefLinks', 'emailPrefComments', 'emailPrefFeatures', 'emailPrefBlog'], 'safe', 'on' => 'emailPref'],

            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'on' => 'backend'],
            ['username', 'string', 'min' => 4, 'max' => 255, 'on' => 'backend'],
            ['newEmail', 'email', 'message' => 'Please provide a valid email address.', 'on' => 'backend'],
            ['newPassword', 'string', 'max' => 20, 'on' => 'backend'],
            [['first_name', 'last_name', 'email', 'new_email_token', 'auth_key', 'password_hash', 'password_reset_token', 'created_at', 'updated_at', 'last_login', 'status', 'display_name', 'home_church', 'primary_role', 'emailPrefProfile', 'emailPrefLinks', 'emailPrefComments', 'emailPrefFeatures', 'reviewed'], 'safe', 'on' => 'backend'],
        
            [['fullName'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'usr_image' => '',
            'display_name' => 'display Name',
            'home_church' => 'Home Church',
            'primary_role' => 'Primary Role',
            'newUsername' => '',
            'newEmail' => '',
            'currentPassword' => '',
            'newPassword' => '',
            'emailMaintenance' => 'Email me regarding my account maintenance',
            'emailPrefProfile' => 'Keep me updated on visitor stats for my profile pages',
            'emailPrefLinks' => 'Tell me when someone links to or unlinks from my profiles',
            'emailPrefComments' => 'Tell me when someone comments on my profiles',
            'emailPrefFeatures' => 'Notify me of new or updated website features',
            'emailPrefBlog' => 'Send me weekly blog digests',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['tokenExpire.passwordReset'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignment()
    {
        return $this->hasOne(Assignment::className(), ['user_id' => 'id']);
    }

    /**
     * Generates new email confirmation token
     */
    public function generateNewEmailToken()
    {
        return $this->updateAttributes(['new_email_token' => Yii::$app->security->generateRandomString() . '_' . time()]);
    }

    /**
     * Removes new email confirmation token
     */
    public function removeNewEmailToken()
    {
        $this->updateAttributes(['new_email_token' => null]);
    }

    /**
     * Finds user by new email confirmation token
     *
     * @param string $token new email confirmation token
     * @return static|null
     */
    public static function findByNewEmailToken($token)
    {
       return static::findOne(['new_email_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds out if new email token is valid
     *
     * @param string $token new email token
     * @return boolean
     */
    public function isNewEmailTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['tokenExpire.newEmailTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Validate current password on mySettings form
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateCurrentPass($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        if (!$user || !$user->validatePassword($this->$attribute, $user->password_hash)) { 
            $this->addError($attribute, 'The password you entered is incorrect.');
            $this->toggle = 'visible';
        }
        return $this;
    }

    /**
     * handleAccount
     * Process updates to account settings
     * @return mixed
     */
    public function handleAccount()
    {
        if ($this->validate()) {
            if ($this->newUsername) {
                $this->username = $this->newUsername;
            }
            if ($this->newEmail != NULL) {
                $this->generateNewEmailToken();
                $this->new_email = $this->newEmail;
                
                // Send confirm email to new address
                $link =  Yii::$app->urlManager->createAbsoluteUrl(['site/email-confirmed', 'token' => $new_email_token]);
                Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'site/notification-html', 'text' => 'notification-text'], 
                        [
                            'title' => 'Confirm Your Email Address', 
                            'message' => 'Follow this link to confirm your new email address: ' . $link
                        ])
                    ->setFrom([\yii::$app->params['email.admin']])
                    ->setTo([$new_email])
                    ->setSubject(Yii::$app->params['email.systemSubject'])
                    ->send();

                // Send alert message to old email address
                $title = 'Your Account Has Changed';
                $msg = 'We recieved a request to update your account email.  If you did not
                    request this change, please contact us at <a href="mailto:admin@ibnet.org">admin@ibnet.org</a>.';
                Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'site/notification-html'], 
                        ['
                            title' => 'Your Account Has Changed', 
                            'message' => 'We recieved a request to update your account email.  If you did not request this 
                                          change, please contact us at ' .  Yii::$app->params['email.admin']
                        ])
                    ->setFrom([\yii::$app->params['email.admin']])
                    ->setTo([$email])
                    ->setSubject(Yii::$app->params['email.systemSubject'])
                    ->send();
            } 
            if ($this->newPassword != NULL) {
                $this->updateAttributes(['password_hash' => 
                        Yii::$app->security->generatePasswordHash($this->newPassword)]);

            // Send alert message of changed password
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'site/notification-html'], 
                    [
                        'title' => 'Your Account Has Changed', 
                        'message' => 'Your password has been changed.  If you did not change your password, or if you feel 
                                      that this message is in error, please contact us at ' . Yii::$app->params['email.admin'],     
                    ])
                ->setFrom([Yii::$app->params['email.admin']])
                ->setTo([$email])
                ->setSubject(Yii::$app->params['email.systemSubject'])
                ->send();

                $this->newPassword = '';
                $this->currentPassword = '';
            }
            $this->newEmail == NULL ?
                Yii::$app->session->setFlash('success', 'Your settings have been updated.') :
                Yii::$app->session->setFlash('success', 'Your settings have been updated.  An email with a confirmation link has been sent to your new email address.');

            return true;
        }
        return false;
    }

    /**
     * Profiles owned by the current user
     * @return array (of objects)
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['user_id' => 'id'])
            ->where(['!=', 'status', Profile::STATUS_TRASH])
            ->orderBy('id ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIndActiveProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id'])
            ->where(['category' => Profile::CATEGORY_IND, 'status' => Profile::STATUS_ACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHasIndActiveProfile()
    {
        return Profile::find()
            ->where(['user_id' => $this->id, 'category' => Profile::CATEGORY_IND, 'status' => Profile::STATUS_ACTIVE])
            ->exists();
    }

    /**
     * @return string|false
     */
    public function getIsMissionary()
    {
        return Profile::find()->where(['user_id' => $this->id, 'type' => Profile::TYPE_MISSIONARY])->exists();
    }

    /**
     * @return string|false
     */
    public function getCommentatorAvatar()
    {
        return isset($this->usr_image) ? '@web' . $this->usr_image : '@img.site/user.png';
    }

    /**
     * @return string
     */
    public function getCommentatorName()
    {
        return $this->display_name;
    }

    /**
     * @return string|false
     */
    public function getCommentatorUrl()
    {
        if ($profile = Profile::find()
            ->where([
                'user_id' => $this->id, 
                'status' => Profile::STATUS_ACTIVE, 
                'category' => Profile::CATEGORY_IND
            ])
            ->one()) {
                return Url::to(['profile/' . ProfileController::$profilePageArray[$profile->type], 'urlLoc' => $profile->url_loc, 'name' => $profile->url_name, 'id' => $profile->id], 'https');
        }
        
        return false;
    }

    /**
     * @return string
     */
    public function getCommentatorChurch()
    {
        $church = Profile::findActiveProfile($this->home_church);
        return Html::a($church->org_name, ['/profile/church',
            'urlLoc' => $church->url_loc, 
            'name' => $church->url_name, 
            'id' => $church->id]);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->display_name ? $this->display_name : $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return string|false
     */
    public function getAvatar()
    {
        return isset($this->usr_image) ? '@web' . $this->usr_image : '@img.site/user.png';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMembers()
    {
        return $this->hasMany(GroupMember::className(), ['user_id' => 'id']);
    } 

    /**
     * @var int $ids Own group Ids to exclude from query
     * @return \yii\db\ActiveQuery
     */
    public function getJoinedGroups($ids=NULL)
    { 
        if ($ids) {
            return $this->hasMany(Group::className(), ['id' => 'group_id'])
                ->via('groupMembers')
                ->where('`group`.`id` NOT IN (' . implode(',', array_map('intval', $ids)) . ')')
                ->andWhere('`group`.`status`=' . Group::STATUS_ACTIVE . ' OR `group`.`status`=' . Group::STATUS_INACTIVE);
        } else {
            return $this->hasMany(Group::className(), ['id' => 'group_id'])
                ->via('groupMembers')
                ->andWhere('`group`.`status`=' . Group::STATUS_ACTIVE . ' OR `group`.`status`=' . Group::STATUS_INACTIVE);
        }
    }

    /**
     * @var int $ids Own group Ids to exclude from query
     * @return \yii\db\ActiveQuery
     */
    public function getActiveJoinedGroups()
    { 
        return $this->hasMany(Group::className(), ['id' => 'group_id'])
            ->via('groupMembers')
            ->andWhere('`group`.`status`=' . Group::STATUS_ACTIVE);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwnGroups()
    {
        return $this->hasMany(Group::className(), ['user_id' => 'id'])->where(['<>', 'status', Group::STATUS_TRASH]);
    }

}