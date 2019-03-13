<?php

use common\rbac\MyProfileRule;
use common\rbac\PermissionProfile;
use common\rbac\MyNetworkRule;
use common\rbac\MemberNetworkRule;
use common\rbac\PermissionNetwork;
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
         * Network Permissions
         *********************************/
        $myNetworkRule = new MyNetworkRule();
        $auth->add($myNetworkRule);

        $memberNetworkRule = new MemberNetworkRule();
        $auth->add($memberNetworkRule);

        $createNetwork = $auth->createPermission(PermissionNetwork::CREATE);
        $createNetwork->description = 'Can create own networks';
        $auth->add($createNetwork);

        $updateNetwork = $auth->createPermission(PermissionNetwork::UPDATE);
        $updateNetwork->description = 'Can update networks';
        $auth->add($updateNetwork);

        $updateOwnNetwork = $auth->createPermission(PermissionNetwork::UPDATE_OWN);
        $updateOwnNetwork->description = 'Can update own networks';
        $updateOwnNetwork->ruleName = $myNetworkRule->name;
        $auth->add($updateOwnNetwork);

        $deleteNetwork = $auth->createPermission(PermissionNetwork::DELETE);
        $deleteNetwork->description = 'Can delete networks';
        $auth->add($deleteNetwork);

        $deleteOwnNetwork = $auth->createPermission(PermissionNetwork::DELETE_OWN);
        $deleteOwnNetwork->description = 'Can delete own networks';
        $deleteOwnNetwork->ruleName = $myNetworkRule->name;
        $auth->add($deleteOwnNetwork);

        $accessNetwork = $auth->createPermission(PermissionNetwork::ACCESS);
        $accessNetwork->description = 'Can access networks';
        $accessNetwork->ruleName = $memberNetworkRule->name;
        $auth->add($accessNetwork);

        // Assign roles
        $auth->addChild($admin, $updateNetwork);
        $auth->addChild($admin, $deleteNetwork);

        $auth->addChild($updateOwnNetwork, $updateNetwork);
        $auth->addChild($deleteOwnNetwork, $deleteNetwork);

        $auth->addChild($safeUser, $createNetwork);
        $auth->addChild($safeUser, $updateOwnNetwork);
        $auth->addChild($safeUser, $deleteOwnNetwork);
        $auth->addChild($safeUser, $accessNetwork);
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
