<?php

namespace backend\models;

use common\models\profile\History;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class HistorySearch extends \common\models\profile\History
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'profile_id', 'date', 'title', 'description', 'event_image', 'deleted'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = History::find()->orderBy(['id' => SORT_DESC]);

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
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['profile_id' => $this->profile_id])
              ->andFilterWhere(['like', 'date', $this->date])
              ->andFilterWhere(['like', 'title', $this->title])
              ->andFilterWhere(['like', 'description', $this->description])
              ->andFilterWhere(['deleted' => $this->deleted]);

        return $dataProvider;
    }
}