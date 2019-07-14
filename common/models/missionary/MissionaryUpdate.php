<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\missionary;

use common\models\User;
use common\models\Utility;
use common\models\group\GroupAlertQueue;
use common\models\group\GroupMember;
use common\models\profile\Profile;
use GuzzleHttp\Client;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "missionary_update".
 *
 * @property int $id
 * @property int $missionary_id FOREIGN KEY (missionary_id) REFERENCES  missionary (id)
 * @property int $created_at
 * @property int $updated_at
 * @property string $title
 * @property string $pdf
 * @property string $mailchimp_url
 * @property string $youtube_url
 * @property string $vimeo_url
 * @property string $thumbnail
 * @property string $description
 * @property string $from_date
 * @property string $to_date
 * @property int $visible
 * @property int $deleted
 * @property int $vid_not_accessible
 * @property int $profile_inactive
 */
class MissionaryUpdate extends \yii\db\ActiveRecord
{
    /**
     * @var int $active Stores duration to calculate to_date on repository page
     */
    public $active;

    /**
     * @var int $editActive Stores duration to calculate to_date on repository page (second Select2 widget)
     */
    public $editActive;

    /**
     * @var boolean $edit Indicates if missionary update record is being edited
     */
    public $edit = NULL;

    /**
     * @var string $videoHtml Stores video embed html
     */
    public $videoHtml;

