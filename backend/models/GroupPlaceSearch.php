<?php

namespace backend\models;

use common\models\group\GroupPlace;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupPlaceSearch extends \common\models\group\GroupPlace
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'group_id', 'city', 'state', 'country', 'deleted'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GroupPlace::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['city' => $this->city])
            ->andFilterWhere(['state' => $this->state])
            ->andFilterWhere(['country' => $this->country])
            ->andFilterWhere(['deleted' => $this->deleted]);

        return $dataProvider;
    }
}