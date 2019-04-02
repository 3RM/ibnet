<?php

namespace common\models\network;

use Yii;

/**
 * This is the model class for table "network_keyword".
 *
 * @property int $id
 * @property int $network_id FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property string $keyword
 * @property int $deleted
 */
class NetworkKeyword extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_keyword';
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
            'network_id' => 'Network ID',
            'keyword' => 'Keyword',
            'deleted' => 'Deleted',
        ];
    }
}