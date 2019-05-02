<?php

namespace common\models\group;

use Yii;

/**
 * This is the model class for table "group_place".
 *
 * @property int $id
 * @property int $group_id FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION
 * @property string $city
 * @property string $state
 * @property string $country
 * @property int $deleted
 */
class GroupPlace extends \yii\db\ActiveRecord
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
        return 'group_place';
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
            'group_id' => 'Group ID',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'deleted' => 'Deleted',
        ];
    }
}