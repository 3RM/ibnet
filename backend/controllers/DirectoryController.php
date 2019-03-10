<?php
namespace backend\controllers;

use backend\models\ProfileSearch;
use backend\models\SocialSearch;
use backend\models\StaffSearch;
use backend\models\MissionarySearch;
use backend\models\HousingSearch;
use backend\models\AssociationSearch;
use backend\models\FellowshipSearch;
use common\models\Utility;
use common\models\missionary\Missionary;
use common\models\profile\Association;
use common\models\profile\Fellowship;
use common\models\profile\MissHousing;
use common\models\profile\Profile;
use common\models\profile\ProfileMail;
use common\models\profile\Social;
use common\models\profile\Staff;
use frontend\controllers\ProfileController;
use kartik\grid\EditableColumnAction;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Accounts controller
 */
class DirectoryController extends Controller
{
    const CLASS_PROFILE = 'profile';              // Used for dynamic classes
    const CLASS_STAFF = 'staff';
    const CLASS_MISSIONARY = 'missionary';
    const CLASS_HOUSING = 'housing';
    const CLASS_ASSOCIATION = 'association';
    const CLASS_FELLOWSHIP = 'fellowship';

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
     * Mark a profile as reviewed
     *
     * @return string
     */
    public function actionReviewProfile($id)
    {
        $model = Profile::findOne($id);
        $model->updateAttributes(['reviewed' => 1]);
        return $this->redirect(['profiles']);
    }

    /**
     * Mark staff as reviewed
     *
     * @return string
     */
    public function actionReviewStaff($id)
    {
        $model = Staff::findOne($id);
        $model->updateAttributes(['reviewed' => 1]);
        return $this->redirect(['staff']);
    }

    /**
     * Mark missionary as reviewed
     *
     * @return string
     */
    public function actionReviewMiss($id)
    {
        $model = Missionary::findOne($id);
        $model->updateAttributes(['reviewed' => 1]);
        return $this->redirect(['missionary']);
    }

    /**
     * Mark missionary housing as reviewed
     *
     * @return string
     */
    public function actionReviewHousing($id)
    {
        $model = MissHousing::findOne($id);
        $model->updateAttributes(['reviewed' => 1]);
        return $this->redirect(['housing']);
    }

    /**
     * Mark association as reviewed
     *
     * @return string
     */
    public function actionReviewAss($id)
    {
        $model = Association::findOne($id);
        $model->updateAttributes(['reviewed' => 1]);
        return $this->redirect(['association']);
    }

    /**
     * Mark fellowship as reviewed
     *
     * @return string
     */
    public function actionReviewFlwship($id)
    {
        $model = Fellowship::findOne($id);
        $model->updateAttributes(['reviewed' => 1]);
        return $this->redirect(['fellowship']);
    }

    /**
     * Displays a detail view of single profile.
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
                    return $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-profile', 'id' => $model->id]);
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
            // [
            //     'attribute' => 'last_update',
            //     'format' => 'raw',
            //     'value' => function ($model) {                      
            //         return $model->status === Profile::STATUS_TRASH ? 
            //             '<span style="color: #CCC;">' . $model->last_update . '</span>' : 
            //             $model->last_update;
            //     }, 
            // ],
            [
                'attribute' => 'renewal_date',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->status === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model->renewal_date . '</span>' : 
                        $model->renewal_date;
                }, 
            ],
            // [
            //     'attribute' => 'inactivation_date',
            //     'format' => 'raw',
            //     'value' => function ($model) {                      
            //         return $model->status === Profile::STATUS_TRASH ? 
            //             '<span style="color: #CCC;">' . $model->inactivation_date . '</span>' : 
            //             $model->inactivation_date;
            //     }, 
            // ],
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
                    } elseif ($model->status == Profile::STATUS_EXPIRED) {
                        return '<span style="color: red;">Expired</span>';  
                    } elseif ($model->status == Profile::STATUS_TRASH) {
                        return '<span style="color: #CCC;">Trash</span>';    
                    }             
                },
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'header' => 'Actions',
                'deleteOptions' => ['label' => '', 'icon' => '']
            ],
        ];

        return $this->render('profiles', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
            'page' => $page,
        ]);
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
                     return Html::a($model->user_id, ['accounts/view', 'id' => $model->user_id], ['target' => '_blank']);
                },
            ],
            'transfer_token',
            'type',
            'sub_type',
            'profile_name',
            'url_name',
            'url_loc',
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
                    } elseif ($model->status == Profile::STATUS_EXPIRED) {
                        return '<span style="color: red;">Expired</span>';  
                    } elseif ($model->status == Profile::STATUS_TRASH) {
                        return '<span style="color: #CCC;">Trash</span>';    
                    }             
                },
            ],
            'tagline',
            'title',
            'description',
            'ministry_of',
            [
                'attribute' => 'home_church',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return Html::a($model->home_church, ['view', 'id' => $model->home_church], ['target' => '_blank']);
                }, 
            ],
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
        ];
        
        return $this->render('view', [
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Displays social table
     *
     * @return string
     */
    public function actionSocial()
    {
        $searchModel = new SocialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id',
            'sermonaudio',
            'facebook',
            'linkedin',
            'twitter',
            'google',
            'rss',
            'youtube',
            'vimeo',
            'pinterest',
            'tumblr',
            'soundcloud',
            'instagram',
            'flickr'
        ];

