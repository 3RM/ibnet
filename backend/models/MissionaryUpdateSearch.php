<?php

namespace backend\models;

use common\models\missionary\MissionaryUpdate;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MissionaryUpdateSearch extends \common\models\missionary\MissionaryUpdate
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
        $query = MissionaryUpdate::find()->orderBy(['id' => SORT_DESC]);

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
        $query->andFilterWhere(['like', 'mission_agcy_id', $this->mission_agcy_id])
              ->andFilterWhere(['like', 'field', $this->field])
              ->andFilterWhere(['like', 'status', $this->status])
              ->andFilterWhere(['like', 'packet', $this->packet])
              ->andFilterWhere(['like', 'cp_pastor_at', $this->cp_pastor_at]);

        return $dataProvider;
    }
}