<?php

namespace backend\models;

use common\models\group\GroupNotification;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupNotificationSearch extends \common\models\group\GroupNotification
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'group_id', 'user_id', 'created_at', 'reply_to', 'subject', 'message'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GroupNotification::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['user_id' => $this->user_id])
            ->andFilterWhere(['created_at' => $this->created_at])
            ->andFilterWhere(['reply_to' => $this->reply_to])
            ->andFilterWhere(['subject' => $this->subject])
            ->andFilterWhere(['message' => $this->message]);

        return $dataProvider;
    }
}