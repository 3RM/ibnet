<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\profile\Profile;
use common\models\User;
use common\models\Utility;
use common\models\missionary\MissionaryUpdate;
use common\models\network\IcalenderEvent;
use common\models\network\Network;
use common\models\network\NetworkIcalendarUrl;
use common\models\network\NetworkCalendarEvent;
use common\models\network\NetworkMember;
use common\models\network\NetworkKeyword;
use common\models\network\NetworkPlace;
use common\models\network\NetworkSearch;
use common\rbac\PermissionNetwork;
use common\models\network\Prayer;
use common\models\network\PrayerTag;
use common\models\network\AnswerSearch;
use common\models\network\PrayerSearch;
use common\models\network\PrayerUpdate;
use common\models\network\UpdateSearch;
use Dompdf\Dompdf;
use kartik\grid\GridView;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;


class NetworkController extends Controller
{
    /**
    * Used to pass network object to left menu layout
    **/
    public $network;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => [],
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['safeUser', 'Admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays network admin page
     *
     * @return mixed
     */
    public function actionMyNetworks()
    {
        if (isset($_POST['disable'])) {
            $network = Network::findOne($_POST['disable']);
            $network->inactivate();
            Yii::$app->session->setFlash('success', 'Your network ' . $network->name . ' has been disabled.');
        } elseif (isset($_POST['trash'])) {
            $network = Network::findOne($_POST['trash']);
            $network->trash();
            Yii::$app->session->setFlash('success', 'Your network ' . $network->name . ' has been deleted.');
        }

        $user = Yii::$app->user->identity;
        $ownNetworks = $user->ownNetworks;
        $ids = ArrayHelper::getColumn($ownNetworks, 'id');
        $joinedNetworks = $user->getJoinedNetworks($ids)->all();
        $networkSearch = new NetworkSearch();

        return $this->render('myNetworks', [
            'ownNetworks' => $ownNetworks,
            'joinedNetworks' => $joinedNetworks,
            'networkSearch' => $networkSearch,
        ]);
    }

    /**
     * Create a new network
     *
     * @return mixed
     */
    public function actionCreate()
    {
        if (!\Yii::$app->user->can(PermissionNetwork::CREATE)) {
            throw new NotFoundHttpException;
        }

        return $this->render('createNetwork', ['network' => $network]);
    }

    /**
     * Enter network title, description, and image
     *
     * @return mixed
     */
    public function actionNetworkInformation($id = NULL)
    {
        if ($id) {
            $network = Network::findOne($id);
            $network->scenario = 'update';
            if (!$network->canUpdateOwn()) {
                throw new NotFoundHttpException;
            }

        } elseif (\Yii::$app->user->can(PermissionNetwork::CREATE)) {
            $network = New Network();        
            $network->scenario = 'information';
            
        } else {
            throw new NotFoundHttpException;
        }

        // Ajax validation for unique network name
        if (Yii::$app->request->isAjax && $network->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($network);
        }

        if ($network->load(Yii::$app->request->Post()) && $network->handleFormInformation()) {
            return $this->redirect(['network-privacy', 'id' => $network->id]);

        } else {

            //Initialize select
            if ($profile = $network->ministry) {
                $initialData = [$profile->id => $profile->org_name];
            }
            return $this->render('networkInformation', [
                'network' => $network,
                'initialData' => $initialData
            ]);
        }
    }

    /**
     * Network privacy
     *
     * @return mixed
     */
    public function actionNetworkPrivacy($id)
    {
        $network = Network::findOne($id);
        $network->scenario = 'options';  
        if (!$network->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }  

        if ($network->load(Yii::$app->request->Post()) && $network->save()) {
            return $this->redirect(['network-location', 'id' => $network->id]);
        } else {
            return $this->render('networkPrivacy', ['network' => $network]);
        }
    }

    /**
     * Network location
     *
     * @return mixed
     */
    public function actionNetworkLocation($id)
    {
        $network = Network::findOne($id);
        $network->scenario = 'location';
        if (!$network->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }
        $place = new NetworkPlace();     
        $place->scenario = 'new';  
        $placeList = $network->places;
        $keyword = new NetworkKeyword();
        $keyword->scenario = 'new';
        $keywordList = $network->keywords; 

        if ($network->load(Yii::$app->request->Post()) && $network->save()) {
            return $this->redirect(['network-features', 'id' => $network->id]);
        } else {

            return $this->render('networkLocation', [
                'network' => $network, 
                'place' => $place,
                'placeList' => $placeList,
                'keyword' => $keyword,
                'keywordList' => $keywordList,
            ]);
        }
    }

    /**
     * Network features
     *
     * @return mixed
     */
    public function actionNetworkFeatures($id)
    {
        $network = Network::findOne($id);
        $network->scenario = 'features';
        if (!$network->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if ($network->load(Yii::$app->request->Post())) {
            
            if (1 != $network->feature_prayer
                && 1 != $network->feature_forum
                && 1 != $network->feature_calendar
                && 1 != $network->feature_notification
                && 1 != $network->feature_document
                && 1 != $network->feature_update
                && 1 != $network->feature_donation) {

                Yii::$app->session->setFlash('warning', Html::icon('warning-sign') . ' Please select at least one network feature.');
                return $this->redirect(['network-features', 'id' => $network->id]);

            } elseif ($network->validate()) {
                if (Network::STATUS_ACTIVE != $network->status) {
                    $network->status = Network::STATUS_ACTIVE;
                    Yii::$app->session->setFlash('success', 'Your network "' . $network->name . '" is now active.');
                }
                $network->save();
                return $this->redirect(['my-networks']);
            }
        }

        return $this->render('networkFeatures', ['network' => $network]);
    }

    /**
     * Manage network members
     *
     * @return mixed
     */
    public function actionNetworkMembers($id)
    {
        $network = Network::findOne($id);
        $members = $network->networkMembers;
        if (!$network->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }
        // if ($update->load(Yii::$app->request->post()) && $update->validate()) {
        //     $update->prayer_id = $rid;
        //     $update->save();
        //     $prayer = Prayer::findOne($rid);
        //     $prayer->scenario = 'update';
        //     $prayer->save();
        //     return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]); 
        
        // } else {
            return $this->render('members', []);
        // }
    }

    /**
     * Invite new network members
     *
     * @return mixed
     */
    public function actionInvite($id)
    {
        $network = Network::findOne($id);
        if (!$network->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        // $update = new PrayerUpdate();
        // if ($update->load(Yii::$app->request->post()) && $update->validate()) {
        //     $update->prayer_id = $rid;
        //     $update->save();
        //     $prayer = Prayer::findOne($rid);
        //     $prayer->scenario = 'update';
        //     $prayer->save();
        //     return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]); 
        
        // } else {
            return $this->renderAjax('_invite', []);
        // }
    }

    /**
     * Find another user in order to initiate a network transfer to them
     * @param string $id
     * @return mixed
     */
    public function actionTransfer($id) 
    {
        $network = Network::findOne($id); 
        $network->scenario = 'transfer';
        if (!$network->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if ($network->load(Yii::$app->request->Post())) {
            if (($oldUser = User::findOne(Yii::$app->user->identity->id))
                && ($newUser = User::findByEmail($network->newUserEmail))
                && $network->generateNetworkTransferToken($newUser->id)) {

                if ($oldUser->id == $newUser->id) {
                    Yii::$app->session->setFlash('warning', 'You cannot transfer the network to yourself.');
                    return $this->redirect(['transfer', 'id' => $id]);
                }

                // Send transfer request email to new user
                Yii::$app->mailer->compose(
                        ['html' => 'network/transfer-html', 'text' => 'network/transfer-text'], 
                        ['network' => $network, 'oldUser' => $oldUser]
                    )
                    ->setFrom([\yii::$app->params['email.admin']])
                    ->setTo([$newUser->email])
                    ->setSubject('IBNet Network Transfer Request')
                    ->send();
            }
            Yii::$app->session->setFlash('success', 'Your request was sent provided the email is registered with IBNet.  You will receive an email when the transfer is complete.');
        }
        return $this->render('transfer', ['network' => $network]);                              
    }

    /**
     * Landing page for network transfer completion
     * @param string $id
     * @return mixed
     */
    public function actionTransferComplete($id, $token) 
    {
        if (($network = Network::findOne($id))
            && $network->checkNetworkTransferToken($token)
            && ($oldUser = User::findOne($network->user_id))
            && ($newUserId = (int) substr($token, 0, strrpos($token, '+')))
            && ($newUserId != Yii::$app->user->identity->id)
            && ($newUser = User::findOne($newUserId))) {
                
            $network->updateAttributes(['user_id' => $newUser->id, 'transfer_token' => NULL]);

            // Create a new network member for newUser if one doesn't exist
            if (!NetworkMember::find()
                ->where(['network_id' => $network->id, 'user_id' => $newUser->id])
                ->exists()) {
                $networkMember = new NetworkMember();
                $networkMember->network_id = $network->id;
                $networkMember->user_id = $newUser->id;
                if ($profile = $newUser->indActiveProfile) {
                    $networkMember->profile_id = $profile->id;
                    if ('Missionary' == $profile->type) {
                        $networkMember->missionary_id = $profile->missionary->id;
                    }
                }
                $networkMember->validate();
                $networkMember->save();
            }

            // Send Email to old profile owner           
            Yii::$app->mailer->compose(
                    ['html' => 'network/transferComplete-html', 'text' => 'network/transferComplete-text'], 
                    ['network' => $network, 'newUser' => $newUser]
                )
                ->setFrom([\yii::$app->params['email.admin']])
                ->setTo([$oldUser->email])
                ->setSubject('IBNet Profile Transfer Complete')
                ->send();

            return $this->render('transferComplete', ['network' => $network]);

        } else {
            throw new NotFoundHttpException;
        }                              
    }

    /**
     * Network dashboard
     *
     * @return mixed
     */
    public function actionDashboard($id)
    {
        $this->layout = '//network/network-dashboard';
        $this->network = Network::findOne($id);
        if (!$this->network->canAccess()) {
            throw new NotFoundHttpException;
        }

        return $this->render('dashboard');
            // , ['network' => $this->network]);
    }

    /**
     * Prayer List feature
     *
     * @return mixed
     */
    public function actionPrayer($id, $pdf=NULL, $dspy=NULL, $f=NULL, $l=NULL) // $dspy=1 preserve new tag text on page reload
    {                                                                          // $f=1 ignore pagination and return the full list
        $this->layout = '//network/network-dashboard';                         // $l=NULL show prayer list; $l=1 show answer list
        $this->network = Network::findOne($id);
        if (!$this->network->canAccess()) {
            throw new NotFoundHttpException;
        }
        $user = Yii::$app->user->identity;
        $member = $this->network->networkMember;
        $prayer = new Prayer();
        $prayer->scenario = 'prayer';
        $tag = new PrayerTag();
        $tagList = $this->network->prayerTagList;
        $update = new PrayerUpdate();
        $answer = new Prayer();
        $answer->scenario = 'answer';
        $prayerNameList = $this->network->getPrayerListNames();

        if (isset($_POST['html'])) {

            $html = $_POST['html'];
            $session = Yii::$app->session;
            $session->open('html');
            $session->set('html', $html);

            // Set options
            $size = $_POST['size'] ? strtolower($_POST['size']) : 'letter';
            $session->open('size');
            $session->set('size', $size);

            return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]);
        }

        if ($prayer->load(Yii::$app->request->post()) && $prayer->validate()) {
            $uid = Yii::$app->user->identity->id;
            $prayer->network_id = $id;
            $prayer->network_member_id = NetworkMember::networkMemberId($id);
            $prayer->save();
            if ($prayer->select) {
                $prayer->handleTags();
            }
            return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]);  

        } elseif ($tag->load(Yii::$app->request->post()) && $tag->validate()) {
            $tag->network_id = $id;
            $tag->tag = strtolower($tag->tag);  // make all tags lower case for easier reference when emailing requests
            $tag->save();
            return $this->redirect(['prayer', 'id' => $id, 'dspy' => 1]);         
        }

        $searchModel = new PrayerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get(), $id, $f, $l);       

