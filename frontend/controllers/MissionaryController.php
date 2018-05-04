<?php
namespace frontend\controllers;

use common\models\Utility;
use common\models\missionary\MailchimpList;
use common\models\missionary\Missionary;
use common\models\missionary\MissionaryUpdate;
use common\models\profile\Profile;
use common\models\profile\Mail;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\JsExpression;
use yii\web\NotFoundHttpException;

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
                'except' => ['update', 'chimp-request', 'new-feature'],
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
     * Video to introduce Missionary Update Feature
     */
    public function actionNewFeature()
    {
        return $this->render('updateFeature');
    }

    /**
     * Display Missionary Report Repository Admin page
     *
     * @return mixed
     */
    public function actionUpdateRepository($a = NULL)                                               // $a stores link anchor
    {
        $user = Yii::$app->user->identity;
        if (!$user->is_missionary) {
            $this->redirect('/site/settings');
        }

        $profile = Profile::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['type' => 'Missionary'])
            ->andWhere('`status` != ' . Profile::STATUS_TRASH)
            ->andWhere('`status` != ' . Profile::STATUS_NEW)
            ->one();
        $profileActive = $profile->status == Profile::STATUS_ACTIVE ? true : false;
        $missionary = $profile->missionary;
        $updates = $missionary->update;
        $newUpdate = New MissionaryUpdate(); 

        if (isset($_POST['remove'])) {
            $update = MissionaryUpdate::findOne($_POST['remove']);
            $update->updateAttributes(['deleted' => 1]);
            $updates = $missionary->update;

        } elseif (isset($_POST['edit'])) {
            $i = 0;
            foreach ($updates as $update) {
                if ($update->id == $_POST['edit']) {
                    $updates[$i]['edit'] = 1;
                    $a = $update->id;     
                    $update->editActive = 12;                                                          
                }
                $i++;
            }

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

        } elseif (isset($_POST['new_url'])) {
            $missionary->generateRepositoryKey();
            Yii::$app->session->setFlash('success', 'A new Url has been created.  
                    You can rest assured that your updates are secure.');
            $a = NULL;

        } elseif ($newUpdate->load(Yii::$app->request->Post())) {
            $newUpdate->missionary_id = $missionary->id;
            $newUpdate->handleForm();
            return $this->redirect(['update-repository', 'a' => $newUpdate->id]);
        }

        $newUpdate->active = 12;                                                                    // Initialize attributes
        $newUpdate->title = date('F') . ' Update';
        $repo_url = Url::toRoute(['/missionary/update/', 
            'repository_key' => $missionary->repository_key, 
            'id' => $profile->id], 'https');
        $active = $profile->status == Profile::STATUS_ACTIVE ? True : False;
        $mcSynced = $missionary->mc_token ? true : false;

        return $this->render('repositoryAdmin', [
            'profileActive' => $profileActive,
            'missionary' => $missionary,
            'repo_url' => $repo_url,
            'updates' => $updates, 
            'newUpdate' => $newUpdate,
            'active' => $active,
            'mcSynced' => $mcSynced,
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
        $this->layout = 'main-nofollow';                                                            // Instruct search engines not to list page
        $profile = Profile::findOne($id);
        $missionary = $profile->missionary;
        if ($missionary->repository_key == $repository_key) {

            $this->layout="bg-gray";
            $profile->getformattedNames();
            $updates = $missionary->getUpdate();

            foreach ($updates as $update) {
                if ($update->vimeo_url) {
                    $url = 'https://vimeo.com/api/oembed.json?url=' . $update->vimeo_url;
                    $res = Utility::get($url);
                    $decoded = json_decode($res);
                    $update->videoHtml = $decoded->html;
                } elseif ($update->youtube_url) {
                    $url = 'http://www.youtube.com/oembed?url=' . $update->youtube_url . '&format=json';
                    $res = Utility::get($url);
                    $decoded = json_decode($res);
                    $update->videoHtml = $decoded->html;
                }
            }

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
     * Setup MailChimp OAuth2
     * Client logs into Mailchimp and is redirected to mailchimp-step2
     *
     * @return mixed
     */
    public function actionMailchimpStep1()
    {
        $user = Yii::$app->user->identity;
        if (!$user->is_missionary) {
            $this->redirect('/site/settings');
        }

        $profile = Profile::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['type' => 'Missionary'])
            ->one();
        $missionary = $profile->missionary;

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
        if (!Yii::$app->user->identity->is_missionary) {
            $this->redirect('/site/settings');
        }

        $code = Yii::$app->getRequest()->get('code');
        $oauthClient = new \common\components\Mailchimp();
        $oauthClient->setReturnUrl(Url::toRoute(['missionary/mailchimp-complete'], 'https'));
        $accessToken = $oauthClient->fetchAccessToken($code);
        $token = $oauthClient->getAccessToken()->token;
        $dc = $oauthClient->userAttributes['dc'];

        $profile = Profile::find()                                                                  // Store token-datacenter to db
            ->where(['user_id' => Yii::$app->user->identity->id])
            ->andWhere(['type' => 'Missionary'])
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
        if (!$user->is_missionary) {
            $this->redirect('/site/settings');
        }

        $profile = Profile::find()
            ->where(['user_id' => Yii::$app->user->identity->id])
            ->andWhere(['type' => 'Missionary'])
            ->one();
        $missionary = $profile->missionary;
        $mcList = new MailchimpList();                                                              // Generic model for capturing user input
    
        if ($mcList->load(Yii::$app->request->Post())) {
            $missionary->deleteAllMCWebhooks();
            foreach ($mcList->select as $listId) {
                $missionary->setMCWebhook($listId);
            }
            $msg = '<h4>Mailchimp setup is complete.  Now enjoy hands-free updates to your IBNet 
                updates page.</h4>';
        }

        if ($res = $missionary->getMCLists()) {                                                     // Request mailing lists from Mailchimp
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
        if ($request->isPost &&
            ($request->userAgent == 'MailChimp.com') &&
            ($request->getBodyParam('type') == 'campaign') &&
            ($missionary = Missionary::findOne($id)) && 
            ($mc_key == $missionary->mc_key)) {
            $profile = $missionary->profile;
            $campaign = $missionary->getMCCampaign($_POST['data']['id']);
            $update = New MissionaryUpdate();
            $update->missionary_id = $missionary->id;
            $update->title = $campaign->settings->title;
            $update->description = $campaign->settings->subject_line;
            $update->mailchimp_url = $campaign->archive_url;
            $update->from_date = new Expression('CURDATE()');
            $update->to_date = new Expression('DATE_ADD(CURDATE(), INTERVAL 1 YEAR)');
            $update->profile_inactive = ($profile->status == Profile::STATUS_INACTIVE ? 1 : 0);     // If profile is not active, save mailchimp upate but mark inactive
            if ($update->validate()) {
                $update->save();
            }
            $user = $profile->user;
            Mail::sendMailchimp($user->email, $missionary->repository_key, $missionary->id);        // email user       
        }
        die;
    }
}