    /**
     * @const int ALERT_* The alert status of the update.
     */
    const ALERT_ENABLED = 10;
    const ALERT_PAUSED = 20;
    const ALERT_USER_SENT = 30; // User sent to send queue
    const ALERT_SENT = 40;
    const ALERT_CANCELED = 50;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'missionary_update';
    }

    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pdf'], 'file', 'extensions' => 'pdf', 'mimeTypes' => 'application/pdf', 'maxFiles' => 1, 'maxSize' => 1024 * 6000, 'skipOnEmpty' => true],
            [['youtube_url', 'vimeo_url', 'drive_url'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' =>true],
            ['drive_url', function ($attribute, $params, $validator) {
                if (!preg_match('%https:\/\/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)\/view\?usp=sharing%', $this->$attribute)) {
                    $this->addError($attribute, 'Incorrect format for a Google Drive video url. Ensure you copied the link from the video preview window in your Google Drive account.');
                }
            }],
            [['title'], 'string', 'max' => 60, 'message' => 'Your text exceeds 60 characters.'],
            [['description'], 'string', 'max' => 1500, 'message' => 'Your text exceeds 1500 characters.'],
            [['active', 'profile_inactive', 'thumbnail'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'active' => 'Keep active for:',
            'editActive' => 'Keep active for:',
            'drive_url' => 'Google Drive Video Url',
            'pdf' => 'PDF',

        ];
    }

    /**
     * @inheritdoc
     */
    public function handleForm()
    {
        if ($this->validate()) {

    		$this->from_date = new Expression('CURDATE()');
    		if ($this->editActive != NULL) {
    			$this->active = $this->editActive;
    		}


    	// ************************** Set to date *****************************
    		switch ($this->active) {
    			case '3':
    				$this->to_date = new Expression('DATE_ADD(CURDATE(), INTERVAL 3 MONTH)');
    				break;
    			case '6':
    				$this->to_date = new Expression('DATE_ADD(CURDATE(), INTERVAL 6 YEAR)');
    				break;
    			case '12':
    				$this->to_date = new Expression('DATE_ADD(CURDATE(), INTERVAL 1 YEAR)');
    				break;
    			case '24':
    				$this->to_date = new Expression('DATE_ADD(CURDATE(), INTERVAL 2 YEAR)');
    				break;
    			case '99':
    				$this->to_date = new Expression('DATE_ADD(CURDATE(), INTERVAL 99 YEAR)');
    				break;
    			
    			default:
    				break;
    		}      	

    	// ************************* Validate Video Url *****************************
        	if ($this->vimeo_url || $this->youtube_url) {
                if (!$this->thumbnail = $this->getVideo(false, true)) {
                    Yii::$app->session->setFlash('danger', 'The Url you supplied does not appear to be a valid Video Url. Ensure your video privacy settings allow embedding. Please contact us if you think this is in error.');
                    return $this;
                }

            } elseif ($this->drive_url) {
                // Put url into correct format for embed
                $this->drive_url = 
                    preg_match('%https:\/\/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)%', $this->drive_url, $match) ? $match[0] . '/preview' : 
                    NULL;
      
        // ************************* Process PDF upload *****************************
    		// Create subfolders on server and store uploaded pdf
            } elseif ($pdf = UploadedFile::getInstance($this, 'pdf')) {
        	    $fileName = md5(microtime() . $pdf->name);
        	    $fileExt = strrchr($pdf->name, '.');
        	    $fileDir = substr($pdf, 0, 2);
        	    
        	    $fileBasePath = Yii::getAlias('@webroot') . Yii::getAlias('@update');
        	    if (!is_dir($fileBasePath)) {
        	        mkdir($fileBasePath, 0755, true);
        	    }
        	    $relativePath = Yii::getAlias('@update') . DIRECTORY_SEPARATOR . $fileDir;
        	    $filePath = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $relativePath;
        	    if (!is_dir($filePath)) {
        	        mkdir($filePath, 0755, true);
        	    }

        	    $pdf->saveAs($filePath . DIRECTORY_SEPARATOR . $fileName . $fileExt);
        	    $this->pdf =  $relativePath . DIRECTORY_SEPARATOR . $fileName . $fileExt;
        	} else {
        	    $this->pdf = $this->getOldAttribute('pdf');
        	}

            if (($this->edit == NULL) 
                && ($this->pdf == NULL) 
                && ($this->vimeo_url == NULL) 
                && ($this->youtube_url == NULL)
                && ($this->drive_url == NULL)) {
                Yii::$app->session->setFlash('info', 'Your update was not saved.  Be sure to upload a pdf or video link.');
                return $this;
            }
            
            $this->save(false);

            // Add to group alert queue
            if ($members = Yii::$app->user->identity->groupMembers) {
                // If sharing updates with at least one group, add to queue
                foreach ($members as $member) {
                    if ($member->show_updates == 1) {
                        $this->addToAlertQueue();
                        $this->updateAttributes(['alert_status' => self::ALERT_ENABLED]);
                        break;
                    }
                }
            }

    		return $this;
    	}
    }

    /**
     * Add update to alert queue
     * @return \yii\db\ActiveQuery
     */
    public function addToAlertQueue()
    {
        $queue = GroupAlertQueue::findOne(['update_id' => $this->id]) ?? new GroupAlertQueue();
        $queue->update_id = $this->id;
        $queue->save();
    }

    /**
     * Call video API
     *
     * If video is retrievable:
     *    Clear vid_not_accessible if set
     *    Return embed coded if $thumbnail = false
     *    Return thumbnail url if $thumbnal = true
     * If video is not retrievable:
     *    Set vid_not_accessible if not set
     *    Return false if $errorImage=false
     *    Return error image html if $errorImage=true
     *
     * Default is return embed code | false
     *
     * http://docs.guzzlephp.org/en/stable/request-options.html
     * 
     * @return mixed
     */
    public function getVideo($errorImage=false, $thumb=false)
    {
        if ($this->vimeo_url) {
            $url = Yii::$app->params['url.vimeoOembed'] . $this->vimeo_url;
        } elseif ($this->youtube_url) {
            $url = Yii::$app->params['url.youtubeOembed'] . $this->youtube_url;
        } else {
            return true;
        }
        
        $client = new Client();
        $response = $client->request('GET', $url, ['http_errors' => false]);
        if ($response->getStatusCode() !== 200) {
            if ($this->vid_not_accessible == 0) {
                $this->updateAttributes(['vid_not_accessible' => 1]);
            }
            return $errorImage ? Html::img('@img.group/broken-vid.jpg', ['style' => 'width:100%']) : false;
        } else {
            $json = $response->getBody()->getContents();
            $decoded = json_decode($json);
            if ($this->vid_not_accessible == 1) {
                $this->updateAttributes(['vid_not_accessible' => 0]);
            }
            return $thumb ? $decoded->thumbnail_url : $decoded->html;
        }
    }

    /*
     * @return \yii\db\ActiveQuery
     */
    public function getMissionary()
    {
        return $this->hasOne(Missionary::className(), ['id' => 'missionary_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->via('missionary');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMember()
    {
        return $this->hasOne(GroupMember::className(), ['missionary_id' => 'missionary_id']);
    }

    /**
     * Group member of current user if sharing missionary updates
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMemberSharingUpdates()
    {
        return $this->hasOne(GroupMember::className(), ['missionary_id' => 'missionary_id'])->where(['show_updates' => 1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMemberProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id'])
            ->via('groupMember');
    }

    /**
     * @return string
     */
    public function getRealName()
    {
        return $this->user->realName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->user->fullName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupAlert()
    {
        return $this->hasOne(GroupAlertQueue::className(), ['update_id' => 'id']);
    }
}
