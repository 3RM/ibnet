<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "social".
 *
 * @property int $id
 * @property string $facebook
 * @property string $instagram
 * @property string $flickr
 * @property string $linkedin
 * @property string $pinterest
 * @property string $rss
 * @property string $sermonaudio
 * @property string $soundcloud
 * @property string $tumblr
 * @property string $twitter
 * @property string $vimeo
 * @property string $youtube
 * @property int $reviewed
 */
class Social extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'social';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sermonaudio', 'facebook', 'linkedin', 'twitter', 'rss', 'youtube', 'vimeo', 'pinterest', 'tumblr', 'soundcloud', 'instagram', 'flickr'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' =>true],
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
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }

}
