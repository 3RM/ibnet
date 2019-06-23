<?php

use common\rbac\MyProfileRule;
use common\rbac\PermissionProfile;
use common\rbac\MyGroupRule;
use common\rbac\MemberGroupRule;
use common\rbac\PermissionGroup;
use \rmrevin\yii\module\Comments\Permission;
use \rmrevin\yii\module\Comments\rbac\ItsMyComment;
use yii\db\Migration;

/**
 * Class m190130_222359_populate_rbac
 */
class m190311_181235_populate_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $auth = Yii::$app->authManager;

        /*********************************
         * Create Roles
         *********************************/
        // Admin
        $admin = $auth->createRole('Admin');
        $auth->add($admin);

        // User
        $user = $auth->createRole('User');
        $auth->add($user);

        // SafeUser (has identified home church)
        $safeUser = $auth->createRole('SafeUser');
        $auth->add($safeUser);

        $auth->addChild($admin, $safeUser);
        $auth->addChild($safeUser, $user);


        /*********************************
         * Comment Permissions
         *********************************/
        $ItsMyCommentRule = new ItsMyComment();
        $auth->add($ItsMyCommentRule);
        
        $auth->add(new \yii\rbac\Permission([
            'name' => Permission::CREATE,
            'description' => 'Can create own comments',
        ]));
        $auth->add(new \yii\rbac\Permission([
            'name' => Permission::UPDATE,
            'description' => 'Can update all comments',
        ]));
        $auth->add(new \yii\rbac\Permission([
            'name' => Permission::UPDATE_OWN,
            'ruleName' => $ItsMyCommentRule->name,
            'description' => 'Can update own comments',
        ]));
        $auth->add(new \yii\rbac\Permission([
            'name' => Permission::DELETE,
            'description' => 'Can delete all comments',
        ]));
        $auth->add(new \yii\rbac\Permission([
            'name' => Permission::DELETE_OWN,
            'ruleName' => $ItsMyCommentRule->name,
            'description' => 'Can delete own comments',
        ]));


        /*********************************
         * Profile Permissions
         *********************************/
        $myProfileRule = new MyProfileRule();
        $auth->add($myProfileRule);

        $createProfile = $auth->createPermission(PermissionProfile::CREATE);
        $createProfile->description = 'Can create own profiles';
        $auth->add($createProfile);

        $updateProfile = $auth->createPermission(PermissionProfile::UPDATE);
        $updateProfile->description = 'Can update profiles';
        $auth->add($updateProfile);

        $updateOwnProfile = $auth->createPermission(PermissionProfile::UPDATE_OWN);
        $updateOwnProfile->description = 'Can update own profiles';
        $updateOwnProfile->ruleName = $myProfileRule->name;
        $auth->add($updateOwnProfile);

        $deleteProfile = $auth->createPermission(PermissionProfile::DELETE);
        $deleteProfile->description = 'Can delete own profiles';
        $auth->add($deleteProfile);

        $deleteOwnProfile = $auth->createPermission(PermissionProfile::DELETE_OWN);
        $deleteOwnProfile->description = 'Can delete own profiles';
        $deleteOwnProfile->ruleName = $myProfileRule->name;
        $auth->add($deleteOwnProfile);

        // Assign roles
        $auth->addChild($admin, $updateProfile);
        $auth->addChild($admin, $deleteProfile);

        $auth->addChild($updateOwnProfile, $updateProfile);
        $auth->addChild($deleteOwnProfile, $deleteProfile);

        $auth->addChild($user, $createProfile);
        $auth->addChild($user, $updateOwnProfile);
        $auth->addChild($user, $deleteOwnProfile);


        /*********************************
         * Group Permissions
         *********************************/
        $myGroupRule = new MyGroupRule();
        $auth->add($myGroupRule);

        $memberGroupRule = new MemberGroupRule();
        $auth->add($memberGroupRule);

        $createGroup = $auth->createPermission(PermissionGroup::CREATE);
        $createGroup->description = 'Can create own groups';
        $auth->add($createGroup);

        $updateGroup = $auth->createPermission(PermissionGroup::UPDATE);
        $updateGroup->description = 'Can update groups';
        $auth->add($updateGroup);

        $updateOwnGroup = $auth->createPermission(PermissionGroup::UPDATE_OWN);
        $updateOwnGroup->description = 'Can update own groups';
        $updateOwnGroup->ruleName = $myGroupRule->name;
        $auth->add($updateOwnGroup);

        $deleteGroup = $auth->createPermission(PermissionGroup::DELETE);
        $deleteGroup->description = 'Can delete groups';
        $auth->add($deleteGroup);

        $deleteOwnGroup = $auth->createPermission(PermissionGroup::DELETE_OWN);
        $deleteOwnGroup->description = 'Can delete own groups';
        $deleteOwnGroup->ruleName = $myGroupRule->name;
        $auth->add($deleteOwnGroup);

        $accessGroup = $auth->createPermission(PermissionGroup::ACCESS);
        $accessGroup->description = 'Can access groups';
        $accessGroup->ruleName = $memberGroupRule->name;
        $auth->add($accessGroup);

        // Assign roles
        $auth->addChild($admin, $updateGroup);
        $auth->addChild($admin, $deleteGroup);

        $auth->addChild($updateOwnGroup, $updateGroup);
        $auth->addChild($deleteOwnGroup, $deleteGroup);

        $auth->addChild($safeUser, $createGroup);
        $auth->addChild($safeUser, $updateOwnGroup);
        $auth->addChild($safeUser, $deleteOwnGroup);
        $auth->addChild($safeUser, $accessGroup);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->truncateTable('auth_item');
        $this->truncateTable('auth_item_child');
        $this->truncateTable('auth_item_rule');

        return false;
    }
}
