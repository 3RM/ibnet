<?php

namespace backend\models;

use common\models\profile\Missionary;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MissionarySearch extends \common\models\profile\Missionary
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['id', 'mission_agcy_id'], 'integer'],
            [['field', 'status', 'packet', 'cp_pastor_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Missionary::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
    		    'pageSize' => 10,
    		],
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'mission_agcy_id', $this->mission_agcy_id])
              ->andFilterWhere(['like', 'field', $this->field])
              ->andFilterWhere(['like', 'status', $this->status])
              ->andFilterWhere(['like', 'packet', $this->packet])
              ->andFilterWhere(['like', 'cp_pastor_at', $this->cp_pastor_at]);

        return $dataProvider;
    }
}