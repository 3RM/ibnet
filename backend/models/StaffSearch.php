<?php

namespace backend\models;

use common\models\profile\Staff;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StaffSearch extends \common\models\profile\Staff
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['id', 'staff_id, ministry_id'], 'integer'],
            [['staff_type', 'staff_title', 'home_church', 'church_pastor', 'ministry_of', 'sr_pastor', 'confirmed'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Staff::find()->orderBy(['id' => SORT_DESC]);

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
        $query->andFilterWhere(['like', 'staff_id', $this->staff_id])
              ->andFilterWhere(['like', 'ministry_id', $this->ministry_id])
              ->andFilterWhere(['like', 'staff_type', $this->staff_type])
              ->andFilterWhere(['like', 'staff_title', $this->staff_title])
              ->andFilterWhere(['like', 'home_church', $this->home_church])
              ->andFilterWhere(['like', 'church_pastor', $this->church_pastor])
              ->andFilterWhere(['like', 'ministry_of', $this->ministry_of])
              ->andFilterWhere(['like', 'sr_pastor', $this->sr_pastor])
              ->andFilterWhere(['like', 'confirmed', $this->confirmed]);

        return $dataProvider;
    }
}