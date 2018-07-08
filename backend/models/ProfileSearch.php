<?php

namespace backend\models;

use common\models\profile\Profile;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProfileSearch extends \common\models\profile\Profile
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['id', 'user_id'], 'integer'],
            [['reviewed', 'type', 'profile_name', 'org_name', 'ind_last_name', 'created_at', 'last_update', 'renewal_date', 'inactivation_date', 'status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Profile::find()->orderBy(['id' => SORT_DESC]);

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
        $query->andFilterWhere(['like', 'user_id', $this->user_id])
              ->andFilterWhere(['like', 'type', $this->type])
              ->andFilterWhere(['like', 'profile_name', $this->profile_name])
              ->andFilterWhere(['like', 'org_name', $this->org_name])
              ->andFilterWhere(['like', 'ind_last_name', $this->ind_last_name])
              ->andFilterWhere(['like', 'created_at', $this->created_at])
              ->andFilterWhere(['like', 'last_update', $this->last_update])
              ->andFilterWhere(['like', 'renewal_date', $this->renewal_date])
              ->andFilterWhere(['like', 'inactivation_date', $this->inactivation_date])
              ->andFilterWhere(['like', 'status', $this->status])
              ->andFilterWhere(['like', 'reviewed', $this->reviewed]);

        return $dataProvider;
    }
}