        return $this->render('prayer/prayer', [
            'network' => $this->network, 
            'member' => $member,
            'prayer' => $prayer,
            'tag' => $tag,
            'tagList' => $tagList,
            'update' => $update,
            'answer' => $answer,
            'dspy' => $dspy,
            'prayerNameList' => $prayerNameList,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'f' => $f,
            'l' => $l,
        ]);
    }

    /**
     * Render content for update prayer request modal
     *
     * @return mixed
     */
    public function actionUpdateRequest($id, $rid)
    {
        $network = Network::findOne($id);
        if (!$network->canAccess()) {
            throw new NotFoundHttpException;
        }

        $prayer = Prayer::findOne($rid);
        $prayer->scenario = 'update';
        $update = new PrayerUpdate();
        $update->select = $prayer->prayerTags;
        $tagList = $network->prayerTagList;

        if ($update->load(Yii::$app->request->post()) && $update->validate()) {

            if ($update->update == NULL && $update->select == NULL) {
                return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]); 
            }
            // Handle tags
            if (isset($update->select)) {
                $prayer->select = $update->select;
                $prayer->handleTags();
            }
            // Handle update
            if (isset($udpate->update)) {
                $update->prayer_id = $rid;
                $update->save();
            }
            // Always save prayer in order to update updated_at
            $prayer->save();
            return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]); 
        
        } else {
            return $this->renderAjax('prayer/_updateRequest', ['update' => $update, 'tagList' => $tagList]);
        }
    }

    /**
     * Render content for answer prayer request modal
     *
     * @return mixed
     */
    public function actionAnswerRequest($id, $rid)
    {
        $network = Network::findOne($id);
        if (!$network->canAccess()) {
            throw new NotFoundHttpException;
        }

        $request = Prayer::findOne($rid);
        $request->scenario = 'prayer';
        $answer = new Prayer();
        $answer->scenario = 'answer';
        if ($answer->load(Yii::$app->request->post()) && $answer->validate()) {
            $request->answer_description = $answer->answer_description;
            $request->answer_date = time();
            $request->answered = 1;
            $request->save();
            return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]); 
        
        }
        // Retain answer description if previously answered, then moved back to list
        $answer->answer_description = $request->answer_description;
        return $this->renderAjax('prayer/_answerRequest', ['answer' => $answer]);
    }

    /**
     * Live Chat feature
     *
     * @return mixed
     */
    public function actionChat($id)
    {
        $this->layout = '//network/network-dashboard';
        $this->network = Network::findOne($id);
        return $this->render('chat', ['network' => $this->network]);
    }

    /**
     * Discussion Forum and chat features
     *
     * @return mixed
     */
    public function actionDiscussion($id)
    {
        $this->layout = '//network/network-dashboard';
        $this->network = Network::findOne($id);
        return $this->render('discussion', ['network' => $this->network]);
    }

    /**
     * Calendar feature
     *
     * @return mixed
     */
    public function actionCalendar($id)
    {
        $this->layout = '//network/network-dashboard';
        $this->network = Network::findOne($id);
        if (!$this->network->canAccess()) {
            throw new NotFoundHttpException;
        }
        $eventList = NetworkCalendarEvent::allEvents($id);
        $icalList = $this->network->icalEvents;
        $upcomingList = NetworkCalendarEvent::upcomingEvents($id);
        return $this->render('calendar/calendar', [
            'network' => $this->network,
            'eventList' => $eventList,
            'icalList' => $icalList,
            'upcomingList' => $upcomingList,
            'urls' => empty($icalList) ? false : true,
        ]);
    }

    /**
     * Render content for new calendar event modal
     *
     * @return mixed
     */
    public function actionNewEvent($id)
    {
        $network = Network::findOne($id);
        if (!$network->canAccess()) {
            throw new NotFoundHttpException;
        }

        $event = new NetworkCalendarEvent();
        $event->network_id = $id;
        $nmid = NetworkMember::networkMemberId($id);

        if ($event->load(Yii::$app->request->post()) && $event->validate()) {
            $event->network_member_id = $nmid;
            $range = explode(' - ', $_POST['dateRange']);
            $event->start = strtotime($range[0]);
            $event->end = strtotime($range[1]);
            $item->end = $event->all_day ?
                strtotime($range[1]) + (24*3600) :
                strtotime($range[1]);
            $event->color = $_POST['color'];
            $event->save();
            return $this->redirect(['calendar', 'id' => $id]);
        } else {
            return $this->renderAjax('calendar/_eventForm', ['event' => $event]);
        }
    }

    /**
     * Render content for edit calendar event modal
     *
     * @return mixed
     */
    public function actionViewEvent($id, $eid, $resourceId)
    {
        $network = Network::findOne($id);
        if (!$network->canAccess()) {
            throw new NotFoundHttpException;
        }

        if ($resourceId == Network::RESOURCE_ICAL) {
            $ical = IcalenderEvent::findOne($eid);
            $viewEvent = new NetworkCalendarEvent();
            $viewEvent->title = $ical->SUMMARY;
            $viewEvent->start = $ical->DTSTART;
            $viewEvent->end = $ical->DTEND;
            $isOwner = 10;
        } else {
            $viewEvent = NetworkCalendarEvent::findOne($eid);
            $ownerId = $viewEvent->networkUser->id;
            $isOwner = ($ownerId == Yii::$app->user->identity->id);
            $isOwner = $isOwner ? 20 : 10;
        }
        $viewEvent->formatDateTimes($resourceId);
        return $this->renderAjax('calendar/_viewEvent', [
            'id' => $id, 
            'viewEvent' => $viewEvent, 
            'isOwner' => $isOwner,
            'resourceId' => $resourceId,
        ]);
    }

    /**
     * Remove a calendar event
     *
     * @return mixed
     */
    public function actionRemoveEvent()
    {
        if (isset($_POST['remove'])) {
            if ($event = NetworkCalendarEvent::findOne($_POST['remove'])) {
                $id = $event->network_id;
                $network = Network::findOne($id);
                if (!$network->canAccess()) {
                    throw new NotFoundHttpException;
                }
                $event->delete();
                return $this->redirect(['calendar', 'id' => $id]);
            }
        }
        throw new NotFoundHttpException;
    }

    /**
     * Render content for edit calendar event modal
     *
     * @return mixed
     */
    public function actionEditEvent($id, $eid)
    {
        $network = Network::findOne($id);
        if (!$network->canAccess()) {
            throw new NotFoundHttpException;
        }

        $event = NetworkCalendarEvent::findOne($eid);
        if ($event->load(Yii::$app->request->post()) && $event->validate()) {
            $range = explode(' - ', $_POST['dateRange']);
            $event->start = strtotime($range[0]);
            $event->end = $event->all_day ?
                strtotime($range[1]) + (24*3600) :
                strtotime($range[1]);
            $event->color = $_POST['color'];
            $event->save();
            return $this->redirect(['calendar', 'id' => $id]);
        } else {
            return $this->renderAjax('calendar/_eventForm', ['event' => $event]);
        }
    }

    /**
     * Render content for import calendar modal
     *
     * @return mixed
     */
    public function actionImportCalendar($id)
    {
        $network = Network::findOne($id);
        if (!$network->canAccess()) {
            throw new NotFoundHttpException;
        }
        $urlList = $network->owniCals;
        $ical = new NetworkIcalendarUrl();
        
        if ($ical->load(Yii::$app->request->post()) && $ical->validate()) {
            if (!empty($ical->url)) {
                // Check if url is already saved
                if (is_array($urlList)) {
                    foreach ($urlList as $url) {
                        $comp = strcmp($url->url, $ical->url);
                        $dup = $comp == 0 ? true : $dup;
                    }
                }
                if (isset($dup)) {
                    Yii::$app->session->setFlash('info', 
                        'The calendar has already been imported.');
                } else {
                    $ical->network_id = $id;
                    $ical->network_member_id = $network->networkMember->id;
                    $ical->color = $_POST['color'];
                    $ical->save();
                    Yii::$app->session->setFlash('success', 
                        'The url has been saved.  Your calendar should sync within 24 hours.');
                }
            }
            return $this->redirect(['calendar', 'id' => $id]);

        } else {

            return $this->renderAjax('calendar/_importCalendar', [
                'id' => $id, 
                'ical' => $ical, 
                'urlList' => $urlList
            ]);
        }
    }

    /**
     * Remove imported iCal calendar
     * 
     * @return mixed
     */
    public function actionRemoveIcal()
    {
        if (isset($_POST['remove'])) {     
            if ($url = NetworkIcalendarUrl::findOne($_POST['remove'])) {

                $network = Network::findOne($url->network_id);
                if (!$network->canAccess()) {
                    throw new NotFoundHttpException;
                }

                $url->updateAttributes(['deleted' => 1]);
                Yii::$app->session->setFlash('info', 'Your imported calendar was removed.');
                
                return $this->redirect(['calendar', 'id' => $url->network_id]);
            }
        }
        throw new NotFoundHttpException;
    }

    /**
     * Notifications feature
     *
     * @return mixed
     */
    public function actionNotification($id)
    {
        $this->layout = '//network/network-dashboard';
        $this->network = Network::findOne($id);
        if (!$network->canAccess()) {
            throw new NotFoundHttpException;
        }
        
        return $this->render('notification', ['network' => $this->network]);
    }

    /**
     * Document Library feature
     *
     * @return mixed
     */
    public function actionDocument($id)
    {
        $this->layout = '//network/network-dashboard';
        $this->network = Network::findOne($id);
        if (!$network->canAcess()) {
            throw new NotFoundHttpException;
        }

        return $this->render('document', ['network' => $this->network]);
    }

    /**
     * Missionary Updates feature
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->layout = '//network/network-dashboard';
        $this->network = Network::findOne($id);
        if (!$this->network->canAccess()) {
            throw new NotFoundHttpException;
        }
        $member = $this->network->networkMember;
        $updateSearchModel = new UpdateSearch();
        $updateDataProvider = $updateSearchModel->search(Yii::$app->request->get(), $this->network);
        $updateNameList = $this->network->getUpdateListNames();
        return $this->render('update/update', [
            'network' => $this->network,
            'member' => $member,
            'updateDataProvider' => $updateDataProvider,
            'updateSearchModel' => $updateSearchModel,
            'updateNameList' => $updateNameList,
        ]);
    }

    /**
     * Donations feature
     *
     * @return mixed
     */
    public function actionDonate($id)
    {
        $this->layout = '//network/network-dashboard';
        $this->network = Network::findOne($id);
        if (!$network->canAcess()) {
            throw new NotFoundHttpException;
        }
        return $this->render('donate', ['network' => $this->network]);
    }
}