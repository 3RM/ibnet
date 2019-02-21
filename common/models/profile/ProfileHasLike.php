<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use Yii;

/**
 * This is the model class for table "profile_has_like".
 *    
 * @property User $likedBy
 * @property Profile $profile
 * @property int $profile_id FOREIGN KEY (profile_id) REFERENCES profile (id)
 * @property int $liked_by_id FOREIGN KEY (liked_by_id) REFERENCES profile (id)
 */
class ProfileHasLike extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile_has_like';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}
