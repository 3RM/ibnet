<?php
namespace common\models;

use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use sadovojav\cutter\behaviors\CutterBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * User model
 *
 * @property integer $id
 * @property string $usr_image
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements 
    \yii\web\IdentityInterface,
    \rmrevin\yii\module\Comments\interfaces\CommentatorInterface
{
    public $newUsername;
    public $newEmail;
    public $currentPassword;
    public $newPassword;
    public $emailMaintenance = 1;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


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
            'all' => ['status'],
            'personal' => ['screen_name', 'home_church', 'role', 'usr_image'],
            'account' => ['newUsername', 'newEmail', 'newPassword', 'emailPrefProfile', 'emailPrefLinks', 'emailPrefComments',   'emailPrefFeatures'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE, 'on' => 'all'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED], 'on' => 'all'],

            ['usr_image', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'personal'],
            [['screen_name', 'home_church', 'role'], 'default', 'value' => NULL,'on' => 'personal'],
            ['screen_name', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This name is already in use.', 'on' => 'personal'],
            ['screen_name', 'string', 'max' => 60, 'on' => 'personal'],
            ['screen_name', 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'personal'],

            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'on' => 'account'],
            ['username', 'string', 'min' => 4, 'max' => 255, 'on' => 'account'],
            ['newEmail', 'email', 'message' => 'Please provide a valid email address.', 'on' => 'account'],
            ['newPassword', 'string', 'max' => 20, 'on' => 'account'],
            ['currentPassword', 'validateCurrentPass', 'on' => 'account'],
            [['emailPrefProfile', 'emailPrefLinks', 'emailPrefComments', 'emailPrefFeatures'], 'safe', 'on' => 'account'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'usr_image' => '',
            'screen_name' => 'Screen Name',
            'home_church' => 'Home Church',
            'role' => 'Primary Role',
            'newUsername' => '',
            'newEmail' => '',
            'currentPassword' => '',
            'newPassword' => '',
            'emailMaintenance' => 'Email me regarding my account maintenance',
            'emailPrefProfile' => 'Keep me updated on visitor stats for my profile pages',
            'emailPrefLinks' => 'Tell me when someone links to or unlinks from my profiles',
            'emailPrefComments' => 'Tell me when someone comments on my profiles',
            'emailPrefFeatures' => 'Notify me of new or updated website features',
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
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
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
       return static::findOne([
            'new_email_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if new email token is valid
     *
     * @param string $token new email token
     * @return boolean
     */
    public static function isNewEmailTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.newEmailTokenExpire'];
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
                Mail::sendEmailConfLink($this->email, $this->newEmail, $this->new_email_token);
            } 
            if ($this->newPassword != NULL) {
                $this->updateAttributes(['password_hash' => 
                        Yii::$app->security->generatePasswordHash($this->newPassword)]);
                Mail::sendNewPwd($this->email);
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
     * @return string|false
     */
    public function getCommentatorAvatar()
    {
        return isset($this->usr_image) ? '@web' . $this->usr_image : '@web/images/user.png';
    }

    /**
     * @return string
     */
    public function getCommentatorName()
    {
        return $this->screen_name;
    }

    /**
     * @return string|false
     */
    public function getCommentatorUrl()
    {
        if ($profile = Profile::find()
            ->where(['user_id' => $this->id])
            ->andWhere(['status' => Profile::STATUS_ACTIVE])
            ->andWhere(['category' => Profile::CATEGORY_IND])
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
        $church = ProfileController::findActiveProfile($this->home_church);
        return Html::a($church->org_name, ['/profile/church',
            'urlLoc' => $church->url_loc, 
            'name' => $church->url_name, 
            'id' => $church->id]);
    }
}