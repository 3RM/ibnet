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
            [['id', 'staff_id', 'staff_type', 'staff_title', 'ministry_id', 'confirmed'], 'safe'],
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

        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
            'attributes' => [
                'id' => [
                    'asc' => ['id' => SORT_ASC],
                    'desc' => ['id' => SORT_DESC],
                ],
                'staff_id' => [
                    'asc' => ['staff_id' => SORT_ASC],
                    'desc' => ['staff_id' => SORT_DESC],
                ],
                'staff_type' => [
                    'asc' => ['staff_type' => SORT_ASC,],
                    'desc' => ['staff_type' => SORT_DESC],
                ],
                'staff_title' => [
                    'asc' => ['staff_title' => SORT_ASC],
                    'desc' => ['staff_title' => SORT_DESC],
                ],
                'ministry_id' => [
                    'asc' => ['ministry_id' => SORT_ASC],
                    'desc' => ['ministry_id' => SORT_DESC],
                ],
                'home_church' => [
                    'asc' => ['home_church' => SORT_ASC],
                    'desc' => ['home_church' => SORT_DESC],
                ],
                'church_pastor' => [
                    'asc' => ['church_pastor' => SORT_ASC],
                    'desc' => ['church_pastor' => SORT_DESC],
                ],
                'ministry_of' => [
                    'asc' => ['ministry_of' => SORT_ASC],
                    'desc' => ['ministry_of' => SORT_DESC],
                ],
                'ministry_other' => [
                    'asc' => ['ministry_other' => SORT_ASC],
                    'desc' => ['ministry_other' => SORT_DESC],
                ],
                'sr_pastor' => [
                    'asc' => ['sr_pastor' => SORT_ASC],
                    'desc' => ['sr_pastor' => SORT_DESC],
                ],
                'confirmed' => [
                    'asc' => ['confirmed' => SORT_ASC],
                    'desc' => ['confirmed' => SORT_DESC],
                ],

            ]
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
              ->andFilterWhere(['confirmed' => $this->confirmed]);

        return $dataProvider;
    }
}