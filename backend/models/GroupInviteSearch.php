<?php

namespace backend\models;

use common\models\group\GroupInvite;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupInviteSearch extends \common\models\group\GroupInvite
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'group_id', 'email', 'created_at', 'token'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GroupInvite::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['email' => $this->email])
            ->andFilterWhere(['created_at' => $this->created_at])
            ->andFilterWhere(['token' => $this->token]);

        return $dataProvider;
    }
}