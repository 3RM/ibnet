<?php

namespace backend\models;

use common\models\group\GroupAlertQueue;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupAlertQueueSearch extends \common\models\group\GroupAlertQueue
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'created_at', 'group_id', 'prayer_id', 'prayer_status', 'update_id', 'alerted'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GroupAlertQueue::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['created_at' => $this->profile_id])
            ->andFilterWhere(['group_id', $this->date])
            ->andFilterWhere(['prayer_id', $this->title])
            ->andFilterWhere(['prayer_status', $this->description])
            ->andFilterWhere(['update_id' => $this->deleted])
            ->andFilterWhere(['alerted' => $this->deleted]);

        return $dataProvider;
    }
}