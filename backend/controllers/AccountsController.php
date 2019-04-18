<?php
namespace backend\controllers;

use backend\models\Assignment;
use backend\models\AssignmentSearch;
use backend\models\BanMeta;
use backend\models\Comment;
use backend\models\Permission;
use backend\models\Role;
use backend\models\Rule;
use backend\models\UserSearch;
use common\models\profile\Profile;
use common\models\Utility;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Accounts controller
 */
class AccountsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays Accounts.
     *
     * @return string
     */
    public function actionUsers()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('users', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Render content for user account detail modal
     *
     * @return mixed
     */
    public function actionViewDetail($id)
    {
        $user = User::findOne($id);
        $church = $user->homeChurch ?? NULL;
        $profiles = Profile::find()->where(['user_id' => $user->id])->all();
        $comments = Comment::find()->where(['created_by' => $user->id])->count();
        $hasBanHistory = BanMeta::find()->where(['user_id' => $user->id])->exists();
        $networks = NULL;

        return $this->renderAjax('_userDetail', [
            'user' => $user,
            'church' => $church,
            'profiles' => $profiles,
            'comments' => $comments,
            'hasBanHistory' => $hasBanHistory,
            'networks' => $networks,
        ]);
    }

    /**
     * Render content for User edit modal
     *
     * @return mixed
     */
    public function actionViewEdit($id)
    {
        $user = User::findOne($id);
        return $this->renderAjax('_userEdit', ['user' => $user]);
    }

    /**
     * Render content for User ban/restore modal
     *
     * @return mixed
     */
    public function actionViewBan($id)
    {
        $user = User::findOne($id);
        $user->scenario = 'backend-banned';
        return $this->renderAjax('_userBan', ['user' => $user]);
    }

    /**
     * Render content for ban history modal
     *
     * @return mixed
     */
    public function actionViewHistory($id)
    {
        $user = User::findOne($id);
        $history = $user->banMeta;
        return $this->renderAjax('_banHistory', ['user' => $user, 'history' => $history]);
    }

    /**
     * Render content for User auth assignment modal
     *
     * @return mixed
     */
    public function actionViewRole($id)
    {
        $user = User::findOne($id);
        $user->select = array_keys(Yii::$app->authManager->getRolesByUser($user->id))[0];
        return $this->renderAjax('_userRole', ['user' => $user]);
    }

    /**
     * Update user account
     *
     * @return string
     */
    public function actionUpdate()
    {
        $user = NULL; 

        // Ban user
        if (isset($_POST['ban']) && $user = User::findOne($_POST['ban'])) {
            $user->scenario = 'backend-banned';
            if ($user->load(Yii::$app->request->Post()) && $user->ban()) {
                Yii::$app->session->setFlash('success', 'User ' . $user->id . ' has been banned.');
            } else {
                throw New \yii\web\ServerErrorHttpException;
            }

        // Restore user account after ban
        } elseif (isset($_POST['restore']) && $user = User::findOne($_POST['restore'])) {
            $user->scenario = 'backend-banned';
            if ($user->load(Yii::$app->request->Post()) && $user->restore()) {
                Yii::$app->session->setFlash('success', 'Account for user ' . $user->id . ' has been reactivated.');
            } else {
                throw New \yii\web\ServerErrorHttpException;
            }


        // Change user rbac role
        } elseif (isset($_POST['role'])) {

            $user = User::findOne($_POST['role']);
            $user->scenario = 'backend';
            $user->load(Yii::$app->request->Post());

            $role = array_keys(Yii::$app->authManager->getRolesByUser($user->id))[0];
            if ($role != $user->select) {
                // Revoke current role
                $auth = Yii::$app->authManager;
                $item = $auth->getRole($role);
                $auth->revoke($item, $user->id);  
                // Set new role      
                $auth = Yii::$app->authManager;
                $userRole = $auth->getRole($user->select);
                $auth->assign($userRole, $user->id);
            }


        // Save changes
        } elseif (isset($_POST['save'])) {

            $user = User::findOne($_POST['save']);
            $user->load(Yii::$app->request->Post());
            $user->scenario = 'backend';
            $user->validate();
            $user->save();

            Yii::$app->session->setFlash('success', 'Record for User ' . $user->id . ' has been updated.');

        } else {
            Yii::$app->session->setFlash('warning', 'An error occurred.  The record was not updated.');
        }

        return $this->redirect(Url::previous());
    }

    /**
     * Mark a model as reviewed
     *
     * @return string
     */
    public function actionReview($id)
    {
        $user = User::findOne($id);
        $user->updateAttributes(['reviewed' => 1]);
        return $this->redirect(Url::previous());
    }

    /**
     * Displays review user account
     *
     * @return string
     */
    public function actionView($id)
    {
        $model = User::findOne($id);
        $attributes = [
            'id',
            'first_name',
            'last_name',
            'email',
            'new_email',
            'new_email_token',
            'username',
            'auth_key',
            'password_reset_token',                                     
            'created_at:date',
            'updated_at:date',
            'last_login',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {  
                    if ($model->status == User::STATUS_DELETED) {
                        return '<span style="color:orange">Deleted</span>';
                    } elseif ($model->status == User::STATUS_ACTIVE) {
                        return '<span style="color:green">Active</span>';
                    } elseif ($model->status == User::STATUS_BANNED) {
                        return '<span style="color:red">Banned</span>';  
                    }             
                },
            ],
            'usr_image',
            'display_name',
            'home_church',
            'primary_role',
            'emailPrefLinks',
            'emailPrefComments',
            'emailPrefFeatures',
            'emailPrefBlog',
            'reviewed',
        ];
        
        return $this->render('view', [
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

}
