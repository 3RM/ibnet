<?php
namespace backend\controllers;

use backend\models\GroupCalendarEventSearch;
use backend\models\GroupIcalendarUrlSearch;
use backend\models\GroupInviteSearch;
use backend\models\GroupKeywordSearch;
use backend\models\GroupMemberSearch;
use backend\models\GroupPlaceSearch;
use backend\models\GroupSearch;
use backend\models\GroupNotificationSearch;
use backend\models\PrayerSearch;
use backend\models\PrayerUpdateSearch;
use backend\models\PrayerTagSearch;
use common\models\Subscription;
use common\models\Utility;
use common\models\group\Group;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Site controller
 */
class GroupsController extends Controller
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionGroups()
    {
        $searchModel = new GroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        Url::remember();
        return $this->render('groups', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Mark a group as reviewed
     *
     * @return string
     */
    public function actionReview($id)
    {
        $model = Group::findOne($id);
        $model->updateAttributes(['reviewed' => 1]);
        return $this->redirect(Url::previous());
    }

    /**
     * Render content for group detail modal
     *
     * @return mixed
     */
    public function actionViewDetail($id)
    {
        $group = Group::findOne($id);
        $memberCount = count($group->groupMembers);
        return $this->renderAjax('_groupDetail', [
            'group' => $group,
            'memberCount' => $memberCount,
        ]);
    }

    /**
     * Render content for group edit modal
     *
     * @return mixed
     */
    public function actionViewEdit($id)
    {
        $group = Group::findOne($id);
        return $this->renderAjax('_groupEdit', ['group' => $group]);
    }

    /**
     * Render content for group inactivate modal
     *
     * @return mixed
     */
    public function actionViewInactivate($id)
    {
        $group = Group::findOne($id);
        return $this->renderAjax('_groupInactivate', ['group' => $group]);
    }

    /**
     * Render content for group trash modal
     *
     * @return mixed
     */
    public function actionViewTrash($id)
    {
        $group = Group::findOne($id);
        return $this->renderAjax('_groupTrash', ['group' => $group]);
    }

    /**
     * Update group
     *
     * @return string
     */
    public function actionUpdate()
    {
        if (isset($_POST['inactivate']) && $group = Group::findOne($_POST['inactivate'])) {
            $group->message = $_POST['Group']['message'] ?? NULL;
            if ($group->inactivate(true)) {
                Yii::$app->session->setFlash('success', 'Group ' . $group->id . ' was successfully inactivated.');
            } else {
                throw New ServerErrorHttpException;
            }
        
        } elseif (isset($_POST['trash']) && $group = Group::findOne($_POST['trash'])) {
            $group->message = $_POST['Group']['message'] ?? NULL;
            if ($group->trash(true)) {
                Yii::$app->session->setFlash('success', 'Group ' . $group->id . ' was successfully deleted.');
            } else {
                throw New ServerErrorHttpException;
            }
        
        } elseif (isset($_POST['save']) && $group = Group::findOne($_POST['save'])) {
            $group->scenario = 'backend';
            if ($group->load(Yii::$app->request->Post())
                && $group->validate()
                && $group->save()) {
                Yii::$app->session->setFlash('success', 'Record for Group ' . $group->id . ' has been updated.');
            } else {
                throw New ServerErrorHttpException;
            }

        } else {
            throw New ServerErrorHttpException;
        }

        return $this->redirect(Url::previous());
    }

    /**
     * Displays group_member table
     *
     * @return mixed
     */
    public function actionGroupMember()
    {
        $searchModel = new GroupMemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id', 
            'user_id', 
            'profile_id', 
            'missionary_id', 
            'group_owner', 
            'created_at', 
            'status', 
            'approval_date', 
            'inactivate_date', 
            'show_updates',
            [ 
                'attribute' => 'email_prayer_alert',
                'label' => 'Prayer Alert',
            ],
            [
                'attribute' => 'email_prayer_summary', 
                'label' => 'Prayer Weekly Summary',
            ],
            [
                'attribute' => 'email_update_alert',
                'label' => 'Update Alert',
            ],
        ];

        return $this->render('groupMember', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays prayer table
     *
     * @return mixed
     */
    public function actionPrayer()
    {
        $searchModel = new PrayerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id', 
            'group_member_id', 
            [
                'attribute' => 'request',
                'contentOptions' => ['style' => 'width:10%;'],
            ], 
            [
                'attribute' => 'description',
                'contentOptions' => ['style' => 'width:35%;'],
            ],
            'answered',
            [
                'attribute' => 'answer_description',
                'contentOptions' => ['style' => 'width:35%;'],
            ],
            'answer_date',
            'duration',
            'created_at',
            'last_update',
            'message_id',
            'deleted',
        ];

        return $this->render('prayer', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays prayer_update table
     *
     * @return mixed
     */
    public function actionPrayerUpdate()
    {
        $searchModel = new PrayerUpdateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'prayer_id', 
            [
                'attribute' => 'update',
                'contentOptions' => ['style' => 'width:35%;'],
            ], 
            'created_at',
            'deleted',
        ];

        return $this->render('prayerUpdate', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays prayer_tag table
     *
     * @return mixed
     */
    public function actionPrayerTag()
    {
        $searchModel = new PrayerTagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id', 
            'tag',
            'deleted',
        ];

        return $this->render('prayerTag', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays group_calendar_event table
     *
     * @return mixed
     */
    public function actionCalendarEvent()
    {
        $searchModel = new GroupCalendarEventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id', 
            'group_member_id', 
            'title', 
            'color', 
            [
                'attribute' => 'description',
                'contentOptions' => ['style' => 'width:25%;'],
            ], 
            'created_at', 
            'start', 
            'end', 
            'all_day', 
            'deleted',
        ];

        return $this->render('calendarEvent', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays group_icalendar_url table
     *
     * @return mixed
     */
    public function actionIcalendarUrl()
    {
        $searchModel = new GroupIcalendarUrlSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id', 
            'group_member_id', 
            'ical_id', 
            'url', 
            'color', 
            'error_on_import', 
            'deleted',
        ];

        return $this->render('icalendarUrl', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays group_notification table
     *
     * @return mixed
     */
    public function actionNotification()
    {
        $searchModel = new GroupNotificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id', 
            'user_id', 
            'created_at', 
            'reply_to',
            [
                'attribute' => 'subject',
                'contentOptions' => ['style' => 'width:10%;'],
            ], 
            [
                'attribute' => 'message',
                'contentOptions' => ['style' => 'width:50%;'],
            ],
        ];

        return $this->render('notification', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays group_place table
     *
     * @return mixed
     */
    public function actionGroupPlace()
    {
        $searchModel = new GroupPlaceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id', 
            'city', 
            'state', 
            'country',
            'deleted',
        ];

        return $this->render('groupPlace', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays group_keyword table
     *
     * @return mixed
     */
    public function actionGroupKeyword()
    {
        $searchModel = new GroupKeywordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id', 
            'keyword', 
            'deleted',
        ];

        return $this->render('groupKeyword', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays group_invite table
     *
     * @return mixed
     */
    public function actionGroupInvite()
    {
        $searchModel = new GroupInviteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $gridColumns = [
            'id', 
            'group_id',
            'email', 
            'created_at', 
            'token',
        ];

        return $this->render('groupInvite', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Displays forwarding email requests
     *
     * @return mixed
     */
    public function actionPendingEmails()
    {
        $group = new Group();
        $group->scenario = 'backend-emails-pending';

        // Ajax validation for unique group name
        if (Yii::$app->request->isAjax && $group->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($group);
        }

        if (isset($_POST['save']) && $group = Group::findOne($_POST['save'])) {
            $group->scenario = 'backend-emails-pending';
            if ($group->load(Yii::$app->request->post())) { 
                // Notify group owner
                $mail = Subscription::getSubscriptionByEmail($group->owner->email) ?? new Subscription();
                $mail->headerColor = Subscription::COLOR_GROUP;
                $mail->headerImage = Subscription::IMAGE_GROUP;
                $mail->headerText = 'Email Setup Complete';
                $mail->to = $group->owner->email;
                $mail->subject = 'IBNet Group Emails';
                $mail->title = 'Group Email Setup Complete';
                if (($group->prayer_email != $group->getOldAttribute('prayer_email'))
                    && ($group->prayer_email_pwd != $group->getOldAttribute('prayer_email_pwd'))
                    && ($group->notice_email != $group->getOldAttribute('notice_email'))
                    && ($group->notice_email_pwd != $group->getOldAttribute('notice_email_pwd'))) {
                    $mail->message = 'Two new emails have been setup for your group ' . $group->name . '. ' . 
                        'Group members can email prayer requests, updates, and answers to ' . $group->prayer_email . '. 
                        This is a quick way to add or update requests on the group prayer list without logging into the 
                        IBNet website. Also, group members can email ' . $group->notice_email . ' to send a notification
                        to the group without logging into IBNet.';
                } elseif (($group->prayer_email != $group->getOldAttribute('prayer_email'))
                    && ($group->prayer_email_pwd != $group->getOldAttribute('prayer_email_pwd'))) {
                    $mail->message = 'A new email has been setup for your group ' . $group->name . '. ' . 
                        'Group members can email prayer requests, updates, and answers to ' . $group->prayer_email . '. 
                        This is a quick way to add or update requests on the group prayer list without logging into the 
                        IBNet website.';
                } elseif (($group->notice_email != $group->getOldAttribute('notice_email'))
                    && ($group->notice_email_pwd != $group->getOldAttribute('notice_email_pwd'))) {
                    $mail->message = 'A new email has been setup for your group ' . $group->name . '. ' . 
                        'Group members can email ' . $group->notice_email . ' to send a notification
                        to the group without logging into IBNet.';
                }
                $mail->sendNotification();
                $group->save();
                Yii::$app->session->setFlash('success', 'The data was saved and the group owner has been notified of the new email addresses.');
            }
        }

        $groups = Group::find()
            ->where(['and',
                ['feature_prayer' => 1],
                ['prayer_email' => NULL],
                ['prayer_email_pwd' => NULL],
            ])
            ->orWhere(['and',
                ['feature_notification' => 1],
                ['notice_email' => NULL],
                ['notice_email_pwd' => NULL],
            ])
            ->andWhere(['status' => Group::STATUS_ACTIVE])
            ->all();
        foreach($groups as $group) {
            $group->scenario = 'backend-emails-pending';
            if ($group->feature_prayer) {
                if (empty($group->prayer_email) || empty($group->prayer_email_pwd)) {
                    $group->prayer_email = 'prayer.' . $group->url_name . '@ibnet.org';
                    $group->prayer_email_pwd = Utility::generateUniqueRandomString($group, 'prayer_email_pwd', 20);
                } else {
                    $group->prayerIsSet = 1;
                }
            }
            if ($group->feature_notification) {
                if (empty($group->notice_email) || empty($group->notice_email_pwd)) {
                    $group->notice_email = 'notice.' . $group->url_name  . '@ibnet.org';
                    $group->notice_email_pwd = Utility::generateUniqueRandomString($group, 'notice_email_pwd', 20);
                } else {
                    $group->noticeIsSet = 1;
                }
            }
        }

        return $this->render('pendingEmails', ['groups' => $groups]);
    }
}
