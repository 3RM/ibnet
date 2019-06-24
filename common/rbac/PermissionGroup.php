<?php
/**
 * Group Permissions
 * 
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\rbac;

class PermissionGroup
{
    const CREATE = 'groups.create';
    const UPDATE = 'groups.update';
    const UPDATE_OWN = 'groups.update.own';
    const DELETE = 'groups.delete';
    const DELETE_OWN = 'groups.delete.own';
    const ACCESS = 'groups.access';
}