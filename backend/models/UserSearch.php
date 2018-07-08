<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends \common\models\User
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['id'], 'integer'],
            [['reviewed', 'first_name', 'last_name', 'email', 'new_email', 'username', 'created_at', 'last_login', 'status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = User::find()->orderBy(['id' => SORT_DESC]);

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
        $query->andFilterWhere(['like', 'first_name', $this->first_name])
              ->andFilterWhere(['like', 'last_name', $this->last_name])
              ->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'new_email', $this->new_email])
              ->andFilterWhere(['like', 'username', $this->username])
              ->andFilterWhere(['like', 'created_at', $this->created_at])
              ->andFilterWhere(['like', 'updated_at', $this->updated_at])
              ->andFilterWhere(['like', 'last_login', $this->last_login])
              ->andFilterWhere(['like', 'status', $this->status])
              ->andFilterWhere(['like', 'reviewed', $this->reviewed]);

        return $dataProvider;
    }
}