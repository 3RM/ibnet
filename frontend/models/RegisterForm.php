<?php
namespace frontend\models;

use common\models\User;
use yii;
use yii\base\Model;

/**
 * Register form
 */
class RegisterForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $username;
    public $password;
    public $password_repeat;                    // not used; hidden form used to prevent bots from registering
    public $verifyCode;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            ['first_name', 'trim'],
            ['first_name', 'required'],
            ['first_name', 'string', 'max' => 20],

            ['last_name', 'trim'],
            ['last_name', 'required'],
            ['last_name', 'string', 'max' => 40],

            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 4, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address is already in use.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'safe'],

            ['verifyCode', 'required'],
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * Registers a user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function register()
    {
        if (!$this->validate() || $this->password_repeat != NULL) {                                 // password_repeat is a hidden field used to detect bots
            return null;
        }
        
        $user = new User();
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->new_email = $this->email;
        $user->new_email_token = Yii::$app->security->generateRandomString() . '_' . time();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->save(false);

        $auth = Yii::$app->authManager;
        $userRole = $auth->getRole('User');
        $auth->assign($userRole, $user->getId());

        return $user;
    }
}
