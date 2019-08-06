<?php

namespace backend\models;

use common\models\group\PrayerTag;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PrayerTagSearch extends \common\models\group\PrayerTag
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'group_id', 'tag', 'deleted'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PrayerTag::find()->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
    		        'pageSize' => 50,
    		    ],
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['group_id' => $this->group_id])
            ->andFilterWhere(['tag' => $this->tag])
            ->andFilterWhere(['deleted' => $this->deleted]);

        return $dataProvider;
    }
}