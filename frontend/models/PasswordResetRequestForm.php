<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Subscription;
use common\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this email.'
            ],
        ];
    }

    /**
     * Sends an email with a link for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            $user->scenario = 'passwordReset';
            if (!$user->save()) {
                return false;
            }
        }

        $mail = $user->subscription ?? new Subscription();
        $mail->to = $user->email;
        $mail->subject = 'Password Reset for IBNet.org';
        $mail->message = 'Follow the link below to reset your password:';
        $mail->link = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
        return $mail->sendNotification(NULL, TRUE);

    }
}
