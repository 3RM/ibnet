<?php

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "social".
 *
 * @property string $id
 * @property string $social
 */
class Social extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function Social()
    {
        return 'social';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sermonaudio', 'facebook', 'linkedin', 'twitter', 'google', 'rss', 'youtube', 'vimeo', 'pinterest', 'tumblr', 'soundcloud', 'instagram', 'flickr'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' =>true],
        ];
    }

        /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'sermonaudio' => 'SermonAudio',
            'facebook' => 'Facebook', 
            'linkedin' => 'LinkedIn', 
            'twitter' => 'Twitter', 
            'google' => 'Google+', 
            'rss' => 'RSS', 
            'youtube' => 'YouTube', 
            'vimeo' => 'Vimeo', 
            'pinterest' => 'Pinterest', 
            'tumblr' => 'Tumblr', 
            'soundcloud' => 'SoundCloud', 
            'instagram' => 'Instagram', 
            'flickr' => 'Flickr',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['social_id' => 'id']);
    }

}
