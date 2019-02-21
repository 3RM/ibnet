<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use sadovojav\cutter\behaviors\CutterBehavior;
use Yii;

/**
 * This is the model class for table "history".
 *
 * @property string $id
 * @property string $history
 */
class History extends \yii\db\ActiveRecord
{
    /**
     * @var string $edit indicates if history event is being edited
     */
    public $edit = NULL;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'history';
    }

    public function behaviors()
    {   
        return [
            'image' => [
                'class' => CutterBehavior::className(),
                'attributes' => ['event_image'],
                'baseDir' => '/uploads/image',
                'basePath' => '@webroot',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'title'], 'required'],
            [['title'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 1000],
            [['title', 'description'], 'filter', 'filter' => 'strip_tags'],
            [['event_image'], 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date' => 'Event Date',
            'title' => 'Event Title',
            'description' => 'Description (optional)',
            'event_image' => 'Image (optional)',
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
