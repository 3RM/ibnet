<?php

namespace backend\models;

use common\models\profile\Association;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AssociationSearch extends \common\models\profile\Association
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['id', 'profile_id'], 'integer'],
            [['association', 'association_acronym'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Association::find()->orderBy(['id' => SORT_DESC]);

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
              ->andFilterWhere(['like', 'association', $this->association])
              ->andFilterWhere(['like', 'association_acronym', $this->association_acronym]);

        return $dataProvider;
    }
}