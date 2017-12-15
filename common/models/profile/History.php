<?php

namespace common\models\profile;

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