        return $this->render('social', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays staff table
     *
     * @return string
     */
    public function actionStaff()
    {
        $searchModel = new StaffSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-staff', 'id' => $model->id]);
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
                'width'=>'1%',
            ],
            [
                'attribute' => 'staff_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return Html::a($model->staff_id, ['view', 'id' => $model->staff_id]);
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
                //'width'=>'1%',
            ],
            'staff_type',
            'staff_title',
            [
                'attribute' => 'ministry_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return Html::a($model->ministry_id, ['view', 'id' => $model->ministry_id]);
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
                //'width'=>'1%',
            ],
            'home_church', 
            'church_pastor', 
            'ministry_of',
            'ministry_other', 
            'sr_pastor', 
            'confirmed',
        ];

        return $this->render('staff', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays missionary table
     *
     * @return string
     */
    public function actionMissionary()
    {
        $searchModel = new MissionarySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            [
                'attribute' => 'profile_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return Html::a($model->profile->id, ['/directory/view', 'id' => $model->profile->id]);
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
            ],
            'id',
            'mission_agcy_id',
            'field',
            'status',
            'packet',
            'cp_pastor_at',
        ];

        return $this->render('missionary', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays missionary table
     *
     * @return string
     */
    public function actionMissionaryUpdate()
    {
        $searchModel = new MissionaryUpdateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id',
            'missionary_id',
            'created_at',
            'title',
            'mailchimp_url',
            'pdf',
            'youtube_url',
            'vimeo_url',
            'description',
            'from_date',
            'to_date',
        ];

        return $this->render('missionaryUpdate', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays missions housing table
     *
     * @return string
     */
    public function actionHousing()
    {
        $searchModel = new HousingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id',
            'description',
            'contact',
            'trailer',
            'water',
            'electric',
            'sewage',
        ];

        return $this->render('housing', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays associations table
     *
     * @return string
     */
    public function actionAssociation()
    {
        $searchModel = new AssociationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-ass', 'id' => $model->id]);
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
                'width'=>'1%',
            ],
            'id',
            'association',
            'association_acronym',
            'profile_id',
        ];

        return $this->render('association', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays fellowship table
     *
     * @return string
     */
    public function actionFellowship()
    {
        $searchModel = new FellowshipSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-flwship', 'id' => $model->id]);
                },
                'hAlign'=>'center',
                'vAlign' => 'middle',
                'width'=>'1%',
            ],
            'id',
            'fellowship',
            'fellowship_acronym',
            'profile_id',
        ];

        return $this->render('fellowship', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays flagged profiles
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
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model['status'] == Profile::STATUS_ACTIVE ? 
                        Html::a(Html::icon('new-window'), ['frontend/profile/view-profile-by-id', 'id' => $model['id']], ['target' => '_blank']) :
                        '';
                },
            ],
            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model['status'] === Profile::STATUS_TRASH ? 
                        '<span style="color: #CCC;">' . $model['id'] . '</span>' : 
                        $model['id'];
                }, 
                'hAlign'=>'center',
                'width'=>'1%',
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return Html::a($model['user_id'], ['accounts/view', 'id' => $model['user_id']]);
                },
                'hAlign'=>'center',
                'width'=>'1%'
            ],
            'type',
            'org_name',
            'ind_last_name',
            'created_at',
            'last_update',
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
                    } elseif ($model->status == Profile::STATUS_EXPIRED) {
                        return '<span style="color: red;">Expired</span>';  
                    } else {
                        return '<span style="color: #CCC;">Trash</span>';    
                    }             
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'template' => '{clear} {disable}',
                'buttons' =>
                [
                    'clear' => function ($url, $model, $key) {
                        return Html::a(Html::icon('check'), ['clear-flag', 'id' => $model['id']]);
                    },
                    'disable' => function ($url, $model, $key) {
                        return Html::a(Html::icon('ban-circle'), ['disable-profile', 'id' => $model['id']]);
                    }
                ],
            ],
        ];

        return $this->render('flagged', [ 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Clear a profile flag
     *
     * @return string
     */
    public function actionClearFlag($id)
    {
        if ($profile = Profile::findOne($id)) {
            $profile->updateAttributes(['inappropriate' => NULL]);
        }

        return $this->redirect(['flagged']);
    }

    /**
     * Disable a flagged profile
     *
     * @return string
     */
    public function actionDisableProfile($id)
    {
        if ($profile = Profile::findOne($id)) {
            $profile->inactivate();
        }

        return $this->redirect(['flagged']);
    }

    /**
     * Displays forwarding email requests
     *
     * @return string
     */
    public function actionForwarding()
    {

       $query = Profile::find()
            ->where(['email_pvt_status' => Profile::PRIVATE_EMAIL_PENDING])
            ->indexBy('id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        foreach($dataProvider->getModels() as $model) {                                             // Set model scenarios
            $model->scenario = 'co-befe';
        }
        $gridColumns = [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return Html::a($model['id'], ['view', 'id' => $model['id']]);
                },
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model) {                      
                     return Html::a($model['user_id'], ['accounts/view', 'id' => $model['user_id']]);
                },
            ],
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
                    } elseif ($model->status == Profile::STATUS_EXPIRED) {
                        return '<span style="color: red;">Expired</span>';  
                    } else {
                        return '<span style="color: #CCC;">Trash</span>';    
                    }             
                },
            ],
            'type',
            'org_name',
            'ind_last_name',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'email',
                'editableOptions'=>[
                    'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                    'formOptions'=>['action' => ['updateForward']],
                ],
            ],
            'email_pvt',
            'email_pvt_status',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'template' => '{activate} {cancel}',
                'buttons' =>
                [
                    'activate' => function ($url, $model, $key) {
                        return Html::a(Html::icon('check'), ['activate-forward', 'id' => $model['id']]);
                    },
                    'cancel' => function ($url, $model, $key) {
                        return Html::a(Html::icon('unchecked'), ['cancel-forward', 'id' => $model['id']]);
                    }
                ],
            ],
        ];

        
        return $this->render('forwarding', [
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Activate a private email & send new forwarding email request notification to admin
     *
     * @return string
     */
    public function actionActivateForward($id)
    {
        $profile = Profile::findOne($id);
        if ($profile) {
            $profile->updateAttributes(['email_pvt_status' => Profile::PRIVATE_EMAIL_ACTIVE]);
        }

        if (ProfileMail::sendForwardingEmailNotif($profile->email)) {                                      // Send request to admin
            Yii::$app->session->setFlash('success', 
                'Private email status has been set to <i>Active</i> for profile ' . $profile->id . ' and a notification email has 
                been sent to the user.');
        }

        return $this->redirect(['forwarding']);
    }

    /**
     * Activate a private email & send new forwarding email request notification to admin
     *
     * @return string
     */
    public function actionCancelForward($id)
    {
        $profile = Profile::findOne($id);
        if ($profile) {
            $profile->updateAttributes(['email_pvt' => NULL, 'email_pvt_status' => NULL]);

            Yii::$app->session->setFlash('success', 
                'Private email has been canceled for profile ' . $profile->id . '. "status" and "email_pvt" were set 
                to NULL.');
        }

        return $this->redirect(['forwarding']);
    }

    /**
     * Update editable columns in Gridview widget
     *
     * @return string
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'updateForward' => [                                                                    // identifier for the editable action
                'class' => EditableColumnAction::className(),
                'modelClass' => Profile::className(),
            ],
        ]);
    }
}
