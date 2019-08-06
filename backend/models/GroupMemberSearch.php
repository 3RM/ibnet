<?php

namespace backend\models;

use common\models\group\GroupMember;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupMemberSearch extends \common\models\group\GroupMember
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'group_id', 'user_id', 'profile_id', 'missionary_id', 'group_owner', 'created_at', 'status', 'approval_date', 
            'inactivate_date', 'show_updates', 'email_prayer_alert', 'email_prayer_summary', 'email_update_alert'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GroupMember::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['profile_id' => $this->profile_id])
            ->andFilterWhere(['missionary_id' => $this->missionary_id])
            ->andFilterWhere(['group_owner' => $this->group_owner])
            ->andFilterWhere(['created_at' => $this->created_at])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['approval_date' => $this->approval_date])
            ->andFilterWhere(['inactivate_date' => $this->inactivate_date])
            ->andFilterWhere(['show_updates' => $this->show_updates])
            ->andFilterWhere(['email_prayer_alert' => $this->email_prayer_alert])
            ->andFilterWhere(['email_prayer_summary' => $this->email_prayer_summary])
            ->andFilterWhere(['email_update_alert' => $this->email_update_alert]);

        return $dataProvider;
    }
}