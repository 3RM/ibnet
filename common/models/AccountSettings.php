<?php
namespace common\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Account Settings
 */
class AccountSettings extends Model
{
    public $toggle;
    public $email;
    public $newEmail;
    public $currentUsername; 
    public $username;
    public $currentPassword;
    public $newPassword;
    public $emailMaintenance;       // Read-only required checkbox
    public $emailPrefProfile;
    public $emailPrefLinks;
    public $emailPrefFeatures;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'on' => 'update'],
            ['username', 'string', 'min' => 4, 'max' => 255, 'on' => 'update'],
            ['newEmail', 'email', 'message' => 'Please provide a valid email address.', 'on' => 'update'],
            ['newPassword', 'string', 'max' => 20, 'on' => 'update'],
            ['currentPassword', 'validateCurrentPass', 'on' => 'update'],
            [['emailPrefProfile', 'emailPrefLinks', 'emailPrefFeatures'], 'safe', 'on' => 'update'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'emailMaintenance' => 'Email me regarding my account maintenance',
            'emailPrefProfile' => 'Keep me updated on visitor stats for my profile pages',
            'emailPrefLinks' => 'Tell me when someone links to or unlinks from my profiles',
            'emailPrefFeatures' => 'Notify me of new or updated website features',
        ];
    }

    /**
     * Handle update account form data 
     * 
     * @return mixed
     */
    public function updateAccount($user)
    {
        
        if (isset($_POST['updateUsername']) && $this->validate()) {                                 // Update username
            $user->username = $this->username;
            if ($user->save()) {

                $message = 'Your IBNet username has been changed.  If you did not change your username,   
                    or if you feel that this message is in error, please contact us at     
                    <a href="mailto:admin@ibnet.org">admin@ibnet.org</a>.';
                $this->sendNotificationEmail(Yii::$app->params['email.systemTitle'], $message);
                                
                Yii::$app->session->setFlash('success', 'Your username has been updated.');
                return $this;
            }


        } elseif (isset($_POST['updateEmail']) && $this->validate()) {                              // Update email
            $user->new_email = $this->newEmail;
            $user->generateNewEmailToken();
            if ($user->save()) {
                
                $link =  Yii::$app->urlManager->createAbsoluteUrl([
                        'site/email-confirmed', 'token' => $user->new_email_token]);
                $message = 'Follow this link to confirm your new email address: ' . $link;
                Yii::$app->mailer                                                                   // Send notification to new email
                ->compose(
                    ['html' => 'site/notification-html'],
                    ['title' => Yii::$app->params['email.systemTitle'], 'message' => $message]
                )
                ->setFrom(Yii::$app->params['email.noReply'])
                ->setTo($user->new_email)
                ->setSubject(Yii::$app->params['email.systemSubject'])
                ->send();

                $message = 'We recieved a request to update your account email.  If you did not
                    request this change, please contact us right away at at     
                    <a href="mailto:admin@ibnet.org">admin@ibnet.org</a>..';
                Yii::$app->mailer                                                                   // send notification to old email
                ->compose(
                    ['html' => 'site/notification-html'],
                    ['title' => Yii::$app->params['email.systemTitle'], 'message' => $message]
                )
                ->setFrom(Yii::$app->params['email.noReply'])
                ->setTo($user->email)
                ->setSubject(Yii::$app->params['email.systemSubject'])
                ->send();
                
                Yii::$app->session->setFlash('success', 'An email with a confirmation link has been sent 
                    to your new email address.');
                return $this;
            }
        
        } elseif (isset ($_POST['updatePass']) && $this->validate()) {                              // Update password
            if ($user->updateAttributes(['password_hash' => 
                    Yii::$app->security->generatePasswordHash($this->newPassword)])) {
                $this->newPassword = '';
                $this->currentPassword = '';
                
                $message = 'Your password has been changed.  If you did not change your password,   
                    or if you feel that this message is in error, please contact us at     
                    <a href="mailto:admin@ibnet.org">admin@ibnet.org</a>.';
                $this->sendNotificationEmail(Yii::$app->params['email.systemTitle'], $message);
                $this->toggle = 'none';
                
                Yii::$app->session->setFlash('success', 'Your password has been updated.');
                return $this;
            }

        } elseif (isset ($_POST['updatePreferences']) && $this->validate()) {                       // Update password
            if ($user->updateAttributes([
                    'emailPrefProfile' => $this->emailPrefProfile,
                    'emailPrefLinks' => $this->emailPrefLinks,
                    'emailPrefFeatures' => $this->emailPrefFeatures,
                ])) {
     
                $message = 'Your email preferences have been changed.  If you did not change your email preferences,   
                    or if you feel that this message is in error, please contact us at 
                    <a href="mailto:admin@ibnet.org">admin@ibnet.org</a>.';
                $this->sendNotificationEmail(Yii::$app->params['email.systemTitle'], $message);
                
                Yii::$app->session->setFlash('success', 'Your email preferences have been updated.');
                return $this;
            }
        }
        return NULL;
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
     * Sends an email with title and system notification 
     * message (e.g. password change confirmation).
     *
     * @return boolean whether the email was send
     */
    public function sendNotificationEmail($title, $message)
    {
        if ($user = Yii::$app->user->identity) {
            return Yii::$app->mailer
                ->compose(
                    ['html' => 'site/notification-html'],
                    ['title' => $title, 'message' => $message]
                )
                ->setFrom(Yii::$app->params['email.noReply'])
                ->setTo($user->email)
                ->setSubject(Yii::$app->params['email.systemSubject'])
                ->send();
        }
        return NULL;
    }
}