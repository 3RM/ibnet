<?php

namespace backend\models;

use common\models\group\PrayerUpdate;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PrayerUpdateSearch extends \common\models\group\PrayerUpdate
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
           [['id', 'prayer_id', 'update', 'created_at', 'deleted'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PrayerUpdate::find()->orderBy(['id' => SORT_DESC]);

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
            ->andFilterWhere(['prayer_id' => $this->prayer_id])
            ->andFilterWhere(['update' => $this->update])
            ->andFilterWhere(['created_at' => $this->created_at])
            ->andFilterWhere(['deleted' => $this->deleted]);

        return $dataProvider;
    }
}