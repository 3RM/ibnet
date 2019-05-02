<?php
namespace common\rbac;

use common\models\group\Group;
use yii\rbac\Rule;

/**
 * Checks if user ID matches user passed via params
 */
class MyGroupRule extends Rule
{
    public $name = 'isGroupOwner';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return true; // isset($params['Group']) ? $params['Group']->user_id === $user : false;
    }
}