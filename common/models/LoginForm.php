<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    /**
     *
     * @var string $loginId username or email to compare against user model.
     */
    public $loginId;

    /**
     *
     * @var string $password the password to compare against user model.2
     */
    public $password;

    /**
     *
     * @var boolean $rememberMe stores user selection at login.
     * Default is true
     */
    public $rememberMe = true;

    /**
     *
     * @var string $_user current user model.
     * Default is id
     */
    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loginId', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loginId' => 'Username or Email',
            'password' => 'Password',
            'rememberMe' => 'RememberMe',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user && $user->status == User::STATUS_BANNED) {
                $this->addError($attribute, 'Your account has been banned.');
            } else if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username/email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username or email and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->user, $this->rememberMe ? Yii::$app->params['user.rememberMeDuration'] : 0);
        }
          
        return false;
    }

    /**
     * Finds user by username or email
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {

            filter_var($this->loginId, FILTER_VALIDATE_EMAIL) ?
                $this->_user = User::findByEmail($this->loginId) :
                $this->_user = User::findByUsername($this->loginId);
        }

        return $this->_user;
    }
}
