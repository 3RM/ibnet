<?php

namespace common\models\group;

use common\models\group\GroupMember;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class MemberSearch extends GroupMember
{
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $id, $pending=NULL)
    { 
        $query = GroupMember::find()
            ->joinWith('user')
            ->where(['group_id' => $id])
            ->andWhere(['group_member.group_owner' => 0]);

        if ($pending) { 
            $query->andWhere(['group_member.status' => GroupMember::STATUS_PENDING]);
            $pageSize = -1;
        } else {
            $query->andWhere('((`group_member`.`status`=' . GroupMember::STATUS_ACTIVE . ') OR (`group_member`.`status`=' . GroupMember::STATUS_BANNED . '))');
            $pageSize = 20;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
    		    'pageSize' => $pageSize,
    		],
        ]);

        $dataProvider->setSort(['defaultOrder' => ['status' => SORT_ASC, 'id' => SORT_DESC]]);
 
        return $dataProvider;
    }
}