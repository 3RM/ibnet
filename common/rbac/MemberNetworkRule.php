<?php
namespace common\rbac;

use common\models\network\Network;
use yii\helpers\ArrayHelper;
use yii\rbac\Rule;
use Yii;

/**
 * Checks if user ID matches user passed via params
 */
class MemberNetworkRule extends Rule
{
    public $name = 'isNetworkMember';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $userModel = Yii::$app->user->identity;
        $joinedNetworks = $userModel->getJoinedNetworks()->all();

        return (isset($params['Network']) && isset($joinedNetworks)) ? in_array($params['Network']->id, ArrayHelper::getColumn($joinedNetworks, 'id')) : false;
    }
}