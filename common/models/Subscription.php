<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "subscription".
 *
 * @property int $id
 * @property string $email
 * @property int $unsubscribe
 * @property int $profile
 * @property int $links
 * @property int $comments
 * @property int $features
 * @property int $blog
 */
class Subscription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscription';
    }

    /**
     * @var string $from From address
     */
    public $fromEmail = 'no-reply@ibnet.org';

    /**
     * @var string $from From address
     */
    public $fromName = 'IBNet';

    /**
     * @var string $to To address
     */
    public $to;

    /**
     * @var string $cc Carbon copy
     */
    public $cc = NULL;

    /**
     * @var string $subject Message subject
     */
    public $subject = 'Notification from IBNet';

    /**
     * @var string $headerColor Header background color
     */
    public $headerColor = '#7a7467';

    /**
     * @var string $image Header image
     */
    public $headerImage = 'https://ibnet.org/images/mail/ibnet-large.png';

    /**
     * @var string $header Header text
     */
    public $headerText = NULL;

    /**
     * @var string $title Message title
     */
    public $title;

    /**
     * @var string $message Message content
     */
    public $message;

    /**
     * @var string $extMessage Extended message content (content provided by user)
     */
    public $extMessage = NULL;

    /**
     * @var string $link Option link below message
     */
    public $link = NULL;

    /**
     * @const string $Color_* Header background color
     */
    const COLOR_PROFILE = '#3b007b';
    const COLOR_MISSIONARY = '#006b6f';
    const COLOR_GROUP = '#003169';

    /**
     * @const string $IMAGE_* Header image
     */
    const IMAGE_PROFILE = 'https://ibnet.org/images/mail/profile.png';
    const IMAGE_PROFILE_COMMENT = 'https://ibnet.org/images/mail/comment.png';
    const IMAGE_MISSIONARY = 'https://ibnet.org/images/mail/missionary.png';
    const IMAGE_MAILCHIMP = 'https://ibnet.org/images/mail/mailchimp.png';
    const IMAGE_GROUP = 'https://ibnet.org/images/mail/cluster.png';

    /**
     * @const string $TEXT_* Header text
     */
    const TEXT_LINK = 'Profile Link';


    public function scenarios() {
        return[
            'add' => ['email', 'token'],
            'unsubscribe' => ['unsubscribe', 'profile', 'links', 'comments', 'features', 'blog'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'token'], 'required', 'on' => 'add'],
            ['email', 'email', 'on' => 'add'],

            [['unsubscribe', 'profile', 'links', 'comments', 'features', 'blog'], 'safe', 'on' => 'unsubscribe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'token' => 'Token',
            'unsubscribe' => 'Unsubscribe from all IBNet communications',
            'profile' => 'Keep me updated on visitor stats for my profile pages',
            'links' => 'Tell me when someone links to or unlinks from my profiles',
            'comments' => 'Tell me when someone comments on my profiles',
            'features' => 'Notify me of new or updated website features',
            'blog' => 'Send me weekly blog digests',
        ];
    }

    /**
     * Add email to subscription list
     * @return \yii\db\ActiveQuery
     */
    public function add($email=NULL)
    {
        $this->scenario = 'add';
        $this->email = $email ?? $this->to;
        if (self::find()->where(['email' => $this->email])->exists()) {
            return false;
        }
        $this->token = Yii::$app->security->generateRandomString(32);
        $this->save();
        return true;
    }

    /**
     * Get subscription by email
     * @param  $email subscriber email
     * @return \yii\db\ActiveQuery
     */
    public static function getSubscriptionByEmail($email)
    {
        return self::find()->where(['email' => $email])->One();
    }

    /**
     * Add email
     * @return \yii\db\ActiveQuery
     */
    public function unsubscribe()
    {
        if ($this->unsubscribe) {
            $this->profile = 0;
            $this->links = 0;
            $this->comments = 0;
            $this->features = 0;
            $this->blog = 0;
        }
        $this->save();
        return true;
    }

    /**
     * Send group invitation emails
     * @return \yii\db\ActiveQuery
     */
    public static function getAllUnsubscribed()
    {
        return self::find()->where(['unsubscribe' => 1])->all();
    }

    /**
     * Return unsubscribe token
     * @return \yii\db\ActiveQuery
     */
    public static function getToken($email)
    {
        return self::find()->select('token')->where(['email' => $email])->one()->token;
    }

    /**
     * Send notification message
     * @param  $multiple boolean Whether to send multiple messages at once
     * @param  $simple boolean Whether to send simple layout (no header or footer)
     * @return \yii\db\ActiveQuery
     */
    public function sendNotification($multiple=NULL, $simple=NULL)
    {
        // Check subscription
        if ($this->token && $this->unsubscribe) {
            return false;
        } elseif (!$this->token) {
            $this->add();
        }

        $view = $simple ? 'site/notification-simple' : 'site/notification';

        // Send message;
        $mailer = Yii::$app->mailer->compose(
                ['html' => $view . '-html', 'text' => $view . '-text'], 
                ['notification' => $this]
            )
            ->setFrom([$this->fromEmail => $this->fromName])
            ->setTo($this->to)
            ->setCc($this->cc)
            ->setSubject('IBNet | ' . $this->subject);

        return $multiple ? $mailer : $mailer->send();

    }
}