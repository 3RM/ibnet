<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\Subscription;
use common\models\User;
use common\models\missionary\MissionaryUpdate; use common\models\Utility;
use common\models\group\IcalenderEvent;
use common\models\group\Group;
use common\models\group\GroupIcalendarUrl;
use common\models\group\GroupCalendarEvent;
use common\models\group\GroupInvite;
use common\models\group\GroupMember;
use common\models\group\GroupKeyword;
use common\models\group\GroupPlace;
use common\models\group\GroupSearch;
use common\models\group\MemberSearch;
use common\models\group\Prayer;
use common\models\group\GroupAlertQueue;
use common\models\group\PrayerTag;
use common\models\group\AnswerSearch;
use common\models\group\PrayerSearch;
use common\models\group\PrayerUpdate;
use common\models\group\UpdateSearch;
use common\rbac\PermissionGroup;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\widgets\ActiveForm;


class GroupController extends Controller
{
    /**
    * Used to pass group object to left menu layout
    **/
    public $group;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['invite-join'],
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_SAFEUSER, User::ROLE_ADMIN],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays group admin page
     *
     * @return mixed
     */
    public function actionMyGroups()
    {
        if (isset($_POST['disable'])) {
            $group = Group::findOne($_POST['disable']);
            $group->inactivate();
            Yii::$app->session->setFlash('success', 'Your group ' . $group->name . ' has been disabled.');
        } elseif (isset($_POST['trash'])) {
            $group = Group::findOne($_POST['trash']);
            $group->trash();
            Yii::$app->session->setFlash('success', 'Your group ' . $group->name . ' has been deleted.');
        } elseif (isset($_POST['join'])) {
            $group = Group::findOne($_POST['join']);
            GroupMember::joinGroup($_POST['join']);
            $group->private ?
                Yii::$app->session->setFlash('success', 'Your request to join ' . $group->name . ' has been submitted.') :
                Yii::$app->session->setFlash('success', 'You are now a member of ' . $group->name . '.');
        } elseif (isset($_POST['leave'])) {
            $group = Group::findOne($_POST['leave']);
            GroupMember::leaveGroup($_POST['leave']);
            Yii::$app->session->setFlash('success', 'You have left ' . $group->name . '.');
        }

        // Solr group search
        $groupSearch = new GroupSearch();
        $dataProvider = NULL;
        if ($groupSearch->load(Yii::$app->request->Post())) {
            $dataProvider = $groupSearch->query($groupSearch->term);
        }

        $user = Yii::$app->user->identity;
        // Owned groups
        $ownGroups = $user->ownGroups;
        $ids = ArrayHelper::getColumn($ownGroups, 'id');
        // Joined groups (any status)
        $allJoinedGroups = $user->getJoinedGroups($ids)->all();
        // Joined groups (active status)
        $joinedGroups = $user->activeJoinedGroups;
        // Owned and joined group ids
        $jids = ArrayHelper::getColumn($allJoinedGroups, 'id');
        $aids = array_merge($ids, $jids);
        // Groups with join requests pending for current user, exclude owned and joined groups
        $pendingGroups = $user->getPendingGroups($aids)->all();
        // Pending group ids
        $pids = ArrayHelper::getColumn($pendingGroups, 'id');

        return $this->render('myGroups', [
            'ownGroups' => $ownGroups,
            'allJoinedGroups' => $allJoinedGroups,
            'joinedGroups' => $joinedGroups,
            'pendingGroups' => $pendingGroups,
            'aids' => $aids,
            'pids' => $pids,
            'groupSearch' => $groupSearch,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create a new group
     *
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('createGroup');
    }

    /**
     * Enter group title, description, and image
     *
     * @return mixed
     */
    public function actionGroupInformation($id=NULL)
    {
        if ($id) {
            $group = Group::findOne($id);
            $group->scenario = 'update';
            if (!$group->canUpdateOwn()) {
                throw new NotFoundHttpException;
            }

        } else {
            $group = New Group();        
            $group->scenario = 'information';
            
        }

        // Ajax validation for unique group name
        if (Yii::$app->request->isAjax && $group->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($group);
        }

        if ($group->load(Yii::$app->request->Post()) && $group->handleFormInformation()) {
            return $this->redirect(['group-privacy', 'id' => $group->id]);

        } else {

            //Initialize select
            $initialData = ($profile = $group->ministry) ? [$profile->id => $profile->org_name] : NULL;

            return $this->render('groupInformation', [
                'group' => $group,
                'initialData' => $initialData
            ]);
        }
    }

    /**
     * Group privacy
     *
     * @return mixed
     */
    public function actionGroupPrivacy($id)
    {
        $group = Group::findOne($id);
        $group->scenario = 'options';  
        if (!$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }  

        if ($group->load(Yii::$app->request->Post()) && $group->save()) {
            return $this->redirect(['group-location', 'id' => $group->id]);
        } else {
            return $this->render('groupPrivacy', ['group' => $group]);
        }
    }

    /**
     * Group location
     *
     * @return mixed
     */
    public function actionGroupLocation($id)
    {
        $group = Group::findOne($id);
        $group->scenario = 'location';
        if (!$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        $place = new GroupPlace();     
        $place->scenario = 'new';  
        $placeList = $group->places;
        $keyword = new GroupKeyword();
        $keyword->scenario = 'new';
        $keywordList = $group->keywords; 

        if ($group->load(Yii::$app->request->Post()) && $group->save()) {
            return $this->redirect(['group-features', 'id' => $group->id]);
        } else {

            return $this->render('groupLocation', [
                'group' => $group, 
                'place' => $place,
                'placeList' => $placeList,
                'keyword' => $keyword,
                'keywordList' => $keywordList,
            ]);
        }
    }

    /**
     * Group features
     *
     * @return mixed
     */
    public function actionGroupFeatures($id)
    {
        $group = Group::findOne($id);
        $group->scenario = 'features';
        if (!$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if ($group->load(Yii::$app->request->Post())) {
            
            if (1 != $group->feature_prayer
                && 1 != $group->feature_forum
                && 1 != $group->feature_calendar
                && 1 != $group->feature_notification
                && 1 != $group->feature_document
                && 1 != $group->feature_update
                && 1 != $group->feature_donation) {

                Yii::$app->session->setFlash('warning', Html::icon('warning-sign') . ' Please select at least one group feature.');
                return $this->redirect(['group-features', 'id' => $group->id]);

            } elseif ($group->validate()) {

                // Set status
                if ($group->status != Group::STATUS_ACTIVE) {
                    $group->status = Group::STATUS_ACTIVE;
                    Yii::$app->session->setFlash('success', 'Your group "' . $group->name . '" is now active.');
                }

                // Create Discourse Group
                if ($group->feature_forum != $group->getOldAttribute('feature_forum')) {
                    $group->feature_forum == 1 ?
                        $group->createForumGroup() :
                        $group->removeForumGroup();
                }

                // Save
                $group->save();

                return $this->redirect(['my-groups']);
            }
        }

        return $this->render('groupFeatures', ['group' => $group]);
    }

    /**
     * Manage group members
     *
     * @return mixed
     */
    public function actionManageMembers($id)
    {
        $group = Group::findOne($id);
        if (!$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if (isset($_POST['approve'])) {
            list($gid, $uid) = explode('-', $_POST['approve']); 
            if ($member = GroupMember::find()->where(['group_id' => $gid, 'user_id' => $uid])->one()) {
                $member->approveMember();
                return $this->redirect(['manage-members', 'id' => $gid]);
            }
            throw new HttpException(500);
        }

        $memberSearchModel = new MemberSearch();
        $memberDataProvider = $memberSearchModel->search(Yii::$app->request->get(), $id);
        $pendingSearchModel = new MemberSearch();
        $pendingDataProvider = $pendingSearchModel->search(Yii::$app->request->get(), $id, true);

        $group->updateAttributes(['last_visit' => time()]);
        
        return $this->render('members', [
            'group' => $group,
            'memberDataProvider' => $memberDataProvider,
            'pendingDataProvider' => $pendingDataProvider,
        ]);
    }

    /**
     * Manage group forum
     *
     * @return mixed
     */
    public function actionManageForum($id)
    {
        $group = Group::findOne($id);
        $group->scenario = 'category-edit';
        if (!$group->canUpdateOwn() || ($group->feature_forum == NULL)) {
            throw new NotFoundHttpException;
        }

        $parentCategory = $group->parentCategory;
        $categories = $group->childCategories;
        
        return $this->render('forum\forum', [
            'group' => $group,
            'parentCategory' => $parentCategory,
            'categories' => $categories,
        ]);
    }

    /**
     * Manage group forum
     *
     * @return mixed
     */
    public function actionCategoryEdit($id=NULL, $cid=NULL)
    {
        $group = $id ? Group::findOne($id) : New Group;
        $group->scenario = 'category-edit';

        if (isset($_POST['save']) && $group->load(Yii::$app->request->Post())) {
            $group->categoryBannerColor = $_POST['categoryBannerColor'];
            $group->categoryTitleColor = $_POST['categoryTitleColor'];
            $group->updateCategory();
            return $this->redirect(['manage-forum', 'id' => $id]);

        } elseif (isset($_POST['trash']) && $group->load(Yii::$app->request->Post())) {
            $group->removeCategory(); 
            return $this->redirect(['manage-forum', 'id' => $id]);
        
        } elseif (isset($_POST['new']) && $group->load(Yii::$app->request->Post())) {
            $group->categoryBannerColor = $_POST['categoryBannerColor'];
            $group->categoryTitleColor = $_POST['categoryTitleColor'];
            $group->addChildCategory();
            return $this->redirect(['manage-forum', 'id' => $id]);
        }

        // Category exists, prepopulate values
        if (isset($cid)) {
            $category = $cid == $group->discourse_category_id ? 
                $group->parentCategory :
                $group->getChildCategory($cid);
            $group->cid = $category->id;
            $group->categoryName = $category->name;
            $group->categoryBannerColor = $category->color;
            $group->categoryTitleColor = $category->text_color;
            $group->_categoryDescription = $group->getCategoryDescription($category->topic_url);
        }

        return $this->renderAjax('forum\_categoryEdit', ['group' => $group]);
    }

    /**
     * Decline user request to join group
     *
     * @return mixed
     */
    public function actionDeclineRequest($id=NULL, $uid=NULL)
    {
        $group = $id ? Group::findOne($id) : new Group;
        $group->scenario = 'group-member-action';
        if ($id && !$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if (isset($_POST['decline']) && $group->load(Yii::$app->request->Post())) {
            if ($member = GroupMember::find()->where(['group_id' => $group->id, 'user_id' => $_POST['decline']])->one()) {
                if ($member->declineMember($group->message)) {
                    Yii::$app->session->setFlash('success', 'The request was declined and the user has been notified.');
                    return $this->redirect(['manage-members', 'id' => $group->id]);
                }
            }
            throw new HttpException(500);
        }

        $user = User::findOne($uid);

        return $this->renderAjax('_declineRequest', [
            'group' => $group, 
            'user' => $user,
        ]);
    }

    /**
     * Remove member from group
     *
     * @return mixed
     */
    public function actionRemoveMember($id=NULL, $uid=NULL)
    {
        $group = $id ? Group::findOne($id) : new Group;
        $group->scenario = 'group-member-action';
        if ($id && !$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if (isset($_POST['remove']) && $group->load(Yii::$app->request->Post())) { 
            if ($member = GroupMember::find()->where(['group_id' => $group->id, 'user_id' => $_POST['remove']])->one()) {
                if ($member->removeMember($group->message)) {
                    Yii::$app->session->setFlash('success', 'The member was removed and a notification was sent.');
                    return $this->redirect(['manage-members', 'id' => $group->id]);
                }
            }
            throw new HttpException(500);
        }

        $user = User::findOne($uid);

        return $this->renderAjax('_removeMember', [
            'group' => $group, 
            'user' => $user,
        ]);
    }

    /**
     * Ban member from group
     *
     * @return mixed
     */
    public function actionBanMember($id=NULL, $uid=NULL)
    {
        $group = $id ? Group::findOne($id) : new Group;
        $group->scenario = 'group-member-action';
        if ($id && !$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if (isset($_POST['ban']) && $group->load(Yii::$app->request->Post())) {
            if ($member = GroupMember::find()->where(['group_id' => $group->id, 'user_id' => $_POST['ban']])->one()) {
                if ($member->banMember($group->message)) {
                    Yii::$app->session->setFlash('success', 'The member was banned and a notification was sent.');
                    return $this->redirect(['manage-members', 'id' => $group->id]);
                }
            }
            throw new HttpException(500);
        }

        $user = User::findOne($uid);

        return $this->renderAjax('_banMember', [
            'group' => $group, 
            'user' => $user,
        ]);
    }

    /**
     * Removes ban of group member
     *
     * @return mixed
     */
    public function actionRestore($id=NULL, $uid=NULL)
    {
        $group = $id ? Group::findOne($id) : new Group;
        $group->scenario = 'group-member-action';
        if ($id && !$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if (isset($_POST['restore']) && $group->load(Yii::$app->request->Post())) {
            if ($member = GroupMember::find()->where(['group_id' => $group->id, 'user_id' => $_POST['restore']])->one()) {
                if ($member->restore($group->message)) {
                    Yii::$app->session->setFlash('success', 'The ban has been removed and a notification was sent.');
                    return $this->redirect(['manage-members', 'id' => $group->id]);
                }
            }
            throw new HttpException(500);
        }

        $user = User::findOne($uid);

        return $this->renderAjax('_restore', [
            'group' => $group, 
            'user' => $user,
        ]);
    }

    /**
     * Contact member modal
     *
     * @return mixed
     */
    public function actionContactMember($id=NULL, $uid=NULL)
    {
        $group = $id ? Group::findOne($id) : new Group();
        $group->scenario = 'contact-member';
        if ($id && !$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if (isset($_POST['contact'])
            && $group->load(Yii::$app->request->Post()) 
            && $group->validate()
            && $user = User::findOne($_POST['contact'])) {

            $owner = $group->owner;
            $mail = $owner->subscription ?? new Subscription();
            $mail->headerColor = Subscription::COLOR_GROUP;
            $mail->headerImage = Subscription::IMAGE_GROUP;
            $mail->headerText = 'Group Message';
            $mail->from = $owner->email;
            $mail->to = $user->email;
            $mail->cc = $owner->email;
            $mail->title = 'Message from IBNet group ' . $group->name;
            $mail->subject = $group->subject;
            $mail->message = $group->message;
            $mail->sendNotification(); 

            Yii::$app->session->setFlash('success', 'You\'re message has been sent.');

            return $this->redirect(['manage-members', 'id' => $group->id]);
        }

        $user = User::findOne($uid);
        $owner = User::findOne($group->user_id);

        return $this->renderAjax('_contactMember', [
            'group' => $group, 
            'user' => $user,
            'owner' => $owner,
        ]);
    }

    /**
     * Invite new group members
     *
     * @return mixed
     */
    public function actionInvite($id)
    {
        $group = Group::findOne($id);
        $group->scenario = 'invite-member';
        if (!$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if (isset($_POST['invite'])
            && $group->load(Yii::$app->request->Post()) 
            && $group->validate()) {

            // Check for failed email addresses
            $return = $group->sendInvites();
            if (is_array($return)) {
                $msg = count($return) > 1 ?
                    'The following address(es) are either invalid or unsubscribed: ' :
                    'The following address is either invalid or unsubscribed: ';
                foreach ($return as $failed) {
                    $msg .= $failed . ', ';
                }
                $msg = rtrim($msg,', ');
                Yii::$app->session->setFlash('warning', $msg);
            } else {
                Yii::$app->session->setFlash('success', 'You\'re invitation has been sent.');
            }

            return $this->redirect(['my-groups', 'id' => $group->id]);
        }

        return $this->renderAjax('_invite', ['group' => $group]);
    }

    /**
     * Invite new group members
     *
     * @return mixed
     */
    public function actionInviteJoin($token)
    {
        if (isset($_POST['join'])) {
            $invite = GroupInvite::findOne($_POST['join']);
            GroupMember::joinGroup($invite->group_id, true);
            $invite->delete();
            return $this->redirect(['group/my-groups']);

        } elseif (isset($_POST['decline'])) {
            $invite = GroupInvite::findOne($_POST['decline']);
            $invite->decline();
            return $this->goHome();
        } 
        
        if (!$invite = GroupInvite::find()->where(['token' => $token])->one()) {
            throw new NotFoundHttpException;
        }        
            
        $group = $invite->group;
        $status = 'authorized';
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            $role = array_keys(Yii::$app->authManager->getRolesByUser($user->id))[0];
            $members = ArrayHelper::getColumn($group->memberUsers, 'id');
        }

        if (Yii::$app->user->isGuest) {
            $status = 'guest';
        } elseif (in_array($user->id, $members)) {
            $status = 'member';
        } elseif (($invite->created_at + Yii::$app->params['tokenExpire.groupInviteJoin']) < time()) {
            $status = 'expired';
        } elseif (($role != User::ROLE_ADMIN) && ($role != User::ROLE_SAFEUSER)) {
            $status = 'not authorized';
        }

        return $this->render('inviteJoin', [
            'group' => $group,
            'invite' => $invite,
            'status' => $status,
        ]);
    }

    /**
     * Find another user in order to initiate a group transfer to them
     * @param string $id
     * @return mixed
     */
    public function actionTransfer($id) 
    {
        $group = Group::findOne($id); 
        $group->scenario = 'transfer';
        if (!$group->canUpdateOwn()) {
            throw new NotFoundHttpException;
        }

        if ($group->load(Yii::$app->request->Post())) {
            if (($oldUser = User::findOne(Yii::$app->user->identity->id))
                && ($newUser = User::findByEmail($group->newUserEmail))
                && $group->generateGroupTransferToken($newUser->id)) {

                // Check if user supplied own address
                if ($oldUser->id == $newUser->id) {
                    Yii::$app->session->setFlash('warning', 'You already own this group.');
                    return $this->redirect(['transfer', 'id' => $id]);
                }

                // Send transfer request email to new user
                if (!$group->sendGroupTransfer($group, $newUser, $oldUser)) {
                    Yii::$app->session->setFlash('warning', 'The message could not be sent to the address supplied.');
                    return $this->redirect(['transfer', 'id' => $id]); 
                }
            }
            Yii::$app->session->setFlash('success', 'Your request was sent and will remain valid for one week.  You will receive an email when the transfer is complete.');
        }
        return $this->render('transfer', ['group' => $group]);                              
    }

    /**
     * Landing page for group transfer completion
     * @param string $id
     * @return mixed
     */
    public function actionTransferComplete($id, $token) 
    {
        if (($group = Group::findOne($id))
            && $group->checkGroupTransferToken($token)
            && ($oldUser = User::findOne($group->user_id))
            && ($newUserId = (int) substr($token, 0, strrpos($token, '+')))
            && ($newUserId != Yii::$app->user->identity->id)
            && ($newUser = User::findOne($newUserId))) {
                
            $group->updateAttributes(['user_id' => $newUser->id, 'transfer_token' => NULL]);

            // Create a new group member for newUser if one doesn't exist
            if (!GroupMember::find()
                ->where(['group_id' => $group->id, 'user_id' => $newUser->id])
                ->exists()) {
                $groupMember = new GroupMember();
                $groupMember->group_id = $group->id;
                $groupMember->user_id = $newUser->id;
                if ($profile = $newUser->indActiveProfile) {
                    $groupMember->profile_id = $profile->id;
                    if ($profile->type == Profile::TYPE_MISSIONARY) {
                        $groupMember->missionary_id = $profile->missionary->id;
                    }
                }
                $groupMember->validate();
                $groupMember->save();
            }

            $sub = $newUser->subscription;
            if ($sub->token && $sub->unsubscribe) {
                return $this->render('transferComplete', ['group' => $group]);
            }

            // Send Email to old group owner
            $group->sendGroupTransfer($group, $newUser, $oldUser, TRUE);           

            return $this->render('transferComplete', ['group' => $group]);

        } else {
            throw new NotFoundHttpException;
        }                              
    }

    /**
     * Prayer List feature
     * @param $id integer group id
     * @param $dspy boolean preserve new prayer request on tag save page reload (1)
     * @param $f boolean ignore pagination and show full list of results (1)
     * @param $l boolean whether to show prayer (0) or answer (1) list
     * @return mixed
     */
    public function actionPrayer($id, $dspy=NULL, $f=NULL, $l=0)
    {
        $group = Group::findOne($id); 
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        $user = Yii::$app->user->identity;
        $joinedGroups = $user->joinedGroups;
        $member = $group->groupMember;
        $prayer = new Prayer();
        $prayer->scenario = 'prayer';
        $tagList = $group->prayerTagList;
        $update = new PrayerUpdate();
        $answer = new Prayer();
        $answer->scenario = 'answer';
        $prayerNameList = $group->getPrayerListNames();

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
            $prayer->group_id = $id;
            $prayer->group_member_id = GroupMember::groupMemberId($id);
            $prayer->save();
            if ($prayer->select) {
                $prayer->handleTags();
            }
            // Save to alert queue
            $prayer->addToAlertQueue(GroupAlertQueue::PRAYER_STATUS_NEW);

            return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]);  

        }

        $prayer->duration = 30; // Initialize to "short-term"
        $searchModel = new PrayerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get(), $id, $f, $l);       

        return $this->render('prayer/prayer', [
            'group' => $group, 
            'joinedGroups' => $joinedGroups,
            'member' => $member,
            'prayer' => $prayer,
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
     * Render content for update prayer modal
     * @param $id integer group id
     * @param  $pid integer prayer id
     * @return mixed
     */
    public function actionUpdatePrayer($id, $pid)
    { 
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        $prayer = Prayer::findOne($pid);
        $prayer->scenario = 'update';
        $update = new PrayerUpdate();
        $update->select = $prayer->prayerTags;
        $tagList = $group->prayerTagList;

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
            if (isset($update->update)) {
                $update->prayer_id = $pid;
                $update->save();

                // Save to alert queue
                $prayer->addToAlertQueue(GroupAlertQueue::PRAYER_STATUS_UPDATE);
            }
            // Always save prayer in order to update updated_at
            $prayer->save();

            return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]); 
        
        } else {
            return $this->renderAjax('prayer/_updatePrayer', ['update' => $update, 'tagList' => $tagList]);
        }
    }

    /**
     * Render content for answer prayer request modal
     *
     * @return mixed
     */
    public function actionAnswerPrayer($id, $pid)
    {
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        $prayer = Prayer::findOne($pid);
        $prayer->scenario = 'prayer';
        $answer = new Prayer();
        $answer->scenario = 'answer';
        if ($answer->load(Yii::$app->request->post()) && $answer->validate()) {
            $prayer->answer_description = $answer->answer_description;
            $prayer->answer_date = time();
            $prayer->answered = 1;
            $prayer->save();

            // Save to alert queue
            $prayer->addToAlertQueue(GroupAlertQueue::PRAYER_STATUS_ANSWER);

            return $this->redirect(['prayer', 'id' => $id, 'dspy' => NULL]); 
        
        }
        // Retain answer description if previously answered, then moved back to list
        $answer->answer_description = $prayer->answer_description;
        return $this->renderAjax('prayer/_answerPrayer', ['answer' => $answer]);
    }

    /**
     * Render content for update prayer modal
     * @param $id integer group id
     * @return mixed
     */
    public function actionNewTag($id=NULL)
    {
        $tag = new PrayerTag();

        if ($tag->load(Yii::$app->request->post()) && $tag->validate()) {
            $tag->tag = strtolower($tag->tag);
            $tag->save();
            return $this->redirect(['prayer', 'id' => $tag->group_id, 'dspy' => 1]);
        }

        $group = Group::findOne($id);
        $tagList = $group->prayerTagList;
        $tag->group_id = $id;

        return $this->renderAjax('prayer/_newTag', [
            'tag' => $tag, 
            'tagList' => $tagList
        ]);
    }

    /**
     * Redirect to Discourse forum
     *
     * @return mixed
     */
    public function actionForum($id)
    {
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        } 
    
        return $this->redirect(Yii::getAlias('@discourse'));
    }

    /**
     * Discourse SSO login
     *
     * @return mixed
     */
    public function actionDiscourseSso()
    {
        $request = Yii::$app->getRequest();
        $sso = Yii::$app->discourseSso;
        
        $payload = $request->get('sso');
        $sig = $request->get('sig');
    
        if(!($sso->validate($payload, $sig))){
            throw new ForbiddenHttpException('Bad SSO request');
        }
        
        $nonce = $sso->getNonce($payload);
        
        if (Yii::$app->getUser()->isGuest) {
            Yii::$app->getSession()->set('sso', ['sso' => $payload, 'sig' => $sig]);
            return $this->redirect(['site/login']);
        } else {
            $user = Yii::$app->getuser()->getIdentity();
        }
        
        Yii::$app->getSession()->remove('sso');
        
        // Send the data
        $userparams = [
            "nonce" => $nonce,
            "external_id" => (String)$user->id,
            "email" => $user->email,
            "username" => $user->username,
            "name" => $user->fullName,
            'avatar_url' => Url::to([$user->usr_image], 'http')
        ];
        $q = $sso->buildLoginString($userparams);
        
        // Redirect back
        $this->redirect(Yii::getAlias('@discourse') . '/session/sso_login?' . $q);
    }

    /**
     * Calendar feature
     * $id integer Group id
     * $date string Start date of last edited event to use on page reload
     * @return mixed
     */
    public function actionCalendar($id, $date=NULL)
    {
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        $user = Yii::$app->user->identity;
        $joinedGroups = $user->joinedGroups;
        $eventList = GroupCalendarEvent::allEvents($id);
        $icalList = $group->icalEvents;
        $upcomingList = GroupCalendarEvent::upcomingEvents($id);
        return $this->render('calendar/calendar', [
            'group' => $group,
            'eventList' => $eventList,
            'icalList' => $icalList,
            'upcomingList' => $upcomingList,
            'urls' => empty($icalList) ? false : true,
            'joinedGroups' => $joinedGroups,
            'date' => $date,
        ]);
    }

    /**
     * Render content for new calendar event modal
     *
     * @return mixed
     */
    public function actionNewEvent($id)
    {
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        $event = new GroupCalendarEvent();
        $event->group_id = $id;
        $nmid = GroupMember::groupMemberId($id);

        if ($event->load(Yii::$app->request->post()) && $event->validate()) {
            $event->group_member_id = $nmid;
            $range = explode(' - ', $_POST['dateRange']);
            $event->start = strtotime($range[0]);
            $event->end = $event->all_day ?
                strtotime($range[1]) + (24*3600) :
                strtotime($range[1]);
            $event->color = $_POST['color'];
            $event->save();
            $date = Yii::$app->formatter->asDate($event->start, 'php:Y-m');
            return $this->redirect(['calendar', 'id' => $id, 'date' => $date]);
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
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        if ($resourceId == Group::RESOURCE_ICAL) {
            $ical = IcalenderEvent::findOne($eid);
            $viewEvent = new GroupCalendarEvent();
            $viewEvent->title = $ical->SUMMARY;
            $viewEvent->start = $ical->DTSTART;
            $viewEvent->end = $ical->DTEND;
            $isOwner = 10;
        } else {
            $viewEvent = GroupCalendarEvent::findOne($eid);
            $ownerId = $viewEvent->groupUser->id;
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
            if ($event = GroupCalendarEvent::findOne($_POST['remove'])) {
                $id = $event->group_id;
                $group = Group::findOne($id);
                if (!$group->canAccess()) {
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
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        $event = GroupCalendarEvent::findOne($eid);
        if ($event->load(Yii::$app->request->post()) && $event->validate()) {
            $range = explode(' - ', $_POST['dateRange']);
            $event->start = strtotime($range[0]);
            $event->end = $event->all_day ?
                strtotime($range[1]) + (24*3600) :
                strtotime($range[1]);
            $event->color = $_POST['color'];
            $event->save();
            $date = Yii::$app->formatter->asDate($event->start, 'php:Y-m');
            return $this->redirect(['calendar', 'id' => $id, 'date' => $date]);
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
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }
        $urlList = $group->owniCals;
        $ical = new GroupIcalendarUrl();
        
        if ($ical->load(Yii::$app->request->post()) && $ical->validate()) {
            if (!empty($ical->url)) {
                // Check if url is already saved
                $duplicate = false;
                if (is_array($urlList)) {
                    foreach ($urlList as $url) {
                        $compare = strcmp($url->url, $ical->url);
                        $duplicate = $compare == 0 ? true : $duplicate;
                    }
                }
                if (!empty($duplicate)) {
                    Yii::$app->session->setFlash('info', 
                        'The calendar has already been imported.');
                } else {
                    $ical->group_id = $id;
                    $ical->group_member_id = $group->groupMember->id;
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
            if ($url = GroupIcalendarUrl::findOne($_POST['remove'])) {

                $group = Group::findOne($url->group_id);
                if (!$group->canAccess()) {
                    throw new NotFoundHttpException;
                }

                $url->updateAttributes(['deleted' => 1]);
                Yii::$app->session->setFlash('info', 'Your imported calendar was removed.');
                
                return $this->redirect(['calendar', 'id' => $url->group_id]);
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
        $group = Group::findOne($id);
        $group->scenario = 'send-notice';
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }

        if ($group->load(Yii::$app->request->Post()) && $group->validate()) {
            $group->sendNotification();
            Yii::$app->session->setFlash('success', 'Your notification has been sent.');
            return $this->redirect(['notification', 'id' => $group->id]);
        }

        $user = Yii::$app->user->identity;
        $joinedGroups = $user->joinedGroups;
        
        return $this->render('notification/notification', [
            'group' => $group,
            'joinedGroups' => $joinedGroups,
        ]);
    }

    /**
     * Document Library feature
     *
     * @return mixed
     */
    public function actionDocument($id)
    {
        $this->group = Group::findOne($id);
        if (!$group->canAcess()) {
            throw new NotFoundHttpException;
        }

        return $this->render('document', ['group' => $this->group]);
    }

    /**
     * Missionary Updates feature
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $group = Group::findOne($id);
        if (!$group->canAccess()) {
            throw new NotFoundHttpException;
        }
        
        $user = Yii::$app->user->identity;
        $joinedGroups = $user->joinedGroups;
        $member = $group->groupMember;
        $updateSearchModel = new UpdateSearch();
        $updateDataProvider = $updateSearchModel->search(Yii::$app->request->get(), $group);
        $updateNameList = $group->getUpdateListNames();
        return $this->render('update/update', [
            'group' => $group,
            'member' => $member,
            'updateDataProvider' => $updateDataProvider,
            'updateSearchModel' => $updateSearchModel,
            'updateNameList' => $updateNameList,
            'joinedGroups' => $joinedGroups,
        ]);
    }

    /**
     * Donations feature
     *
     * @return mixed
     */
    public function actionDonate($id)
    {
        $this->group = Group::findOne($id);
        if (!$group->canAcess()) {
            throw new NotFoundHttpException;
        }
        return $this->render('donate', ['group' => $this->group]);
    }
}