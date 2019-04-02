<?php

namespace common\models\network;

use Yii;

/**
 * This is the model class for table "network_place".
 *
 * @property int $id
 * @property int $network_id FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property string $city
 * @property string $state
 * @property string $country
 * @property int $deleted
 */
class NetworkPlace extends \yii\db\ActiveRecord
{
    /**
     * @var string $place capture Google places return string
     */
    public $place;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_place';
    }

    public function scenarios() {
        return[
            'new' => ['city', 'state', 'country', 'place'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city', 'state', 'country'], 'string', 'max' => 255],
            [['city', 'state', 'country'], 'filter', 'filter' => 'strip_tags'],
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
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'deleted' => 'Deleted',
        ];
    }
}