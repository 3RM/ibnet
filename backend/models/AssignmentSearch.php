<?php

namespace backend\models;

use backend\models\Assignment;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AssignmentSearch extends \backend\models\Assignment
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['item_name', 'user_id'], 'string'],
            ['created_at', 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Assignment::find()->orderBy(['user_id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['user_id' => SORT_DESC]],
            'pagination' => [
    		    'pageSize' => 100,
    		],
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['user_id' => $this->user_id]);
        $query->andFilterWhere(['like', 'item_name', $this->item_name])
              ->andFilterWhere(['like', 'created_at', $this->created_at]);

        return $dataProvider;
    }
}