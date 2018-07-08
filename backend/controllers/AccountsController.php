<?php
namespace backend\controllers;

use backend\models\Assignment;
use backend\models\AssignmentSearch;
use backend\models\Permission;
use backend\models\Role;
use backend\models\Rule;
use backend\models\UserSearch;
use common\models\Utility;
use common\models\User;
use kartik\grid\EditableColumnAction;
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
        $gridColumns = [
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review', 'id' => $model->id]);
                },
            ],
            [
                //'class'=>'kartik\grid\EditableColumn',
                'attribute' => 'id',
                'hAlign'=>'center',
                'vAlign' => 'middle',
                'width'=>'1%',
            ], 
            'first_name', 
            'last_name', 
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
            'username', 
            [
                'attribute' => 'email',
                'format' => 'raw',
                'value' => function ($model) {  
                    if ($model->email) {
                        return $model->email;
                    } elseif ($model->new_email) {
                        return '<span style="color:red">' . $model->new_email . '</span>';
                    }                
                    return Html::icon('eye-open');
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return Yii::$app->formatter->asDate($model->created_at, 'php:Y-m-d');
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
                'width'=>'8%',
                'headerOptions'=>['class'=>'kv-sticky-column'],
                'contentOptions'=>['class'=>'kv-sticky-column'],
            ],
            'last_login',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
            ],
        ];

        return $this->render('users', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
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
            'screen_name',
            'home_church',
            'role',
            'ind_act_profiles',
            'emailPrefLinks',
            'emailPrefComments',
            'emailPrefFeatures',
            'emailPrefBlog',
            'is_missionary',
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

    /**
     * Displays Auth Assignments.
     *
     * @return string
     */
    public function actionAssignmentq()
    {
        $searchModel = new AssignmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'user_id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'item_name',
                'editableOptions'=>[
                    'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                    'data' => ['User', 'Admin'],
                    'formOptions'=>['action' => ['updateUserRole']],
                ],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return Yii::$app->formatter->asDate($model->created_at, 'php:Y-m-d');
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
                'width'=>'8%',
                'headerOptions'=>['class'=>'kv-sticky-column'],
                'contentOptions'=>['class'=>'kv-sticky-column'],
            ],
        ];

        return $this->render('assignments', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays Auth Roles.
     *
     * @return string
     */
    public function actionRole()
    {
   
        $dataProvider = new ActiveDataProvider([
            'query' => Role::find(),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        // $gridColumns = [];

        return $this->render('roles', [
            'dataProvider' => $dataProvider,
            // 'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays permissions.
     *
     * @return string
     */
    public function actionPermission()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Permission::find(),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        // $gridColumns = [];

        return $this->render('permissions', [
            'dataProvider' => $dataProvider,
            // 'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays Rules.
     *
     * @return string
     */
    public function actionRule()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Rule::find(),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        // $gridColumns = [];

        return $this->render('rules', [
            'dataProvider' => $dataProvider,
            // 'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Update editable columns in Gridview widget
     *
     * @return string
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'updateUserRole' => [                                                                       // identifier for the editable action
                'class' => EditableColumnAction::className(),
                'modelClass' => Assignment::className(),
            ],
        ]);
    }
}
