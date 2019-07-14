<?php

namespace common\models\group;

use common\models\missionary\MissionaryUpdate;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class UpdateSearch extends MissionaryUpdate
{
    public $name;

    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params, $group)
    {
        $query = $group->getUpdates()
            ->where([
                'missionary_update.deleted' => 0,
                'missionary_update.profile_inactive' => 0,
                'missionary_update.vid_not_accessible' => 0])
            ->andWhere('missionary_update.to_date >= NOW()');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
    		    'pageSize' => 10,
    		],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['created_at' => SORT_DESC],
            'attributes' => [
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                ],
            ]
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            $query->joinWith(['user']);
            return $dataProvider;
        }

        $query->joinWith(['user' => function ($q) { 
            $_name = explode(' ', $this->name);
            $q->where('(user.first_name LIKE "%' . $_name[0] . '%" ' .
                    'AND user.last_name LIKE "%' . $_name[1] . '%")' .
                    'OR user.display_name LIKE "%' . $this->name . '%"');
        }]);

        return $dataProvider;
    }
}