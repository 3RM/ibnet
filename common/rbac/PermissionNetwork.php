<?php
/**
 * Network Permissions
 * 
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\rbac;

class PermissionNetwork
{
    const CREATE = 'networks.create';
    const UPDATE = 'networks.update';
    const UPDATE_OWN = 'networks.update.own';
    const DELETE = 'networks.delete';
    const DELETE_OWN = 'networks.delete.own';
    const ACCESS = 'networks.access';
}