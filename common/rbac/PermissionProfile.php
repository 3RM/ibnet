<?php
/**
 * Profile Permission
 * 
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\rbac;

class PermissionProfile
{
    const CREATE = 'profiles.create';
    const UPDATE = 'profiles.update';
    const UPDATE_OWN = 'profiles.update.own';
    const DELETE = 'profiles.delete';
    const DELETE_OWN = 'profiles.delete.own';
}