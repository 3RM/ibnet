<?php

namespace backend\models;

use common\models\group\GroupIcalendarUrl;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupIcalendarUrlSearch extends \common\models\group\GroupIcalendarUrl
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'group_id', 'group_member_id', 'ical_id', 'url', 'color', 'error_on_import', 
            'deleted'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GroupIcalendarUrl::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['group_member_id' => $this->group_member_id])
            ->andFilterWhere(['ical_id' => $this->ical_id])
            ->andFilterWhere(['url' => $this->url])
            ->andFilterWhere(['color' => $this->color])
            ->andFilterWhere(['error_on_import' => $this->error_on_import])
            ->andFilterWhere(['deleted' => $this->deleted]);

        return $dataProvider;
    }
}