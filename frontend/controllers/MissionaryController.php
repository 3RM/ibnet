<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */
 
namespace frontend\controllers;

use common\models\Utility;
use common\models\group\Group;
use common\models\missionary\MailchimpList;
use common\models\missionary\Missionary;
use common\models\missionary\MissionaryUpdate;
use common\models\profile\Profile;
use common\models\profile\ProfileMail;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\JsExpression;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Missionary controller
 */
class MissionaryController extends Controller
{
    public $layout;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['update', 'chimp-request'],
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Disable csrf validation for chimp-request action to allow for incoming post request
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['chimp-request'])) {
            $this->enableCsrfValidation = 0;
        }
        return parent::beforeAction($action);
    }

    /**
     * Display Missionary Report Repository Admin page
     *
     * @return mixed
     */
    public function actionUpdateRepository($a = NULL) // $a stores link anchor
    {
        $user = Yii::$app->user->identity;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id))[0];
        if (!$user->isMissionary) {
            $this->redirect('/site/settings');
        }

        if (!$profile = Profile::find()
            ->where(['and', 
                ['user_id' => $user->id],
                ['!=', 'status', Profile::STATUS_TRASH],
                ['!=', 'status', Profile::STATUS_NEW]
            ])
            ->andWhere(['or',
                ['type' => Profile::TYPE_MISSIONARY],
                ['type' => Profile::TYPE_CHAPLAIN],
            ])
            ->one()) 
        {
            throw new NotFoundHttpException;
        }
        $profileActive = $profile->status == Profile::STATUS_ACTIVE ? true : false;
        $missionary = $profile->missionary;
        $updates = $missionary->updatesAll;
        $newUpdate = New MissionaryUpdate(); 

        // Ajax validation of Google Drive url
        if (Yii::$app->request->isAjax && $newUpdate->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($newUpdate);
        }

        // Remove an udpate
        if (isset($_POST['remove'])) {
            $update = MissionaryUpdate::findOne($_POST['remove']);
            $update->updateAttributes(['deleted' => 1]);
            if ($alert = $update->groupAlert) {
                $alert->delete();
            }
            $updates = $missionary->updates;

        // Show update edit form
        } elseif (isset($_POST['edit'])) {
            foreach ($updates as $i=>$update) {
                if ($update->id == $_POST['edit']) {
                    $updates[$i]['edit'] = 1;
                    $a = $update->id;     
                    $update->editActive = 12;                                                          
                }
                $i++;
            }

        // Save an edit
        } elseif (isset($_POST['edit-save'])) {
            $update = MissionaryUpdate::findOne($_POST['edit-save']);
            if ($update->load(Yii::$app->request->Post())) {
                $update->edit = 1;
                $update->handleForm();
                return $this->redirect(['update-repository', 'a' => $update->id]);
            } else {
                Yii::$app->session->setFlash('danger', 'OOps.  Something went wrong.  
                    Try making your edit again.');
            }

        // Generate a new repository url
        } elseif (isset($_POST['new_url'])) {
            $missionary->generateRepositoryKey();
            Yii::$app->session->setFlash('success', 'A new Url has been created.  
                    You can rest assured that your updates are secure.');
            $a = NULL;

        // New update
        } elseif ($newUpdate->load(Yii::$app->request->Post())) {
            $newUpdate->missionary_id = $missionary->id;
            $newUpdate->handleForm();
            return $this->redirect(['update-repository', 'a' => $newUpdate->id]);
        }

        // Initialize attributes
        $newUpdate->active = 12;
        $newUpdate->title = date('F') . ' Update';
        $repo_url = Url::toRoute(['/missionary/update/', 
            'repository_key' => $missionary->repository_key, 
            'id' => $profile->id], 'https');
        $active = $profile->status == Profile::STATUS_ACTIVE ? true : false;
        $mcSynced = $missionary->mc_token ? true : false;
        $displayNone = $missionary->viewed_update ? 'style="display:none"' : NULL;

        $joinedGroups = $user->joinedGroups;

        Url::Remember();
        return $this->render('repositoryAdmin', [
            'profileActive' => $profileActive,
            'missionary' => $missionary,
            'repo_url' => $repo_url,
            'updates' => $updates, 
            'newUpdate' => $newUpdate,
            'joinedGroups' => $joinedGroups,
            'role' => $role,
            'active' => $active,
            'mcSynced' => $mcSynced,
            'displayNone' => $displayNone,
            'a' => $a,
        ]);
    }

    /**
     * Display public missionary report page.
     *
     * @return mixed
     */
    public function actionUpdate($repository_key, $id)
    {
        // Instruct search engines not to list page
        $this->layout = 'main-nofollow';
        $profile = Profile::findOne($id);
        $missionary = $profile->missionary;
        if ($missionary->repository_key == $repository_key) {

            $this->layout="bg-gray";
            $updates = $missionary->updates;

            return $this->render('updates', [
                'profile' => $profile,
                'missionary' => $missionary,
                'updates' => $updates,
            ]);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Watch a video update linked from alart email
     * @param  $gid integer Group id
     * @param  $uid integer Update id
     * @return mixed
     */
    public function actionWatch($gid, $uid)
    {
        $group = Group::findOne($gid);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        $update = MissionaryUpdate::findOne($uid);
        $missionary = $update->missionary;
        $profile = $missionary->profile;
        
        return $this->render('watch', [
            'profile' => $profile,
            'missionary' => $missionary,
            'update' => $update,
        ]);
    }

    /**
     * Setup MailChimp OAuth2
     * Client logs into Mailchimp and is redirected to mailchimp-step2
     *
     * @return mixed
     */
    public function actionMailchimpStep1()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isMissionary) {
            $this->redirect('/site/settings');
        }

        $profile = Profile::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['type' => 'Missionary'])
            ->one();
        $missionary = $profile->missionary;

        $unsynced = false;
        if (Yii::$app->request->post()) {
            if ($missionary->unsyncMC()) {
                $unsynced = true;
            }
        }

        $synced = $missionary->mc_token ? true : false;

        return $this->render('mailchimpStep1', ['synced' => $synced, 'unsynced' => $unsynced]);
    }

    /**
     * Begin Mailchimp OAuth2 authorize
     *
     * @return mixed
     */
    public function actionMailchimpAuthorize()
    {
        $oauthClient = new \common\components\Mailchimp();
        $oauthClient->setReturnUrl(Url::toRoute(['missionary/mailchimp-complete'], 'https'));
        $url = $oauthClient->buildAuthUrl();
        $response = Yii::$app->getResponse()->redirect($url);

        return true;
    }

    /**
     * Redirect URI for MailChimp OAuth2
     *
     * @return mixed
     */
    public function actionMailchimpComplete()
    {
        if (!Yii::$app->user->identity->isMissionary) {
            $this->redirect('/site/settings');
        }

        $code = Yii::$app->getRequest()->get('code');
        $oauthClient = new \common\components\Mailchimp();
        $oauthClient->setReturnUrl(Url::toRoute(['missionary/mailchimp-complete'], 'https'));
        $accessToken = $oauthClient->fetchAccessToken($code);
        $token = $oauthClient->getAccessToken()->token;
        $dc = $oauthClient->userAttributes['dc'];

        // Store token-datacenter to db
        $profile = Profile::find()
            ->where(['user_id' => Yii::$app->user->identity->id])
            ->andWhere(['or',
                ['type' => Profile::TYPE_MISSIONARY],
                ['type' => Profile::TYPE_CHAPLAIN]
            ])
            ->one();
        $missionary = $profile->missionary;
        $missionary->updateAttributes(['mc_token' => $token . '-' . $dc]);

        return $this->redirect(['mailchimp-step2']);  
    }

    /**
     * Setup Mailchimp webhook
     *
     * @return mixed
     */
    public function actionMailchimpStep2()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isMissionary) {
            $this->redirect('/site/settings');
        }

        $profile = Profile::find()
            ->where(['user_id' => Yii::$app->user->identity->id])
            ->andWhere(['or',
                ['type' => Profile::TYPE_MISSIONARY],
                ['type' => Profile::TYPE_CHAPLAIN]
            ])
            ->one();
        $missionary = $profile->missionary;
        // Generic model for capturing user input
        $mcList = new MailchimpList();
        
        $msg = NULL;
        if ($mcList->load(Yii::$app->request->Post())) {
            $missionary->deleteAllMCWebhooks();
            foreach ($mcList->select as $listId) {
                $missionary->setMCWebhook($listId);
            }
            $msg = '<h4>Mailchimp setup is complete.  Now enjoy hands-free updates to your IBNet 
                updates page.</h4>';
        }

        // Request mailing lists from Mailchimp
        $listArray = NULL;
        if ($res = $missionary->getMCLists()) {
            $listArray = \yii\helpers\ArrayHelper::map($res, 'id', 'name');
        } else {
            $msg = '<h4>You don\'t have any mailing lists.  Login to Mailchimp
                and create at least one mailing list.  Then try setting up Mailchimp again.<h4>';
        }

        return $this->render('mailchimpStep2', [
            'missionary' => $missionary,
            'mcList' => $mcList,
            'listArray' => $listArray,
            'msg' => $msg,
        ]);        
    }

    /**
     * Url for Mailchimp webhook
     * When Mailchimp notifies of sent campaign, save relevant data in db for posting to update page
     */
    public function actionChimpRequest($id, $mc_key)
    {  
        $request = Yii::$app->request;
        if ($request->post() 
            && ($request->userAgent == 'MailChimp') 
            && ($request->getBodyParam('type') == 'campaign')
            && ($missionary = Missionary::findOne($id)) 
            && ($mc_key == $missionary->mc_key)) {
            $profile = $missionary->profile;
            $campaign = $missionary->getMCCampaign($_POST['data']['id']);
            $update = New MissionaryUpdate();
            $update->missionary_id = $missionary->id;
            $update->title = $campaign->settings->title;
            $update->description = $campaign->settings->subject_line;
            $update->mailchimp_url = $campaign->archive_url;
            $update->from_date = new Expression('CURDATE()');
            $update->to_date = new Expression('DATE_ADD(CURDATE(), INTERVAL 1 YEAR)');
            // If profile is not active, save mailchimp upate but mark inactive
            $update->profile_inactive = ($profile->status == Profile::STATUS_INACTIVE ? 1 : 0);
            if ($update->validate()) {
                $update->save();
            }
            // email user  
            $user = $profile->user;
            ProfileMail::sendMailchimp($user->email, $missionary->repository_key, $missionary->id);     
        }       
        die;
    }
}
