<?php

namespace common\models\missionary;

use common\models\Utility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * This is the model class for table "missionary_update".
 *
 * @property string $id
 * @property string $missionary_id
 * @property string $created_at
 * @property string $title
 * @property string $pdf
 * @property string $youtube_link
 * @property string $vimeo_link
 * @property string $description
 * @property string $start_date
 * @property string $end_date
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
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
            [['active', 'profile_inactive'], 'safe'],
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

    	// ************************* Validate Vimeo Url *****************************
        	if ($this->vimeo_url) {  // https://developer.vimeo.com/apis/oembed

        		$vimeo_oembed_url = 'https://vimeo.com/api/oembed.json?url=';
        		$url = $vimeo_oembed_url . $this->vimeo_url;
        		$res = Utility::get($url);
        		if ($res == '404 Not Found') {
        			Yii::$app->session->setFlash('danger', 'The Url you supplied does not appear to be a valid Vimeo Url. Please contact us if you think this is in error.');
        			return false;
        		}
        		$decoded = Json::decode($res);
        		if (!is_array($decoded) || !isset($decoded['html'])) {
        			Yii::$app->session->setFlash('danger', 'The Url you supplied does not appear to be a valid Vimeo Url. Please contact us if you think this is in error.');
        			return false;
        		}
        		$this->thumbnail = ($decoded['thumbnail_url'] != NULL) ? $decoded['thumbnail_url'] : NULL;

    	// ************************** Validate Youtube Url *************************
        	} elseif ($this->youtube_url) {

        		$youtube_oembed_url = 'http://www.youtube.com/oembed?format=json&url=';
        		$url = $youtube_oembed_url . $this->youtube_url;
        		$res = Utility::get($url);
        		if ($res == '404 Not Found') {
        			Yii::$app->session->setFlash('danger', 'The Url you supplied does not appear to be a valid Youtube Url. Please contact us if you think this is in error.');
        			return false;
        		}
        		$decoded = Json::decode($res);
        		if (!is_array($decoded) || !isset($decoded['html'])) {
        			Yii::$app->session->setFlash('danger', 'The Url you supplied does not appear to be a valid Youtube Url. Please contact us if you think this is in error.');
        			return false;
        		}
        		$this->thumbnail = ($decoded['thumbnail_url'] != NULL) ? $decoded['thumbnail_url'] : NULL;
      
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

    		if (($this->edit == NULL) && ($this->pdf == NULL) && ($this->vimeo_url == NULL) && ($this->youtube_url == NULL)) {
    			Yii::$app->session->setFlash('info', 'Your update was not saved.  Be sure to upload a pdf or video link.');
    		} else {
    			$this->save();
    		}
    		return $this;
    	}
    }

    /*
     * Links a missionary update to its missionary record
     * @return \yii\db\ActiveQuery
     */
    public function getMissionary()
    {
        return $this->hasOne(Missionary::className(), ['id' => 'missionary_id']);
    }
}
