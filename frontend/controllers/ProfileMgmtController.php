<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\User;
use common\models\Utility;
use common\models\profile\FormsCompleted;
use common\models\profile\History;
use common\models\profile\Profile;
use common\models\profile\ProfileMail;
use common\models\profile\Type;
use common\models\profile\Staff;
use common\models\profile\SubType;
use common\rbac\PermissionProfile;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class ProfileMgmtController extends ProfileController
{
    public $layout="main";  

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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * My Profiles
     * @return mixed
     */
    public function actionMyProfiles()
    {
        if (isset($_POST['trash'])) {
            $this->redirect(['trash', 'id' => $_POST['trash']]);
        }

        $user = Yii::$app->user->identity;
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id))[0];
        $profiles = $user->profiles;
        foreach ($profiles as $profile) {
            if ($profile->category == Profile::CATEGORY_ORG) {
                $profile->unconfirmed = Staff::checkUnconfirmed($profile->id);
            }
            $profile->events = ($profile->history != NULL);
        }
        $isMissionary = ($user->isMissionary == User::IS_MISSIONARY);

        $joinedGroups = $user->joinedGroups;

        Url::remember();
        return $this->render('myProfiles', [
            'profiles' => $profiles, 
            'isMissionary' => $isMissionary, 
            'joinedGroups' => $joinedGroups,
            'role' => $role,
        ]);
    }

    /**
     * List terms for creating a profile
     * User confirms agreement by clicking the "I Agree" button
     * @return 
     */
    public function actionTerms()
    {
        if (Yii::$app->request->Post()) {
            return $this->redirect(['create']);
        } else {
            return $this->render('profileTerms');
        }
    }

    /**
     * Creates a new Profile model.
     * If creation is successful, redirect to ProfileFormController where user will advance through 
     * a series of data collection views dependent on the profile type.
     * @return mixed
     */
    public function actionCreate()
    {
        $profile = new Profile();
        $profile->scenario = 'create';

        if ($profile->load(Yii::$app->request->Post()) && 
            $profile->profileCreate() &&
            $profile->createProgress($profile->id)) {

            return $this->redirect(['profile-form/form-route', 'type' => $profile->type, 'fmNum' => -1, 'id' => $profile->id]);

        } else {
            $types = ArrayHelper::map(Type::find()->where(['active' => 1])->all(), 'type', 'type', 'group');
            $pastorTypes = ArrayHelper::map(SubType::find()->select('sub_type')
                ->where(['type' => 'Pastor'])->all(), 'sub_type', 'sub_type');
            $missionaryTypes = ArrayHelper::map(SubType::find()->select('sub_type')
                ->where(['type' => 'Missionary'])->all(), 'sub_type', 'sub_type');
             $chaplainTypes = ArrayHelper::map(SubType::find()->select('sub_type')
                ->where(['type' => 'Chaplain'])->all(), 'sub_type', 'sub_type');

            return $this->render('profileCreate', [
                'profile' => $profile, 
                'types' => $types, 
                'pastorTypes' => $pastorTypes,
                'missionaryTypes' => $missionaryTypes,
                'chaplainTypes' => $chaplainTypes]);
        }
    }

    /**
     * Activate a profile
     * @param string $id
     * @return mixed
     */
    public function actionActivate($id)
    {
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE_OWN, ['profile' => $profile])) {
            throw new NotFoundHttpException;
        }

        // Check if user already has an individual profile
        $user = Yii::$app->user->identity;
        if ($profile->category == Profile::CATEGORY_IND) && $user->hasIndActiveProfile) {
            Yii::$app->session->setFlash('warning', 'You already have an individual 
                    profile. Only one individual profile can be active at a time.');
            return $this->redirect(['preview/view-preview', 'id' => $profile->id]);
        }

        // Check if all required forms for this profile type have been completed
        if (!$progress = FormsCompleted::findOne($id)) {
            $progress = $profile->createProgress($id);
        }        
        $completeArray = unserialize($progress->form_array);
        $typeArray = ProfileFormController::$formArray[$profile->type];
        // any $fmNum => 1 pairs that are missing from $completeArray  
        $missingArray = array_diff_assoc($completeArray, $typeArray);
        if (!$progress || !empty($missingArray)) {
            if ($profile->type == Profile::TYPE_CHURCH) {   
                // Ignore skipped form (church profile missions housing)                                                    
                if (!((count($missingArray) == 1) &&
                    isset($missingArray[ProfileFormController::$form['mh']]))) {
                    
                    $missing = json_encode($missingArray);
                    return $this->redirect(['profile-form/missing-forms', 'id' => $profile->id, 'missing' => $missing]);
                }
            
            } else {
                $missing = json_encode($missingArray);
                return $this->redirect(['profile-form/missing-forms', 
                    'id' => $profile->id, 
                    'missing' => $missing,
                ]);
            }
        }

        // Check for duplicates
        if ($dup = $profile->duplicate) {
            return $this->redirect(['profile-form/duplicate-profile', 'id' => $profile->id, 'dupId' => $dup->id]);
        }

        // Activate
        if ($progress->delete() && $profile->activate()) {
            return $this->render('activationComplete', ['profile' => $profile]);

        // Some other errror
        } else {
            throw new HttpException(500, 'There was a problem processing your request. If this 
                problem persits, please contact us at admin@ibnet.org.');
        }
    }

    /**
     * Continue profile activation
     * @param string $id
     * @return mixed
     */
    public function actionContinueActivate($id) 
    {
        $profile = Profile::findProfile($id);
        // Get progress of profile completion
        if ($progress = $profile->getProgress()) {
            // If at least one form has been completed, go to forms menu
            if (in_array(1, $progress)) {
                return $this->redirect(['profile-form/forms-menu', 'id' => $id]);
            }
        }
        return $this->redirect(['profile-form/form-route', 'type' => $profile->type, 'fmNum' => -1, 'id' => $profile->id]);                                
    }

    /**
     * Disables a profile
     * @param string $id
     * @return mixed
     */
    public function actionDisable($id) 
    {
        $profile = Profile::findProfile($id);
        if ($profile->inactivate() && $profile->setInactivationDate()) {
            Yii::$app->session->setFlash('success', 'Your Profile "' . 
                $profile->profile_name . '" was successfully disabled.');
        } else {
            Yii::$app->session->setFlash('danger', 'There was a problem with disabling your profile "' . 
                $profile->profile_name . '".  Please try again or contact us if this problem persists.');
        }
        return $this->redirect(['my-profiles']);
    }

    /**
     * Trash a profile
     * @param string $id
     * @return mixed
     */
    public function actionTrash($id) 
    {
        $profile = Profile::findProfile($id);
        if ($profile->trash()) {    
            Yii::$app->session->setFlash('success', 'Your Profile "' . 
                $profile->profile_name . '" was successfully deleted.');
        } else {
            Yii::$app->session->setFlash('danger', 'There was a problem with disabling your profile "' . 
                $profile->profile_name . '".  Please try again or contact us if this problem persists.');
        }   
        return $this->redirect(['my-profiles']);                                
    }

    /**
     * Find another user in order to initiate a profile transfer to them
     * @param string $id
     * @return mixed
     */
    public function actionTransfer($id) 
    {
        $profile = Profile::findProfile($id); 
        $profile->scenario = 'transfer';

        if ($profile->load(Yii::$app->request->Post())) {
            if (($user = User::findByEmail($profile->select)) && 
                ($user->id != Yii::$app->user->id)) {
                return $this->redirect(['transfer-initiate', 'id' => $id, 'email' => $user->email]);
            } else {
                Yii::$app->session->setFlash('danger', 'We could not find another user with this email address.');
            }
        }
        return $this->render('transfer', ['profile' => $profile]);                              
    }

    /**
     * Initiate a profile transfer to another user
     * @param string $id
     * @param string $email new owner email
     * @return 
     */
    public function actionTransferInitiate($id, $email)
    {
        $profile = Profile::findProfile($id);
        if (Yii::$app->request->Post()) {
            $newUser = User::findByEmail($email);
            $token = $profile->generateProfileTransferToken($newUser->id);
            $profile->updateAttributes(['transfer_token' => $token]);

            // Send transfer request email to new user
            $oldUser = User::findOne(Yii::$app->user->id);
            ProfileMail::sendProfileTransfer($profile, $newUser, $oldUser);
            return $this->redirect(['transfer-sent']);
        }
        return $this->render('transferInit', ['profile' => $profile, 'email' => $email]);
    }

    /**
     * Transfer has been initiated
     * @return mixed
     */
    public function actionTransferSent() 
    {
        return $this->render('transferSent');                            
    }

    /**
     * Landing page for profile transfer completion
     * @param string $id
     * @return mixed
     */
    public function actionTransferComplete($id, $token) 
    {
        $profile = Profile::findProfile($id);
        if ($profile->checkProfileTransferToken($token)) {

            $oldUser = User::findOne($profile->user_id);
            $newUserId = (int) substr($token, 0, strrpos($token, '+'));
            $newUser = User::findOne($newUserId);
            $profile->updateAttributes(['user_id' => $newUserId, 'transfer_token' => NULL]);

            // Send Email to previous profile owner
            ProfileMail::sendProfileTransfer($profile, $newUser, $oldUser, TRUE);

            return $this->render('transferComplete', ['profile' => $profile]);
        } else {
            throw new NotFoundHttpException;
        }                              
    }

    /**
     * Profile History
     * @param string $id
     * @return mixed
     */
    public function actionHistory($id, $a = NULL) 
    {
        $profile = Profile::findProfile($id); 
        $profile->scenario = 'history';

        $history = new History();
        $action = NULL;
        if (isset($_POST['add'])) {
            $action = 'add';

        } elseif (isset($_POST['remove'])) {
            $event = History::findOne($_POST['remove']);
            $event->updateAttributes(['deleted' => 1]);

        } elseif (isset($_POST['edit'])) {
            $action = 'edit';
            $events = $profile->history;
            foreach ($events as $i=>$event) {
                if ($event->id == $_POST['edit']) {
                    $events[$i]['edit'] = 1;
                    $events[$i]['date'] = Yii::$app->formatter->asDate($event->date, 'MM/dd/yyyy');
                    $a = $event->id;
                }
            }
            return $this->render('history', [
                'profile' => $profile,
                'history' => $history,
                'events' => $events,
                'action' => $action,
                'a' => $a]);

        } elseif (isset($_POST['edit-save'])) {
            $event = History::findOne($_POST['edit-save']);
            if ($event->load(Yii::$app->request->Post()) && $event->validate()) {
                $event->profile_id = $profile->id;
                $event->date = strtotime($event->date);
                $event->save();
            }
            return $this->redirect(['history', 'id' => $profile->id, 'a' => $event->id]);

        } elseif (isset($_POST['cancel'])) {
            $events = $profile->history;
            return $this->render('history', [
                'profile' => $profile,
                'history' => $history,
                'events' => $events,
                'action' => $action]);

        } elseif ($history->load(Yii::$app->request->Post()) && 
            $history->validate()) {
            $history->profile_id = $profile->id;
            $history->date = strtotime($history->date);
            $history->save();
            $this->redirect(['history', 'id' => $profile->id, 'a' => $history->id]);
        }

        $events = $profile->history;

        return $this->render('history', [
            'profile' => $profile,
            'history' => $history,
            'events' => $events,
            'action' => $action,
            'a' => $a]);                              
    }

}                     