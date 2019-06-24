<?php

namespace common\models\group;

use Yii;

/**
 * This is the model class for table "group_keyword".
 *
 * @property int $id
 * @property int $group_id FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property string $keyword
 * @property int $deleted
 */
class GroupKeyword extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_keyword';
    }

    public function scenarios() {
        return[
            'new' => ['keyword'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keyword'], 'required'],
            [['keyword'], 'string', 'max' => 12],
            [['keyword'], 'filter', 'filter' => 'strip_tags'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => 'Group ID',
            'keyword' => 'Keyword',
            'deleted' => 'Deleted',
        ];
    }
}