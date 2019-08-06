<?php

namespace backend\models;

use common\models\group\Group;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GroupSearch extends \common\models\User
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['reviewed', 'id', 'name', 'username', 'email', 'role', 'last_login', 'status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Group::find(); //->joinWith('assignment');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
    		    'pageSize' => 50,
    		],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
            'attributes' => [
                // 'reviewed' => [
                //     'asc' => ['reviewed' => SORT_ASC],
                //     'desc' => ['reviewed' => SORT_DESC],
                // ],
                'id' => [
                    'asc' => ['id' => SORT_ASC],
                    'desc' => ['id' => SORT_DESC],
                ],
                // 'name' => [
                //     'asc' => ['last_name' => SORT_ASC, 'first_name' => SORT_ASC],
                //     'desc' => ['last_name' => SORT_DESC, 'first_name' => SORT_DESC],
                // ],
                // 'username' => [
                //     'asc' => ['username' => SORT_ASC],
                //     'desc' => ['username' => SORT_DESC],
                // ],
                // 'email' => [
                //     'asc' => ['email' => SORT_ASC],
                //     'desc' => ['email' => SORT_DESC],
                // ],
                // 'role' => [
                //     'asc' => ['auth_assignment.item_name' => SORT_ASC],
                //     'desc' => ['auth_assignment.item_name' => SORT_DESC],
                // ],
                // 'last_login' => [
                //     'asc' => ['last_login' => SORT_ASC],
                //     'desc' => ['last_login' => SORT_DESC],
                // ],
                // 'status' => [
                //     'asc' => ['status' => SORT_ASC],
                //     'desc' => ['status' => SORT_DESC],
                // ],
            ]
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}