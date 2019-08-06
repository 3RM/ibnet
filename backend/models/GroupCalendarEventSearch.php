<?php

namespace backend\models;

use common\models\group\GroupCalendarEvent;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupCalendarEventSearch extends \common\models\group\GroupCalendarEvent
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'group_id', 'group_member_id', 'title', 'color', 'description', 'created_at', 'start', 'end', 
           'all_day', 'deleted'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GroupCalendarEvent::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['title' => $this->title])
            ->andFilterWhere(['color' => $this->color])
            ->andFilterWhere(['description' => $this->description])
            ->andFilterWhere(['created_at' => $this->created_at])
            ->andFilterWhere(['start' => $this->start])
            ->andFilterWhere(['end' => $this->end])
            ->andFilterWhere(['all_day' => $this->all_day])
            ->andFilterWhere(['deleted' => $this->deleted]);

        return $dataProvider;
    }
}