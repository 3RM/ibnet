<?php

namespace backend\models;

use common\models\profile\Profile;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProfileSearch extends \common\models\profile\Profile
{
    public $name;

    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['id', 'user_id', 'reviewed', 'type', 'name', 'created_at', 'renewal_date', 'status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Profile::find()->where(['!=', 'status', Profile::STATUS_BANNED]);

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

        return $dataProvider;
    }
}