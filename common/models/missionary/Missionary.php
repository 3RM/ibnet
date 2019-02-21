<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\missionary;

use common\models\profile\ProfileMail;
use common\models\profile\MissionAgcy;
use common\models\profile\Profile;
use common\models\profile\Staff;
use common\models\User;
use common\models\Utility;
use common\models\missionary\MailchimpList;
use common\models\missionary\MissionaryUpdate;
use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "missionary".
 *
 * @property int $id
 * @property int $user_id
 * @property string $field
 * @property string $status
 * @property int $mission_agcy_id FOREIGN KEY (mission_agcy_id) REFERENCES mission_agcy (id)
 * @property string $packet
 * @property int $cp_pastor_at FOREIGN KEY (cp_pastor_at) REFERENCES profile (id)
 * @property string $repository_key
 * @property string $mc_token
 * @property string $mc_key
 * @property int $viewed_update
 */

class Missionary extends \yii\db\ActiveRecord
{
    
    /**
     * @var string $showMap Accepts checkbox selection for map display on missionary church plant form
     */
    public $showMap;

    /**
     * @const int $STATUS_* The missionary field status
     */
    const STATUS = [
        'Deputation' => 'Deputation',
        'Field' => 'Field',
        'Furlough' => 'Furlough',
    ];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'missionary';
    }

    public function scenarios() {
        return[
            'fi' => ['field', 'status'],
            'cp' => ['cp_pastor_at', 'showMap'],
            'ma-missionary' => ['mission_agcy_id', 'packet'],
            'ma-chaplain' => ['mission_agcy_id', 'packet'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field', 'status'], 'required', 'on' => 'fi'],
            [['cp_pastor_at', 'showMap'], 'safe', 'on' => 'cp'],
            [['mission_agcy_id'], 'required', 'on' => 'ma-missionary'],
            [['packet'], 'file', 'extensions' => 'pdf', 'mimeTypes' => 'application/pdf', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'ma-missionary'],
            
            [['mission_agcy_id'], 'safe', 'on' => 'ma-chaplain'],
            [['packet'], 'file', 'extensions' => 'pdf', 'mimeTypes' => 'application/pdf', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'ma-chaplain'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cp_pastor_at' => 'Church-Planting Pastor at',
            'showMap' => 'Show a Google map of this church plant on my profile',
            'mission_agcy_id' => 'Mission Agency',
            'packet' => '',
        ];
    }

    /**
     * handleFormCP: Church Plant
     * 
     * @return mixed
     */
    public function handleFormCP($profile)
    {
        $oldCP = $this->getOldAttribute('cp_pastor_at');
        // Removed CP
        if ($oldCP && ($this->cp_pastor_at == NULL)) {
            $profile->updateAttributes(['cp_pastor' => NULL]);
            if ($staff = Staff::find()
                ->where(['staff_id' => $profile->id])
                ->andWhere(['ministry_id' => $oldCP])
                ->andWhere(['staff_type' => $profile->type])
                ->andWhere(['staff_title' => $profile->sub_type])
                ->andWhere(['church_pastor' => 1])
                ->one()) {
                $staff->delete();
            }

        // Added new CP
        } elseif ($this->cp_pastor_at && ($this->cp_pastor_at != $oldCP)) {
            $profile->updateAttributes(['cp_pastor' => 1]);
            if (!$staff = Staff::find()
                ->where(['staff_id' => $profile->id])
                ->andWhere(['ministry_id' => $this->cp_pastor_at])
                ->andWhere(['staff_type' => $profile->type]) // Allow for different staff roles at same church
                ->andWhere(['staff_title' => $profile->sub_type])
                ->andwhere(['church_pastor' => 1]) // Pastor of church plant
                ->one()) {
                $staff = new Staff();
                $staff->save();
            }
            $staff->updateAttributes([
                'staff_id' => $profile->id, 
                'staff_type' => $profile->type,
                'staff_title' => $profile->sub_type,
                'ministry_id' => $this->select,
                'church_pastor' => 1]);
        }
        $profile->updateMap(Profile::MAP_CHURCH_PLANT);

        return $profile;
    }  

    /**
     * handleFormMA: Missions Agency
     * Process selection of mission agency
     * @return mixed
     */
    public function handleFormMA($profile)
    {
    // *********************** Missions Packet *********************************
        if ($uploadPacket = UploadedFile::getInstance($this, 'packet')) {                           // Create subfolders on server and store uploaded pdf
            $fileName = md5(microtime() . $uploadPacket->name);
            $fileExt = strrchr($uploadPacket->name, '.');
            $fileDir = substr($fileName, 0, 2);
            
            $fileBasePath = Yii::getAlias('@webroot') . Yii::getAlias('@packet');
            if (!is_dir($fileBasePath)) {
                mkdir($fileBasePath, 0755, true);
            }
            $relativePath = Yii::getAlias('@packet') . DIRECTORY_SEPARATOR . $fileDir;
            $filePath = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $relativePath;
            if (!is_dir($filePath)) {
                mkdir($filePath, 0755, true);
            }
            $uploadPacket->saveAs($filePath . DIRECTORY_SEPARATOR . $fileName . $fileExt);
            $this->packet =  $filePath . DIRECTORY_SEPARATOR . $fileName . $fileExt;
        } else {
            $this->packet = $this->getOldAttribute('packet');
        }

    // ************************** Missionary ***********************************

        if ($this->mission_agcy_id != ($this->getOldAttribute('mission_agcy_id'))) {                // Send link notifications
            $missionary = $this->profile;

            $oldMA = MissionAgcy::findOne($this->getOldAttribute('mission_agcy_id'));
            if ($oldMA && ($oldMAProfile = $oldMA->linkedProfile)) {
                $oldMAProfileOwner = User::findOne($oldMAProfile->user_id);
                ProfileMail::sendLink($missionary, $oldMAProfile, $oldMAProfileOwner, 'MA', 'UL');  // Notify mission agency profile owner of unlink
            }
            
            $mA = $this->missionAgcy;
            if ($mA && ($mAProfile = $mA->linkedProfile)) {
                $mAProfileOwner = User::findOne($mAProfile->user_id);
                ProfileMail::sendLink($missionary, $mAProfile, $mAProfileOwner, 'MA', 'L');     // Notify mission agency profile owner of link
            }
        }
        
        if ($this->validate() && $this->save() && $profile->setUpdateDate()) {            
            if (!isset($profile->missionary)) {
                $this->link('profile', $profile);
            }
            return $profile;
        }
        return False;
    }

    /**
     * Generate new repository key and save to missionary record
     *
     * @return mixed
     */
    public function generateRepositoryKey()
    {
        $randomString = Utility::generateUniqueRandomString($this, 'repository_key', 32);
        $this->updateAttributes(['repository_key' => $randomString]);
        
        return true;
    }

    /**
     * Get Mailchimp mailing lists via MC api
     *
     * @return array
     */
    public function getMCLists()
    {
        $client = new \sammaye\mailchimp\Chimp();
        $client->apikey = $this->mc_token;
        $res = $client->get('/lists');
        return $res->lists;
    }

    /**
     * Set Mailchimp webhooks via MC api
     *
     * @return boolean
     */
    public function setMCWebhook($listId)
    {
        if (empty($this->mc_key)) {
            $string = Utility::generateUniqueRandomString($this, 'mc_key', 12);
            $this->updateAttributes(['mc_key' => $string]);
        }
        $this->deleteAllMCWebhooks();                                                               // Remove all active webhooks
        $url = Url::to(['missionary/chimp-request', 'id' => $this->id, 'mc_key' => $this->mc_key], 'https');
        $client = new \sammaye\mailchimp\Chimp();
        $client->apikey = $this->mc_token;
        $res = $client->post('/lists/' . $listId . '/webhooks', [
            'url' => $url, 
            'events' => ['campaign' => true], 
            'sources' => ['user' => true, 'admin' => true, 'api' => true]
        ]);
        return true;
    }

    /**
     * Get Mailchimp webhooks via MC api
     *
     * @return boolean
     */
    public function getMCWebhooks($listId)
    {
        $client = new \sammaye\mailchimp\Chimp();
        $client->apikey = $this->mc_token;
        $res = $client->get('/lists/' . $listId . '/webhooks');
        return $res->webhooks;
    }

    /**
     * Delete Mailchimp webhooks via MC api
     *
     * @return boolean
     */
    public function deleteMCWebhook($listId, $webhookId)
    {
        $client = new \sammaye\mailchimp\Chimp();
        $client->apikey = $this->mc_token;
        $res = $client->delete('/lists/' . $listId . '/webhooks/' . $webhookId);
        return true;
    }

    /**
     * Get Mailchimp campaign via MC api
     *
     * @return array
     */
    public function getMCCampaign($campaignId)
    {
        $client = new \sammaye\mailchimp\Chimp();
        $client->apikey = $this->mc_token;
        $res = $client->get('/campaigns/' . $campaignId);
        return $res;
    }

    /**
     * Delete all Mailchimp webhooks via MC api
     *
     * @return boolean
     */
    public function deleteAllMCWebhooks()
    {
        $url = Url::to(['missionary/chimp-request', 'id' => $this->id, 'mc_key' => $this->mc_key], 'https');

        if ($lists = $this->getMCLists()) {
            foreach ($lists as $list) {
                if ($webhooks = $this->getMCWebhooks($list->id)) {
                    foreach ($webhooks as $webhook) {
                        if ($webhook->url == $url) {
                            $this->deleteMCWebhook($list->id, $webhook->id);
                        }
                    }
                }
                
            }
        }
        return true;
    }

     /**
     * Unsync Mailchimp from missionary account
     *
     * @return boolean
     */
    public function unsyncMC()
    {
        $this->deleteAllMCWebhooks();
        $this->updateAttributes(['mc_token' => NULL, 'mc_key' => NULL]);
        return true;
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
    public function getMissionAgcy()
    {
        return $this->hasOne(MissionAgcy::className(), ['id' => 'mission_agcy_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChurchPlant()
    {
        return $this->hasOne(Profile::className(), ['id' => 'cp_pastor_at']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdates()
    {
        return $this->hasMany(MissionaryUpdate::className(), ['missionary_id' => 'id'])
            ->andWhere(['deleted' => 0])
            ->andWhere('to_date >= NOW()')
            ->andWhere(['profile_inactive' => 0])
            ->andWhere(['vid_not_accessible' => 0])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPublicUpdates()
    {
        return $this->hasMany(MissionaryUpdate::className(), ['missionary_id' => 'id'])
            ->andwhere(['deleted' => 0, 'visible' => 1]) 
            ->andwhere('to_date >= NOW()')
            ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Updates including videos that are marked inaccessible 
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatesAll()
    {
        return $this->hasMany(MissionaryUpdate::className(), ['missionary_id' => 'id'])
            ->andWhere(['deleted' => 0])
            ->andWhere('to_date >= NOW()')
            ->andWhere(['profile_inactive' => 0])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * @return boolean
     */
    public function setUpdatesActive()
    {
        $updates = MissionaryUpdate::find()
            ->where(['missionary_id' => $this->id])
            ->andWhere(['deleted' => 0])
            ->andWhere('to_date >= NOW()')
            ->andWhere(['profile_inactive' => 1])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        foreach ($updates as $update) {
            $update->updateAttributes(['profile_inactive' => 0]);
        }
        return true;
    }
}