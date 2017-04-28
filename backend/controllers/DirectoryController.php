<?php
namespace backend\controllers;

use backend\models\ProfileSearch;
use common\models\Utility;
use common\models\profile\Profile;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Accounts controller
 */
class DirectoryController extends Controller
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
    public function actionProfiles()
    {
        $searchModel = new ProfileSearch();
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
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model->id . '</span>' : 
                        $model->id;
                }, 
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . Html::a($model->user_id, ['accounts/review', 'id' => $model->user_id]) . '</span>' :
                        Html::a($model->user_id, ['accounts/review', 'id' => $model->user_id]);
                },
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model->type . '</span>' : 
                        $model->type;
                }, 
            ],
            // [
            //     'attribute' => 'profile_name',
            //     'format' => 'raw',
            //     'value' => function ($model) {                      
            //         return $model->status === Profile::STATUS_TRASH ? 
            //             '<span style="color: #CCC;">' . $model->profile_name . '</span>' : 
            //             $model->profile_name;
            //     }, 
            // ],
            [
                'attribute' => 'org_name',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model->org_name . '</span>' : 
                        $model->org_name;
                }, 
            ],
            [
                'attribute' => 'ind_last_name',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->status === Profile::STATUS_TRASH) {                      
                        return $model->spouse_first_name ? 
                            '<span style="color: #CCC;">' . $model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name . '</span>' : 
                            '<span style="color: #CCC;">' . $model->ind_first_name . ' ' . $model->ind_last_name . '</span>';
                    } else {
                        return $model->spouse_first_name ? 
                            $model->ind_first_name . ' & ' . $model->spouse_first_name . ' ' . $model->ind_last_name : 
                            $model->ind_first_name . ' ' . $model->ind_last_name;
                    }
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model->created_at . '</span>' : 
                        $model->created_at;
                }, 
            ],
            [
                'attribute' => 'last_update',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model->last_update . '</span>' : 
                        $model->last_update;
                }, 
            ],
            [
                'attribute' => 'renewal_date',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model->renewal_date . '</span>' : 
                        $model->renewal_date;
                }, 
            ],
            [
                'attribute' => 'inactivation_date',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model->inactivation_date . '</span>' : 
                        $model->inactivation_date;
                }, 
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {  
                    if ($model->status == Profile::STATUS_NEW) {
                        return '<span style="color:blue">New</span>';
                    } elseif ($model->status == Profile::STATUS_ACTIVE) {
                        return '<span style="color:green">Active</span>';
                    } elseif ($model->status == Profile::STATUS_INACTIVE) {
                        return '<span style="color: orange;">Inactive</span>';  
                    } else {
                        return '<span style="color: #CCC;">Trash</span>';    
                    }             
                },
            ],
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

        return $this->render('profiles', [
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
        $user = Profile::findOne($id);
        $user->updateAttributes(['reviewed' => 1]);
        return $this->redirect(['profiles']);
    }

    /**
     * Displays Assignments.
     *
     * @return string
     */
    public function actionFlagged()
    {
        $dataProvider = Profile::find()->where('inappropriate > 0')->all();
        $gridColumns = [
            'id',
        ];

        return $this->render('flagged', [ 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }
}
