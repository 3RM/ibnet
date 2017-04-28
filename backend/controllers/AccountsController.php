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
            'id', 
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
            ],
            'last_login',
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return Html::icon('eye-open');
                },
            ],
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return Html::icon('edit');
                },
            ],
        ];

        return $this->render('users', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays Assignments.
     *
     * @return string
     */
    public function actionReview($id)
    {
        $user = User::findOne($id);
        $user->updateAttributes(['reviewed' => 1]);
        return $this->redirect(['accounts']);
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
