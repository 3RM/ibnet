<?php

namespace common\models\network;

use common\models\network\Network;
use common\models\network\Prayer;
use common\models\profile\Profile;
use common\models\User;
use Yii;

/**
 * This is the model class for table "network_member".
 *
 * @property int $id
 * @property int $network_id
 * @property int $user_id
 * @property int $profile_id
 * @property string $created_at
 */
class NetworkMember extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['network_id'], 'required'],
            [['network_id', 'user_id', 'profile_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'network_id' => 'Network ID',
            'user_id' => 'User ID',
            'profile_id' => 'Profile ID',
            'created_at' => 'Created At',
            'email_prayer_alert' => '',
            'email_prayer_summary' => '',
            'email_update_alert' => '',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::className(), ['id' => 'network_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function networkMemberId($id)
    {
        return self::find()
            ->where(['network_id' => $id, 'user_id' => Yii::$app->user->identity->id])
            ->one()
            ->id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmail()
    {
        return $this->user->email;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrayers()
    {
        return $this->hasMany(Prayer::className(), ['network_member_id' => 'id']);
    }
}
