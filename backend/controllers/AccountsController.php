<?php
namespace backend\controllers;

use backend\models\Assignment;
use backend\models\AssignmentSearch;
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
    public function actionAccountDetail($id)
    {
        $user = User::findOne($id);
        $church = $user->homeChurch ?? NULL;
        $profiles = Profile::find()->where(['id' => $user->id])->all();
        $comments = Comment::find()->where(['created_by' => $user->id])->count();
        $networks = NULL;

        return $this->renderAjax('_userDetail', [
            'user' => $user,
            'church' => $church,
            'profiles' => $profiles,
            'comments' => $comments,
            'networks' => $networks,
        ]);
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
        return $this->redirect(['users']);
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

    /**
     * Displays Update user account
     *
     * @return string
     */
    public function actionUpdate($id)
    {
        $user = User::findOne($id);
        $user->scenario = 'backend';

        if (isset($_POST['cancel'])) {
            return $this->redirect(['users']); 
        } elseif ($user->load(Yii::$app->request->Post())) {
            $user->email = ($user->email == '' || $user->email == NULL) ? NULL : $user->email;
            $user->validate();
            $user->save();
            Yii::$app->session->setFlash('success', 'User record has been updated.');

            return $this->redirect(['users']);
        }

        return $this->render('update', ['user' => $user]);
    }

    /**
     * Displays delete user account
     *
     * @return string
     */
    public function actionDelete($id)
    {
        if ($user = User::findOne($id)) {
            $user->delete();
        }
        return $this->redirect(['users']);
    }

}
