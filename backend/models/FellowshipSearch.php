<?php

namespace backend\models;

use common\models\profile\Fellowship;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class FellowshipSearch extends \common\models\profile\Fellowship
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['id', 'profile_id'], 'integer'],
            [['fellowship', 'fellowship_acronym'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Fellowship::find()->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
    		    'pageSize' => 100,
    		],
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'profile_id', $this->profile_id])
              ->andFilterWhere(['like', 'fellowship', $this->fellowship])
              ->andFilterWhere(['like', 'fellowship_acronym', $this->fellowship_acronym]);

        return $dataProvider;
    }
}