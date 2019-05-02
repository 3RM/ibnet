<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\missionary;

use common\models\User;
use common\models\Utility;
use common\models\group\GroupMember;
use common\models\profile\Profile;
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
            [['youtube_url', 'vimeo_url'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' =>true],
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
    		if (NULL != $this->editActive) {
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
                    Yii::$app->session->setFlash('danger', 'The Url you supplied does not appear to be a valid Vimeo Url. Ensure your video privacy settings allow embedding. Please contact us if you think this is in error.');
                    return false;
                }        	
      
        // ************************* Process PDF upload *****************************
    		} elseif ($pdf = UploadedFile::getInstance($this, 'pdf')) {                           // Create subfolders on server and store uploaded pdf
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

    		if ((NULL == $this->edit) 
                && (NULL == $this->pdf) 
                && (NULL == $this->vimeo_url) 
                && (NULL == $this->youtube_url)) {
    			Yii::$app->session->setFlash('info', 'Your update was not saved.  Be sure to upload a pdf or video link.');
    		} else {
    			$this->save();
    		}
    		return $this;
    	}
    }

    /**
     * Call video API
     *
     * If video is retrievable:
     *    Clear vid_not_accessible if set
     *    Return embed coded if $thumbnail = false
     *    Return thumbnail url if $thumbnal = true
     * If video is not retrievable:
     *    Set vid_not_accessible if clear
     *    Return false if $errorImage=false
     *    Return error image html if $errorImage=true
     *
     * Default is return embed code | false
     *
     * @return mixed
     */
    public function getVideo($errorImage=false, $thumb=false)
    {
        if ($this->vimeo_url) {
            $url = 'https://vimeo.com/api/oembed.json?url=' . $this->vimeo_url;
        } elseif ($this->youtube_url) {
            $url = 'http://www.youtube.com/oembed?format=json&url=' . $this->youtube_url;
        }
        $res = Utility::get($url);
        if (('404 Not Found' == $res) || ('Not Found' == $res)) {
            if (0 == $this->vid_not_accessible) {
                $this->updateAttributes(['vid_not_accessible' => 1]);
            }
            return $errorImage ? Html::img('@img.group/broken-vid.jpg', ['style' => 'width:100%']) : false;
        } else {
            if (1 == $this->vid_not_accessible) {
                $this->updateAttributes(['vid_not_accessible' => 0]);
            }
            return $thumb ? json_decode($res)->thumbnail_url : json_decode($res)->html;
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
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMemberProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id'])
            ->via('groupMember');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFullName()
    {
        return $this->user->fullName;
    }
}
