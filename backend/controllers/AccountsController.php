<?php
namespace backend\controllers;

use backend\models\UserSearch;
use common\models\Utility;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
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
                    return $model->status == User::STATUS_ACTIVE ?
                        '<span style="color:green">Active</span>' :
                        '<span style="color: #222222;">Deleted</span>';                 
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
            'emailPrefProfile',
            'emailPrefLinks',
            'emailPrefFeatures',
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
    public function actionUpdate()
    {
        return $this->render('update');
    }

    /**
     * Displays delete user account
     *
     * @return string
     */
    public function actionDelete()
    {
        return $this->render('delete');
    }

    /**
     * Displays Assignments.
     *
     * @return string
     */
    public function actionAssignments()
    {
        return $this->render('assignments');
    }

    /**
     * Displays Roles.
     *
     * @return string
     */
    public function actionRoles()
    {
        return $this->render('roles');
    }

    /**
     * Displays permissions.
     *
     * @return string
     */
    public function actionPermissions()
    {
        return $this->render('permissions');
    }

    /**
     * Displays Rules.
     *
     * @return string
     */
    public function actionRules()
    {
        return $this->render('rules');
    }
}
