<?php

namespace backend\models;

use common\models\group\Prayer;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PrayerSearch extends \common\models\group\Prayer
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'group_id', 'group_member_id', 'request', 'description', 'answered', 'answer_description', 
           'answer_date', 'duration', 'created_at', 'last_update', 'message_id', 'deleted'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Prayer::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['request' => $this->request])
            ->andFilterWhere(['description' => $this->description])
            ->andFilterWhere(['answered' => $this->answered])
            ->andFilterWhere(['answer_description' => $this->answer_description])
            ->andFilterWhere(['answer_date' => $this->answer_date])
            ->andFilterWhere(['duration' => $this->duration])
            ->andFilterWhere(['created_at' => $this->created_at])
            ->andFilterWhere(['last_update' => $this->last_update])
            ->andFilterWhere(['message_id' => $this->message_id])
            ->andFilterWhere(['deleted' => $this->deleted]);

        return $dataProvider;
    }
}