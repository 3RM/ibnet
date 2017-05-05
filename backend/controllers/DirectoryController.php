<?php
namespace backend\controllers;

use backend\models\ProfileSearch;
use common\models\Utility;
use common\models\profile\Profile;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\Query;
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
                'hAlign'=>'center',
                'vAlign' => 'middle',
                'width'=>'1%',
            ], 
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . Html::a($model->user_id, ['accounts/view', 'id' => $model->user_id]) . '</span>' :
                        Html::a($model->user_id, ['accounts/view', 'id' => $model->user_id]);
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
                'width'=>'1%',
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
                    } elseif ($model->status == Profile::STATUS_TRASH) {
                        return '<span style="color: #CCC;">Trash</span>';    
                    }             
                },
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => 'Actions',
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
     * Displays review user account
     *
     * @return string
     */
    public function actionView($id)
    {
        $model = Profile::findOne($id);
        $attributes = [
            'id',
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return Html::a($model->user_id, ['accounts/view', 'id' => $model->user_id]);
                },
            ],
            'transfer_token',
            'type',
            'sub_type',
            'profile_name',
            'url_name',
            'url_city',
            'created_at:date',                                     
            'last_update:date',
            'last_modified:date',
            'renewal_date',
            'inactivation_date',
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
                    } elseif ($model->status == Profile::STATUS_TRASH) {
                        return '<span style="color: #CCC;">Trash</span>';    
                    }             
                },
            ],
            'tagline',
            'title',
            'description',
            'ministry_of',
            'home_church',
            'image1',
            'image2',
            'flwsp_ass_level',
            'org_name',
            'org_address1',
            'org_address2',
            'org_po_box',
            'org_city',
            'org_st_prov_reg',
            'org_zip',
            'org_country',
            'org_loc',
            'org_po_address1',
            'org_po_address2',
            'org_po_city',
            'org_po_st_prov_reg',
            'org_po_state_long',
            'org_po_zip',
            'org_po_country',
            'ind_first_name',
            'ind_last_name',
            'spouse_first_name',
            'ind_address1',
            'ind_address2',
            'ind_po_box',
            'ind_city',
            'ind_st_prov_reg',
            'ind_state_long',
            'ind_zip',
            'ind_country',
            'ind_loc',
            'ind_po_address1',
            'ind_po_address2',
            'ind_po_city',
            'ind_po_st_prov_reg',
            'ind_po_state_long',
            'ind_po_zip',
            'ind_po_country',
            [
                'attribute' => 'show_map',
                'format' => 'raw',
                'value' => function ($model) {  
                    if ($model->status == Profile::MAP_PRIMARY) {
                        return 'Primary';
                    } elseif ($model->status == Profile::MAP_CHURCH) {
                        return 'Church';
                    } elseif ($model->status == Profile::MAP_MINISTRY) {
                        return 'Ministry';  
                    } elseif ($model->status == Profile::MAP_CHURCH_PLANT) {
                        return 'Church Plant';    
                    }             
                },
            ],
            'phone',
            'email',
            'email_pvt',
            'email_pvt_status',
            'website',
            'pastor_interim',
            'cp_pastor',
            'bible',
            'worship_style',
            'polity',
            'packet',
            'inappropriate',
            'service_time_id',
            'social_id',
            'flwship_id',
            'ass_id',
            'miss_housing_id',
            'missionary_id'
        ];
        
        return $this->render('view', [
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Displays Assignments.
     *
     * @return string
     */
    public function actionFlagged()
    {
        $query = (new Query())->from('profile')->where(['inappropriate' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $gridColumns = [
            'id',
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return Html::a($model['user_id'], ['accounts/view', 'id' => $model['user_id']]);
                },
            ],
            'type',
            'org_name',
            'ind_last_name',
            'created_at',
            'last_update',
            'renewal_date',
            'inactivation_date',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {  
                    if ($model['status'] == Profile::STATUS_NEW) {
                        return '<span style="color:blue">New</span>';
                    } elseif ($model['status'] == Profile::STATUS_ACTIVE) {
                        return '<span style="color:green">Active</span>';
                    } elseif ($model['status'] == Profile::STATUS_INACTIVE) {
                        return '<span style="color: orange;">Inactive</span>';  
                    } else {
                        return '<span style="color: #CCC;">Trash</span>';    
                    }             
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
            ],
        ];

        return $this->render('flagged', [ 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }
}
