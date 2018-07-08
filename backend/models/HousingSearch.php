<?php

namespace backend\models;

use common\models\profile\MissHousing;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class HousingSearch extends \common\models\profile\MissHousing
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            ['id', 'integer'],
            [['description', 'contact', 'trailer', 'water', 'electric', 'sewage'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = MissHousing::find()->orderBy(['id' => SORT_DESC]);

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
        $query->andFilterWhere(['like', 'description', $this->description])
              ->andFilterWhere(['like', 'contact', $this->contact])
              ->andFilterWhere(['like', 'trailer', $this->trailer])
              ->andFilterWhere(['like', 'water', $this->water])
              ->andFilterWhere(['like', 'electric', $this->electric])
              ->andFilterWhere(['like', 'sewage', $this->sewage]);

        return $dataProvider;
    }
}