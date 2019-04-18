<?php
namespace backend\controllers;

use backend\models\BanMeta;
use backend\models\ProfileSearch;
use backend\models\SocialSearch;
use backend\models\StaffSearch;
use backend\models\MissionarySearch;
use backend\models\MissionaryUpdateSearch;
use backend\models\HistorySearch;
use backend\models\HousingSearch;
use backend\models\AssociationSearch;
use backend\models\FellowshipSearch;
use common\models\Utility;
use common\models\profile\Profile;
use common\models\profile\ProfileMail;
use common\models\profile\Social;
use frontend\controllers\ProfileController;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

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
     * Displays a detail view of single profile.
     *
     * @return string
     */
    public function actionProfiles()
    {
        $searchModel = new ProfileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('profiles', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
        ]);
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
        return $this->redirect(Url::previous());
    }

    /**
     * Render content for profile detail modal
     *
     * @return mixed
     */
    public function actionViewDetail($id)
    {
        $profile = Profile::findOne($id);
        return $this->renderAjax('_profileDetail', ['profile' => $profile]);
    }

    /**
     * Render content for profile edit modal
     *
     * @return mixed
     */
    public function actionViewEdit($id)
    {
        $profile = Profile::findOne($id);
        return $this->renderAjax('_profileEdit', ['profile' => $profile]);
    }

    /**
     * Render content for profile inactivate modal
     *
     * @return mixed
     */
    public function actionViewInactivate($id)
    {
        $profile = Profile::findOne($id);
        return $this->renderAjax('_profileInactivate', ['profile' => $profile]);
    }

    /**
     * Render content for profile trash modal
     *
     * @return mixed
     */
    public function actionViewTrash($id)
    {
        $profile = Profile::findOne($id);
        return $this->renderAjax('_profileTrash', ['profile' => $profile]);
    }

    /**
     * Render content for profile flag modal
     *
     * @return mixed
     */
    public function actionViewFlag($id)
    {
        $profile = Profile::findOne($id);
        return $this->renderAjax('_profileFlag', ['profile' => $profile]);
    }

    /**
     * Update profile
     *
     * @return string
     */
    public function actionUpdate()
    {
        if (isset($_POST['inactivate']) && $profile = Profile::findOne($_POST['inactivate'])) {
            $status = $profile->status;
            if ($profile->inactivate()) {
                $status == Profile::STATUS_TRASH ?
                    Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' was successfully restored.') :
                    Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' was successfully inactivated.');
            } else {
                throw New ServerErrorHttpException;
            }
        
        } elseif (isset($_POST['trash']) && $profile = Profile::findOne($_POST['trash'])) {
            if ($profile->trash()) {
                Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' was successfully deleted.');
            } else {
                throw New ServerErrorHttpException;
            }

        } elseif (isset($_POST['restore']) && $profile = Profile::findOne($_POST['restore'])) {
            if ($profile->inactivate(TRUE)) {
                Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' was successfully restored.');
            } else {
                throw New ServerErrorHttpException;
            }

        } elseif (isset($_POST['flag']) && $profile = Profile::findOne($_POST['flag'])) {
            $profile->updateAttributes(['inappropriate' => 1]);
            Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' was has been flagged for review.');
            
        
        } elseif (isset($_POST['save']) && $profile = Profile::findOne($_POST['save'])) {
            $profile->scenario = 'backend';
            if ($profile->load(Yii::$app->request->Post())
                && $profile->validate()
                && $profile->save()) {
                Yii::$app->session->setFlash('success', 'Record for Profile ' . $profile->id . ' has been updated.');
            } else {
                throw New ServerErrorHttpException;
            }

        } else {
            throw New ServerErrorHttpException;
        }

        return $this->redirect(Url::previous());
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
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return $model->reviewed === 1 ? '' : Html::a(Html::icon('check'), ['review-staff', 'id' => $model->id]);
                },
            ],
            'id',
            'sermonaudio',
            'facebook',
            'linkedin',
            'twitter',
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
     * Mark a social as reviewed
     *
     * @return string
     */
    public function actionReviewSocial($id)
    {
        $model = Social::findOne($id);
        $model->updateAttributes(['reviewed' => 1]);
        return $this->redirect(Url::previous());
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
            'id',
            'staff_id',
            // [
            //     'attribute' => 'staff_id',
            //     'format' => 'raw',
            //     'value' => function ($model) {                      
            //         return Html::a($model->staff_id, ['view', 'id' => $model->staff_id]);
            //     },
            // ],
            'staff_type',
            'staff_title',
            'ministry_id',
            // [
            //     'attribute' => 'ministry_id',
            //     'format' => 'raw',
            //     'value' => function ($model) {                      
            //         return Html::a($model->ministry_id, ['view', 'id' => $model->ministry_id]);
            //     },
            // ],
            'home_church', 
            'church_pastor', 
            'ministry_of',
            'ministry_other', 
            'sr_pastor', 
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
            'id',
            'name',
            'acronym',
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
            'id',
            'name',
            'acronym',
            'profile_id',
        ];

        return $this->render('fellowship', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays history table
     *
     * @return string
     */
    public function actionHistory()
    {
        $searchModel = new HistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id',
            'profile_id',
            'date',
            'title',
            [
                'attribute' => 'description',
                'contentOptions' => ['style' => 'width:35%;'],
            ],
            'event_image',
            'deleted',
        ];

        return $this->render('history', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Render content for profile ban modal
     *
     * @return mixed
     */
    public function actionViewRestore($id)
    {
        $profile = Profile::findOne($id);
        $profile->scenario = 'backend-flagged';
        return $this->renderAjax('_profileBan', ['profile' => $profile]);
    }

    /**
     * Render content for profile ban/restore modal
     *
     * @return mixed
     */
    public function actionViewBan($id)
    {
        $profile = Profile::findOne($id);
        $profile->scenario = 'backend-flagged';
        return $this->renderAjax('_profileBan', ['profile' => $profile]);
    }

    /**
     * Render content for ban history modal
     *
     * @return mixed
     */
    public function actionViewHistory($id)
    {
        $profile = Profile::findOne($id);
        $history = $profile->banMeta;
        return $this->renderAjax('_banHistory', ['profile' => $profile, 'history' => $history]);
    }

    /**
     * Displays flagged profiles
     *
     * @return mixed
     */
    public function actionFlagged()
    {  
        if (isset($_POST['clear']) && $profile = Profile::findOne($_POST['clear'])) {
            $profile->updateAttributes(['inappropriate' => NULL]);
            Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' flag has been cleared.');
        
        } elseif (isset($_POST['ban']) && $profile = Profile::findOne($_POST['ban'])) {
            $profile->scenario = 'backend-flagged'; 
            if ($profile->load(Yii::$app->request->Post()) && $profile->ban()) {
                Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' was successfully banned.');
            } else {
                Yii::$app->session->setFlash('warning', 'Something went wrong and the record was not saved. Note that description is a required field.');
            }

        } elseif (isset($_POST['restore']) && $profile = Profile::findOne($_POST['restore'])) {
            $profile->scenario = 'backend';
            if ($profile->load(Yii::$app->request->Post()) && $profile->restore()) {      
                Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' was successfully restored.');
            } else {
                Yii::$app->session->setFlash('warning', 'Something went wrong and the record was not saved. Note that description is a required field.');
            }

        } elseif (isset($_POST['delete']) && $profile = Profile::findOne($_POST['delete'])) {
            if ($profile->hardDelete()) {
                Yii::$app->session->setFlash('success', 'Profile ' . $profile->id . ' was has been permanently deleted.');
            } else {
                throw new \yii\web\ServerErrorHttpException;
            }        
        }

        $flaggedProfiles = Profile::find()->where(['inappropriate' => 1])->all();
        $bannedProfiles = Profile::find()->where(['status' => Profile::STATUS_BANNED])->all();

        return $this->render('flagged', [
            'flaggedProfiles' => $flaggedProfiles,
            'bannedProfiles' => $bannedProfiles
        ]);
    }

    /**
     * Displays forwarding email requests
     *
     * @return mixed
     */
    public function actionForwarding()
    {
        if (isset($_POST['save']) && $profile = Profile::findOne($_POST['save'])) {
            // Send request to admin
            if (!ProfileMail::sendForwardingEmailNotif($profile->email)) {
                throw new \yii\web\ServerErrorHttpException;
            }
            $profile->updateAttributes(['email_pvt_status' => Profile::PRIVATE_EMAIL_ACTIVE]);
            Yii::$app->session->setFlash('success', 'Private email status has been set to <i>Active</i> for 
                    profile ' . $profile->id . ' and a notification email has been sent to the user.');
        
        } elseif (isset($_POST['remove']) && $profile = Profile::findOne($_POST['remove'])) {
            $profile->updateAttributes(['email_pvt' => NULL, 'email_pvt_status' => NULL]);
            Yii::$app->session->setFlash('success', 'Private email has been canceled for profile ' . 
                $profile->id . '. "status" and "email_pvt" were set to NULL.');
        
        }

        $profiles = Profile::find()->where(['email_pvt_status' => Profile::PRIVATE_EMAIL_PENDING])->all();
        foreach($profiles as $profile) {
            $profile->scenario = 'co-befe';
        }

        return $this->render('forwarding', ['profiles' => $profiles]);
    }
}
