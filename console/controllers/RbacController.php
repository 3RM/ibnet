<?php
namespace console\controllers;

use \rmrevin\yii\module\Comments\Permission;
use \rmrevin\yii\module\Comments\rbac\ItsMyComment;
use yii\console\Controller;

/**
 * RBAC controller
 */
class RBacController extends Controller
{
    
    /**
     * Create permissions for comments module
     */
    public function actionInit() {

        $AuthManager = \Yii::$app->getAuthManager();
        $ItsMyCommentRule = new ItsMyComment();
        
        $AuthManager->add($ItsMyCommentRule);
        
        $AuthManager->add(new \yii\rbac\Permission([
            'name' => Permission::CREATE,
            'description' => 'Can create own comments',
        ]));
        $AuthManager->add(new \yii\rbac\Permission([
            'name' => Permission::UPDATE,
            'description' => 'Can update all comments',
        ]));
        $AuthManager->add(new \yii\rbac\Permission([
            'name' => Permission::UPDATE_OWN,
            'ruleName' => $ItsMyCommentRule->name,
            'description' => 'Can update own comments',
        ]));
        $AuthManager->add(new \yii\rbac\Permission([
            'name' => Permission::DELETE,
            'description' => 'Can delete all comments',
        ]));
        $AuthManager->add(new \yii\rbac\Permission([
            'name' => Permission::DELETE_OWN,
            'ruleName' => $ItsMyCommentRule->name,
            'description' => 'Can delete own comments',
        ]));
    }

    /**
     * Run SomeModel::some_method for today only as the default action
     * @return int exit code
     */
    public function actionIndex(){
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));
    }

    /**
     * Run SomeModel::some_method for yesterday
     * @return int exit code
     */
    public function actionYesterday(){
        return $this->actionInit(date("Y-m-d", strtotime("-1 days")), date("Y-m-d", strtotime("-1 days")));
    }
}
