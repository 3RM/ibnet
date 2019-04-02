<?php

namespace common\models\network;

use common\models\network\Prayer;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class PrayerSearch extends Prayer
{
    public $name;
    public $tag;

    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['name', 'duration', 'tag', 'request'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params, $nid, $f=NULL, $l=0)
    {
        $query = Prayer::find()->distinct()->where(['prayer.network_id' => $nid, 'prayer.answered' => $l, 'prayer.deleted' => 0]);
        $pageSize = $f ? -1 : 10;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
    		    'pageSize' => $pageSize,
    		],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['last_update' => SORT_DESC],
            'attributes' => [
                'answer_date' => [
                    'asc' => ['answer_date' => SORT_ASC],
                    'desc' => ['answer_date' => SORT_DESC],
                ],
                'last_update' => [
                    'asc' => ['last_update' => SORT_ASC],
                    'desc' => ['last_update' => SORT_DESC],
                ],
                'name' => [
                    'asc' => ['user.last_name' => SORT_ASC, 'user.first_name' => SORT_ASC],
                    'desc' => ['user.last_name' => SORT_DESC, 'user.first_name' => SORT_DESC],
                ],
                'duration' => [
                    'asc' => ['duration' => SORT_ASC],
                    'desc' => ['duration' => SORT_DESC],
                ],
            ]
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            $query->joinWith(['networkUser', 'prayerTags']);
            return $dataProvider;
        }

        if (isset($this->name)) {
            $query->joinWith(['networkUser' => function ($q) {
                $_name = explode(' ', $this->name);
                $q->where('(user.first_name LIKE "%' . $_name[0] . '%" ' .
                        'AND user.last_name LIKE "%' . $_name[1] . '%")' .
                        'OR user.display_name LIKE "%' . $this->name . '%"');
            }]);
        }

        if (isset($this->tag)) {
            $query->joinWith(['prayerTags' => function ($q) {
                $q->where('prayer_tag.tag LIKE "%' . $this->tag . '%"');
            }]);
        }

        if (isset($this->duration)) {
            $query->andFilterWhere(['like', 'prayer.duration', $this->duration]);
        }

        return $dataProvider;
    }
}