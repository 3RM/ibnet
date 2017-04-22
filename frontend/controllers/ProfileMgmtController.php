<?php

namespace frontend\controllers;

use common\models\User;
use common\models\Utility;
use common\models\SendMail;
use common\models\profile\FormsCompleted;
use common\models\profile\Profile;
use common\models\profile\Type;
use common\models\profile\SubType;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class ProfileMgmtController extends ProfileController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => [],                                                                     // Apply authentication to all actions
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
        $profileArray = Profile::getProfileArray();
        return $this->render('myProfiles', ['profileArray' => $profileArray]);
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
     * If creation is successful, the browser will advance through a series of
     * data collection views dependent on the profile type.
     * @return mixed
     */
    public function actionCreate()
    {
        $profile = new Profile();
        $profile->scenario = 'create';

        if ($profile->load(Yii::$app->request->Post()) && 
            $profile->profileCreate() &&
            $profile->createProgress($profile->id)) {

            return $this->redirect(['profile-form/form-route', 
                'type' => $profile->type, 
                'fmNum' => -1, 
                'id' => $profile->id]);

        } else {
            $types = ArrayHelper::map(Type::find()->all(),                      //add ->where(['active' => 1])
                'type', 'type', 'group');
            $pastorTypes = ArrayHelper::map(SubType::find()->select('sub_type')
                ->where(['type' => 'Pastor'])->all(), 'sub_type', 'sub_type');
            $missionaryTypes = ArrayHelper::map(SubType::find()->select('sub_type')
                ->where(['type' => 'Missionary'])->all(), 'sub_type', 'sub_type');

            return $this->render('profileCreate', [
                'profile' => $profile, 
                'types' => $types, 
                'pastorTypes' => $pastorTypes,
                'missionaryTypes' => $missionaryTypes]);
        }
    }

    /**
     * Activate a profile
     * @param string $id
     * @return mixed
     */
    public function actionActivate($id)
    {
        $profile = $this->findProfile($id);
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile])) {
            throw new NotFoundHttpException;
        }

        if (!$progress = FormsCompleted::findOne($id)) {                                            // Check if all required forms for this profile type have been completed
            $progress = $profile->createProgress($id);
        }
        $completeArray = unserialize($progress->form_array);
        $typeArray = ProfileFormController::$formArray[$profile->type];
        if (!$progress || $missing = array_diff_assoc($typeArray, $completeArray)) {                // $missing will contain any $fmNum => 1 pairs that are missing from $completeArray
            if ($profile->type == 'Church' &&                                                       // Ignore skipped form (missions housing)
                count($missing == 1) && 
                isset($missing[self::$form['mh']]) &&
                $missing[self::$form['mh']] == 1) {

                return $this->redirect(['missing-forms', 
                    'id' => $profile->id, 
                    'fmNum' => key($missing)
                ]);
            }
        } 

        if ($progress->delete() && !ProfileFormController::isDuplicate($id) && $profile->activate()) {
            return $this->render('activationComplete', ['profile' => $profile]);
        } else {
            throw new HttpException(500, 'There was a problem processing your request. If this 
                problem persits, please contact us at admin@ibnet.org.');;
        }
    }

    /**
     * Continue profile activation
     * @param string $id
     * @return mixed
     */
    public function actionContinueActivate($id) 
    {
        $profile = $this->findProfile($id);
        if ($progress = $profile->getProgress()) {                                                  // Get progress of profile completion
            if (in_array(1, $progress)) {                                                           // If at least one form has been completed, go to forms menu
                return $this->redirect(['profile-form/forms-menu', 'id' => $id]);
            }
        }
        return $this->redirect(['profile-form/form-route', 
            'type' => $profile->type, 
            'fmNum' => -1, 
            'id' => $profile->id]);                                
    }

    /**
     * Disables a profile
     * @param string $id
     * @return mixed
     */
    public function actionDisable($id) 
    {
        $profile = $this->findProfile($id);
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
        $profile = $this->findProfile($id);
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
        $profile = $this->findProfile($id); 
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
     * @return 
     */
    public function actionTransferInitiate($id, $email)
    {
        $profile = $this->findProfile($id);

        if (Yii::$app->request->Post()) {
            
            $newUser = User::findByEmail($email);
            $token = $profile->generateProfileTransferToken($newUser->id);
            $profile->updateAttributes(['transfer_token' => $token]);

            // Send transfer request email to new user
            $oldUser = User::findOne(Yii::$app->user->id);
            $subject = 'IBNet Profile Transfer Request';
            $title = 'IBNet Profile Transfer Request';
            $msg = $oldUser->first_name . ' ' . $oldUser->last_name . ' requests that you assume ownership of 
                IBNet profile "' . $profile->profile_name . '".  Click the link below to complete the 
                transfer and take ownership of this profile.  This link will remain valid for one week.';
            SendMail::sendProfileTransfer($subject, $title, $msg, $profile, $email, true);

            return $this->redirect(['transfer-sent']);
        }
        return $this->render('transferInit', ['profile' => $profile, 'email' => $email]);
    }

    /**
     * Transfer has been initiated
     * @param string $id
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
        $profile = $this->findProfile($id);
        if ($profile->checkProfileTransferToken($token)) {

            $oldUser = User::findOne($profile->user_id);
            $newUserId = (int) substr($token, 0, strrpos($token, '+'));
            $newUser = User::findOne($newUserId);
            $profile->updateAttributes(['user_id' => $newUserId, 'transfer_token' => NULL]);

            // Send Email to "old" profile owner
            $subject = 'IBNet Profile Transfer Complete';
            $title = 'IBNet Profile Transfer Complete';
            $msg = 'Your IBNet profile "' . $profile->profile_name . '" has been successfully transferred 
                to ' . $newUser->first_name . ' ' . $newUser->last_name;
            SendMail::sendProfileTransfer($subject, $title, $msg, $profile, $oldUser->email, false);

            return $this->render('transferComplete', ['profile' => $profile]);
        } else {
            throw new NotFoundHttpException;
        }                              
    }

}                     