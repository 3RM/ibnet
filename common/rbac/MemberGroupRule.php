<?php
namespace common\rbac;

use common\models\group\Group;
use yii\helpers\ArrayHelper;
use yii\rbac\Rule;
use Yii;

/**
 * Checks if user is a member of the group passed via params
 */
class MemberGroupRule extends Rule
{
    public $name = 'isGroupMember';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $userModel = Yii::$app->user->identity;
        $joinedGroups = $userModel->joinedGroups;

        return 1; //(isset($params['Group']) && isset($joinedGroups)) ? in_array($params['Group']->id, ArrayHelper::getColumn($joinedGroups, 'id')) : false;
    }
}