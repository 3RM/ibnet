<?php

namespace frontend\controllers;

use common\models\profile\Association;
use common\models\profile\Country;
use common\models\profile\Fellowship;
use common\models\profile\FormsCompleted;
use common\models\profile\MissHousing;
use common\models\profile\MissHousingVisibility;
use common\models\profile\MissionAgcy;
use common\models\profile\Missionary;
use common\models\profile\Profile;
use common\models\profile\ProfileForm;
use common\models\profile\School;
use common\models\profile\ServiceTime;
use common\models\profile\Social;
use common\models\profile\Staff;
use common\models\profile\SubType;
use common\models\profile\Type;
use common\models\User;
use common\models\Utility;
use common\models\profile\Mail;
use frontend\controllers\MailController;
use kartik\markdown\MarkdownEditor;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class ProfileFormController extends ProfileController
{
    // Determine sequence of forms
    public static $formArray = [     
          // Form #            0   1   2   3   4   5   6   7   8   9   10  11  12  13  14  15  16  17  18 
        'Pastor' =>           [1,  1,  1,  1,  1,  0,  0,  0,  1,  0,  1,  0,  1,  0,  1,  0,  0,  1,  0],  
        'Evangelist' =>       [1,  1,  1,  1,  1,  0,  0,  0,  1,  0,  1,  0,  1,  0,  1,  0,  0,  1,  0],
        'Missionary' =>       [1,  1,  1,  1,  1,  0,  0,  1,  1,  1,  1,  0,  1,  0,  1,  1,  0,  0,  0],
        'Chaplain' =>         [1,  1,  1,  1,  1,  0,  0,  0,  1,  0,  1,  0,  1,  0,  0,  0,  0,  0,  0],
        'Staff' =>            [1,  1,  1,  1,  1,  0,  0,  0,  1,  0,  1,  0,  1,  0,  0,  0,  0,  0,  0],
        'Church' =>           [1,  1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  1,  1,  1,  1,  0],
        'Mission Agency' =>   [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  0],
        'Fellowship' =>       [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0],
        'Association' =>      [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0],
        'Camp' =>             [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  0],
        'School' =>           [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  1,  0,  0,  0,  1,  0],
        'Print Ministry' =>   [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  0],
        'Music Ministry' =>   [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  0],
        'Special Ministry' => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  1]];

    // Assign form #s
    public static $form = [
        'nd' => 0,            // Name & Description
        'i1' => 1,            // Image 1
        'i2' => 2,            // Image 2
        'lo' => 3,            // Location
        'co' => 4,            // Contact
        'sf' => 5,            // Staff
        'st' => 6,            // Church Service Times
        'fi' => 7,            // Missionary Field Information
        'hc' => 8,            // Home Church
        'cp' => 9,            // Church Plant
        'pm' => 10,           // Parent Ministry
        'pg' => 11,           // Programs
        'sa' => 12,           // Schools Attended
        'sl' => 13,           // School Levels
        'di' => 14,           // Distinctives
        'ma' => 15,           // Mission Agencies
        'mh' => 16,           // Mission Housing
        'as' => 17,           // Associations
        'ta' => 18,           // Tags
    ];

    // Ordered list of form names
    public static $formList = [
        'Name & Description',
        'Large Picture',
        'Small Picture',
        'Location',
        'Contact',
        'Staff',
        'Service Times',
        'Missionary Field Information',
        'Home Church',
        'Church Plant',
        'Ministry or Organization',
        'Programs',
        'Schools Attended',
        'School Levels',
        'Distinctives',
        'Mission Agency',
        'Skip',
        'Associations',
        'Tags'
    ];

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
     * Render Forms list.  
     * User confirms agreement by clicking the "I Agree" button
     * @return 
     */
    public function actionFormsMenu($id)
    {
        $profile = $this->findProfile($id);
        $typeMask = self::$formArray[$profile->type];
        $profile->status != Profile::STATUS_ACTIVE ? 
            $progressPercent = $profile->getProgressPercent($typeMask) :
            $progressPercent = NULL;

        return $this->render('formsMenu', [
            'profile' => $profile,
            'formList' => self::$formList,
            'count' => count(Self::$form)-1,
            'typeMask' => $typeMask,
            'progressPercent' => $progressPercent]);
    }

    /**
     * FORM SEQUENCE
     * Redirect to the next form action, given a profile type and form number
     * Pass the profile id
     *
     * @return mixed
     */
    public function actionFormRoute($type, $fmNum, $id, $e=0)
    {
        $value = 0;                                                                                 // Initialize $value
        while ($value == 0) {                                                                       // Find next '1' (i.e. required form) in row
            $fmNum++;
            if ($fmNum > (count(Self::$form)-1)) {
                return $this->redirect(['/preview/view-preview', 'id' => $id]);
            }
            $value = self::$formArray[$type][$fmNum];
        }
        $action = 'form' . $fmNum;
        
        return $this->redirect([$action, 'id' => $id, 'e' => $e]);
    }






/***************************************************************************************************
 ***************************************************************************************************
 *
 * The following sequence of actions with data collection forms 
 * follow the attribute map in app/docs/IBD DB Attribute Map.xls
 *
 ***************************************************************************************************
\**************************************************************************************************/

    /**
     * Name & Description (nd)
     * Render:
     *     - form#-flwsp_ass
     *     - form#-miss_agency
     *     - form#-school
     *     - form#-org
     *     - form#-ind
     *
     * @return mixed
     */
    public function actionForm0($id, $e=0)
    {
        $fmNum = Self::$form['nd'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        if ($profile->type == 'Fellowship' || $profile->type == 'Association') {
            $profile->scenario = 'nd-flwsp_ass';
        } elseif ($profile->type == 'Mission Agency') {
            $profile->scenario = 'nd-miss_agency';
        } elseif ($profile->type == 'School') {
            $profile->scenario = 'nd-school';
        } else {
            $profile->isIndividual($profile->type) ?
                $profile->scenario = 'nd-ind':
                $profile->scenario = 'nd-org';
        }

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 
        }
        if (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
        }
        if ($profile->load(Yii::$app->request->Post()) && 
            $profile->handleFormND() && 
            $profile->setUpdateDate() && 
            $profile->setProgress($fmNum)) {

            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);

        } else {
            $toolbar = $this->getMarkdownToolbar();
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;
            switch($profile->type) {

        // *********************** Association *********************************
                case 'Association': 
                    $list = ArrayHelper::map(Association::find()
                        ->where(['status' => Profile::STATUS_ACTIVE])
                        ->andWhere('profile_id IS NULL')
                        ->orWhere(['profile_id' => $profile->id])
                        ->orderBy('association')
                        ->all(), 'id', 'association');
                    $toggle = NULL;
                    if ($association = Association::find()
                        ->where(['status' => Profile::STATUS_ACTIVE])
                        ->andWhere(['profile_id' => $profile->id])
                        ->one()) {                                                                  // Prepopulate select
                        $toggle = true;
                        $profile->select = $association->id;
                        $profile->name = NULL;
                    } else {
                        $profile->name = $profile->org_name;
                        $profile->select = NULL;
                    }                                   
                    return $this->render($fm . '-flwsp_ass', [
                        'profile' => $profile, 
                        'list' => $list,
                        'toggle' => $toggle,
                        'toolbar' => $toolbar, 
                        'pp' => $progressPercent, 
                        'e' => $e]);
                    break;

        // *********************** Fellowship *********************************
                case 'Fellowship':
                    $list = ArrayHelper::map(Fellowship::find()
                        ->where(['status' => Profile::STATUS_ACTIVE])
                        ->andWhere('profile_id IS NULL')
                        ->orWhere(['profile_id' => $profile->id])
                        ->orderBy('fellowship')
                        ->all(), 'id', 'fellowship');
                    $toggle = NULL;
                    if ($fellowship = Fellowship::find()
                        ->where('profile_id IS NULL')
                        ->andWhere(['profile_id' => $profile->id])
                        ->one()) {                                                                  // Prepopulate select
                        $toggle = true;
                        $profile->select = $fellowship->id;
                        $profile->name = NULL;
                    } else {
                        $profile->name = $profile->org_name;
                        $profile->select = NULL;
                    }          
                    return $this->render($fm . '-flwsp_ass', [
                        'profile' => $profile, 
                        'list' => $list,
                        'toolbar' => $toolbar, 
                        'pp' => $progressPercent, 
                        'e' => $e]);
                    break;

        // *********************** Mission Agency *********************************
                case 'Mission Agency':
                    $list = ArrayHelper::map(MissionAgcy::find()
                        ->where('id>2')
                        ->orWhere(['profile_id' => $profile->id])
                        ->orderBy('mission')
                        ->all(), 'id', 'mission');
                    $toggle = NULL;
                    if ($missionAgcy = MissionAgcy::find()
                        ->where(['profile_id' => $profile->id])->one()) {                           // Prepopulate select
                        $toggle = true;
                        $profile->select = $missionAgcy->id;
                        $profile->name = NULL;
                    } else {
                        $profile->name = $profile->org_name;
                        $profile->select = NULL;
                    }          
                    return $this->render($fm . '-miss_agency', [
                        'profile' => $profile, 
                        'toggle' => $toggle,
                        'toolbar' => $toolbar, 
                        'list' => $list,
                        'pp' => $progressPercent, 
                        'e' => $e]);
                    break;

        // *********************** School *********************************
                case 'School': 
                    $schools = School::find()                                                       //     Populate dropdown: array of all associations minus ones with other linked profiles
                        ->select('id, school, city, st_prov_reg')
                        ->where('id > 2')                                                           // Exclude first two Ids (other secular, other Christian schools)
                        ->andWhere('ib = 1')                                                        // Include only "IB" schools
                        ->andWhere('closed < 1')                                                    // Include only schools that are still open
                        ->andWhere('profile_id IS NULL')                                            // List only schools that have not already been claimed by another profile
                        ->orWhere(['profile_id' => $profile->id])
                        ->indexBy('id')
                        ->orderBy('id')
                        ->all();
                    $i = 1;
                    foreach($schools as $school) {                                                  // Create 2D array of [id=>1][name=>XX] to format names as school (city, state)
                        $formatNames[$i]['id'] = $school->id;
                        $formatNames[$i]['name'] = $school->school . ' (' . 
                            $school->city . ', ' . $school->st_prov_reg . ')';
                        $i++;
                    }
                    $list = ArrayHelper::map($formatNames, 'name', 'name');                         // Build map (key-value pairs) from 2D array [1=>XX] for use in dropdown list
                    $toggle = NULL;
                    if ($school = School::find()->where(['profile_id' => $profile->id])->one()) {   // Prepopulate select
                        $toggle = true;
                        $profile->select = $school->school . ' (' . 
                            $school->city . ', ' . $school->st_prov_reg . ')';
                        $profile->name = NULL;
                    } else {
                        $profile->name = $profile->org_name;
                        $profile->select = NULL;
                    }
                    return $this->render($fm . '-school', [
                        'profile' => $profile,
                        'toggle' => $toggle,
                        'toolbar' => $toolbar,
                        'list' => $list,
                        'pp' => $progressPercent,
                        'e' => $e]);
                    break;  

        // *********************** All Others *********************************  
                
                default:
                    return $profile->scenario == 'nd-org' ?
                        $this->render($fm . '-org', [
                            'profile' => $profile, 
                            'toolbar' => $toolbar, 
                            'pp' => $progressPercent,
                            'e' => $e]) :
                        $this->render($fm . '-ind', [
                            'profile' => $profile,
                            'toolbar' => $toolbar, 
                            'pp' => $progressPercent,
                            'e' => $e]);
                    break;
            }
        }
    }

    /**
     * Image1 (i1)
     * Render: form#
     *
     * @return mixed
     */
    public function actionForm1($id, $e=0)
    {
        $fmNum = Self::$form['i1'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'i1';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 
        }
        if (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
        }
        if (Yii::$app->request->Post() && 
            $profile->save() && 
            $profile->setUpdateDate() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);   

        } else {   
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;      

            return $this->render($fm, ['profile' => $profile, 'pp' => $progressPercent, 'e' => $e]);
        }
    }

    /**
     * Image2 (i2)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm2($id, $e=0)
    {
        $fmNum = Self::$form['i2'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'i2';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if ($profile->type == 'Church') {                                                           // Handle linked image on church profile
            if (isset($_POST['remove'])) {                                                          // User selected to remove linked image
                $profile->updateAttributes(['image2' => NULL]);
                return $this->redirect(['form' . $fmNum, 'id' => $id, 'e' => $e]);
            } elseif (isset($_POST['use'])) {                                                       // User selected to use pastor image for church profile
                $profile->updateAttributes(['image2' => $_POST['use']]);
                return $this->redirect(['form' . $fmNum, 'id' => $id, 'e' => $e]);
            }        
        }
        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 
        }
        if (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
        }
        if (Yii::$app->request->Post() && 
            $profile->save() && 
            $profile->setUpdateDate() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]); // Give option to share linked pastor image with church profile

        } else {
            $imageLink = NULL;
            if (($profile->type == 'Church') && ($pastorLink = $profile->findSrPastor())) {
                if (isset($pastorLink->image2)) {
                    $imageLink = $pastorLink->image2;
                }
            }
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;

            return $this->render($fm, [
                'profile' => $profile, 
                'imageLink' => $imageLink, 
                'pp' => $progressPercent, 
                'e' => $e]);
        } 
    }

    /**
     * Location (lo)
     * Render:
     *     - form#-ind
     *     - form#-org
     *
     * @return mixed
     */
    public function actionForm3($id, $e=0)
    {
        $fmNum = Self::$form['lo'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->isIndividual($profile->type) ?
            $profile->scenario = 'lo-ind' :
            $profile->scenario = 'lo-org';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 
        }
        if (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
        }
        if ($profile->load(Yii::$app->request->Post()) && 
            $profile->handleFormLO() &&
            !($this->isDuplicate($id)) && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]); 

        } else {
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;
            if ($profile->org_country === NULL) {
                $profile->org_country = 'United States';                                            // Set default country to United States
            }
            if ($profile->show_map == Profile::MAP_PRIMARY) {
                $profile->map = 1;
            }
    
            if ($profile->scenario == 'lo-ind') {
                $title = 'Street or Mailing Address';
                $list = ArrayHelper::map(Country::find()->where('id>1')->andWhere(['RAN' => 0])->all(), 'printable_name', 'printable_name');
                return $this->render($fm . '-ind', [
                    'profile' => $profile, 
                    'title' => $title, 
                    'list' => $list,
                    'pp' => $progressPercent,
                    'e' => $e]);
            } else {
                $profile->type == 'Special Ministry' ?
                    $title = 'Minsitry Address' :
                    $title = $profile->type . ' Address';
                $list = ArrayHelper::map(Country::find()->where('id>1')->andWhere(['RAN' => 0])->all(), 'printable_name', 'printable_name');

                return $this->render($fm . '-org', [
                    'profile' => $profile, 
                    'title' => $title, 
                    'list' => $list,
                    'pp' => $progressPercent,
                    'e' => $e]);
            }
        } 
    }

    /**
     * Contact (co)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm4($id, $e=0)
    {
        $fmNum = Self::$form['co'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'co';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (!$social = $profile->social) {
            $social = new Social();
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($social->load(Yii::$app->request->Post()) &&
            $profile->load(Yii::$app->request->Post()) &&
            $profile->handleFormCO($social) &&
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);

        } else {
            empty($profile->org_country) ?
                $preferred[] = Country::find()
                    ->select('iso')
                    ->where(['printable_name' => $profile->ind_country])
                    ->scalar() :
                $preferred[] = Country::find()
                    ->select('iso')
                    ->where(['printable_name' => $profile->org_country])
                    ->scalar();
            if ($preferred == NULL) {
                $preferred[] = 'US';
            }

            $profile->isIndividual($profile->type) ?
                $ibnetEmail = Profile::urlName($profile->ind_last_name) . $profile->id . '@ibnet.org' :
                $ibnetEmail = Profile::urlName($profile->org_name) . $profile->id . '@ibnet.org';

            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;

            return $this->render($fm, [
                'profile' => $profile, 
                'social' => $social, 
                'preferred' => $preferred, 
                'ibnetEmail' => $ibnetEmail,
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

    /**
     * Staff (sf)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm5($id, $e=0)
    {
        $fmNum = Self::$form['sf'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->type == 'Church' ?
            $profile->scenario = 'sf-church' :
            $profile->scenario = 'sf-org';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

    // *********************** Add Staff *********************************
        if (isset($_POST['add'])) {
            if ($staff = Staff::findOne($_POST['add'])) {
                $staff->updateAttributes(['confirmed' => 1]);

                $staffProfile = $this->findProfile($staff->staff_id);
                $staffProfileOwner = User::findOne($staffProfile->user_id);
                MailController::initSendLink($profile, $staffProfile, $staffProfileOwner, 'SF', 'L');   // Notify staff profile owner of unconfirmed status
            
            }
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id, 'e' => $e]);             // Refresh page
    
    // *********************** Remove Staff *********************************    
        } elseif (isset($_POST['remove'])) {
            if ($staff = Staff::findOne($_POST['remove'])) {
                $staff->updateAttributes(['confirmed' => NULL]);

                $staffProfile = $this->findProfile($staff->staff_id);
                $staffProfileOwner = User::findOne($staffProfile->user_id);
                MailController::initSendLink($profile, $staffProfile, $staffProfileOwner, 'SF', 'UL'); // Notify staff profile owner of unconfirmed status

            }
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id, 'e' => $e]);             // Refresh page

    // *********************** Add Sr Pastor ******************************
        } elseif (isset($_POST['senior']) && $profile->handleFormSFSA($profile)) {
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id, 'e' => $e]);             // Refresh page
    
    // *********************** Remove Sr Pastor ******************************     
        } elseif (isset($_POST['clear']) && $profile->handleFormSFSR($profile)) {
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id, 'e' => $e]);             // Refresh page
        
        } elseif (isset($_POST['continue-org'])) {                                                  // Post coming from Org Staff page
            $profile->setProgress($fmNum);
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $profile->id, 'e' => $e]);
        
        } elseif (isset($_POST['exit-org'])) {
            $profile->setProgress($fmNum);
            return $this->redirect(['/profile-mgmt/my-profiles', 'id' => $profile->id]);        
        
        } elseif (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
    
        } elseif ($profile->load(Yii::$app->request->Post()) && $profile->validate() && 
            $profile->save() && $profile->setUpdateDate() && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);      
        
        } else {
            $profile->getFormattedNames();
            $srPastor = NULL;
            if ($staff = Staff::find()
                ->where(['ministry_id' => $profile->id])
                ->andWhere(['sr_pastor' => 1])
                ->one()) {
                if ($srPastor = $this->findActiveProfile($staff->staff_id)) {
                    $srPastor->getFormattedNames();
                }
            }

            $staffArray = Staff::find()
                ->select('
                    staff.id,
                    staff.staff_id, 
                    staff.staff_title, 
                    staff.confirmed, 
                    staff.sr_pastor,
                    profile.ind_first_name,
                    profile.spouse_first_name,
                    profile.ind_last_name,
                    profile.ind_city,
                    profile.ind_st_prov_reg,
                    profile.sub_type,
                    profile.home_church AS prof_home_church')
                ->innerJoinWith('profile', '`staff`.`staff_id` = `profile`.`id`')
                ->where(['staff.ministry_id' => $profile->id])
                ->andWhere(['staff.sr_pastor' => NULL])
                ->andWhere(['profile.status' => Profile::STATUS_ACTIVE])
                ->all();

            foreach ($staffArray as $staff) {
                $staff->{'profile'}->getFormattedNames();
            }

            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;

            return $profile->type == 'Church' ?
                $this->render($fm . '-church', [
                    'profile' => $profile, 
                    'srPastor' => $srPastor,
                    'staffArray' => $staffArray,
                    'pp' => $progressPercent,
                    'e' => $e]) :
                $this->render($fm . '-org', [
                    'profile' => $profile, 
                    'staffArray' => $staffArray, 
                    'fmNum' => $fmNum, 
                    'pp' => $progressPercent,
                    'e' => $e]);
        }
    }

    /**
     * Church Service Times (st)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm6($id, $e=0)
    {
        $fmNum = Self::$form['st'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'st';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if($profile->service_time_id != NULL) {                                                     // Load previously saved service times
            $serviceTime = $profile->serviceTime;
            $serviceTime->explodeTime();
        } else {
            $serviceTime = new ServiceTime();
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($serviceTime->load(Yii::$app->request->Post()) && 
            $serviceTime->handleFormST($profile) && 
            $profile->setUpdateDate() && 
            $profile->setProgress($fmNum)) {    
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);

        } else {
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;
            $days = [
                'Sun' => 'Sunday', 
                'Mon' => 'Monday', 
                'Tue' => 'Tuesday', 
                'Wed' => 'Wednesday', 
                'Thu' => 'Thursday', 
                'Fri' => 'Friday', 
                'Sat' => 'Saturday'];
            $hours = [
                '7' => '7 AM',
                '8' => '8 AM', 
                '9' => '9 AM', 
                '10' => '10 AM', 
                '11' => '11 AM', 
                '12' => '12 PM', 
                '13' => '1 PM', 
                '14' => '2 PM',
                '15' => '3 PM',
                '16' => '4 PM', 
                '17' => '5 PM', 
                '18' => '6 PM',
                '19' => '7 PM',
                '20' => '8 PM', 
                '21' => '9 PM'];
            $minutes = [
                '00' => '00',
                '05' => '05',
                '10' => '10',
                '15' => '15',
                '20' => '20',
                '25' => '25', 
                '30' => '30',
                '35' => '35',
                '40' => '40', 
                '45' => '45',
                '50' => '50',
                '55' => '55'];
            
            return $this->render($fm, [
                'profile' => $profile, 
                'serviceTime' => $serviceTime,
                'days' => $days,
                'hours' => $hours,
                'minutes' => $minutes,
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

    /**
     * Missionary Field Information (fi)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm7($id, $e=0)
    {
        $fmNum = Self::$form['fi'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);

        $profile->missionary_id == NULL ?
            $missionary = new Missionary() :
            $missionary = $profile->missionary;
        $missionary->scenario = 'fi';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($missionary->load(Yii::$app->request->Post()) && 
            $missionary->save() &&
            $profile->setUpdateDate() &&
            $profile->setProgress($fmNum)) {
            if ($profile->missionary_id != $missionary->id) {
                $profile->link('missionary', $missionary);                                          // Link new missionary record to profile
            }
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);     
        }
        $profile->status != Profile::STATUS_ACTIVE ?
            $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
            $progressPercent = NULL;

        return $this->render($fm, [
            'profile' => $profile, 
            'missionary' => $missionary, 
            'pp' => $progressPercent,
            'e' => $e]);
    }

    /**
     * Home Church (hc)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm8($id, $e=0)
    {
        $fmNum = Self::$form['hc'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id); 
        $profile->scenario = 'hc-required';
        $churchLink = NULL;
   
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }
        $profile->status != Profile::STATUS_ACTIVE ?
            $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
            $progressPercent = NULL;

        if (isset($_POST['remove']) && $profile->handleFormHCR()) {
            return $this->render($fm, [
                'profile' => $profile,
                'churchLink' => $churchLink,
                'pp' => $progressPercent,
                'e' => $e]);

        } elseif (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) && 
            $profile->handleFormHC() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);    

        } else {
            $churchLabel = $profile->getChurchLabel($profile->type, $profile->sub_type);
            if (isset($profile->home_church)) {
                if ($churchLink = $profile->homeChurch) {
                    $profile->select = $churchLink->id;
                    if ($churchLink->status != Profile::STATUS_ACTIVE) {                                // A linked profile is inactive.  Show an info message to user to reactivate the linked profile                                                              
                        Yii::$app->session->setFlash('info', 'The profile for ' .                       // If the linked church stays inactive then this profile will eventually go inactive as well
                            $churchLink->org_name . ' is currently inactive. Reactivate the 
                            profile or choose a different home church to proceed.');
                        $churchLink = NULL;
                    } else {
                        $profile->scenario = 'hc';                                                      // Don't require church for profiles that already have this set.  This is a work-around for the ajax loaded kartik dropdown, which cannot select an intiial value.
                    }
                }
            }
            if ($profile->show_map == Profile::MAP_CHURCH) {
                $profile->map = 1;
            }

            return $this->render($fm, [
                'profile' => $profile,
                'churchLink' => $churchLink,
                'churchLabel' => $churchLabel,
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

     /**
     * Church Plant (cp)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm9($id, $e=0)
    {
        $fmNum = Self::$form['cp'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->missionary_id == NULL ?
            $missionary = New Missionary() :
            $missionary = $profile->missionary;
        $missionary->scenario = 'cp';
        $ministryLink = NULL;

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }
        if ($profile->sub_type != 'Church Planter') {
             return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['remove']) && $missionary->handleFormCPR($profile)) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id, 'e' => $e]);
        
        } elseif (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($missionary->load(Yii::$app->request->Post()) &&
            $missionary->handleFormCP($profile) && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);

        } else {
            $profile->show_map == NULL ?
                $missionary->showMap = 0 :                                                         // Set default map choice to sending church map
                $missionary->showMap = $profile->show_map;
            
            if (isset($missionary->cp_pastor_at) && 
                ($ministryLink = $profile->findOne($missionary->cp_pastor_at))) {                  // Load any previously selected ministry_of (sending church)
                $missionary->ministrySelection = $ministryLink->id;
                
                if ($ministryLink->status != Profile::STATUS_ACTIVE) {                              // A linked profile is inactive.  Show info message to user to reactivate the linked profile
                    Yii::$app->session->setFlash('info', 'The profile for ' . 
                        $ministryLink->org_name . ' is currently inactive. Consider reactivating 
                        the profile in order to list it as a church planting ministry.');
                    $ministryLink = NULL;
                }
            }
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;
            $msg = $profile->show_map;
            $profile->show_map == Profile::MAP_CHURCH_PLANT ? 
                $missionary->showMap = 1 : 
                $missionary->showMap = NULL;

            return $this->render($fm, [
                'profile' => $profile,
                'missionary' => $missionary,
                'ministryLink' => $ministryLink,
                'msg' => $msg,
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

    /**
     * Parent Ministry (pm)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm10($id, $e=0)
    { 
        $fmNum = Self::$form['pm'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id); 
        if ($profile->type == 'Staff') {
            $profile->scenario = 'pm-required';
        } elseif ($profile->isIndividual($profile->type)) {
            $profile->scenario = 'pm-ind';
        } else {
            $profile->scenario = 'pm-org';
        }

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }

        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        $ministryLink = NULL;
        $more = false;

        $ministryLabel = $profile->getMinistryLabel($profile->type);
        if (isset($profile->ministry_of)) {
            $ministryLink = $profile->ministryOf;
            $profile->select = $ministryLink->id;
            if ($ministryLink->status != Profile::STATUS_ACTIVE) {                                  // A linked profile is inactive.  Show info message to user to reactivate the linked profile
                Yii::$app->session->setFlash('info', 'The profile for ' . 
                    $ministryLink->org_name . ' is currently inactive. Consider reactivating 
                    the profile in order to list it as a parent ministry here.');
                $ministryLink = NULL;
            }
        }
        
        if ($profile->show_map == Profile::MAP_MINISTRY) {
            $profile->map = $profile->show_map;
        }

        $profile->status != Profile::STATUS_ACTIVE ?
            $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
            $progressPercent = NULL;

        if (isset($_POST['more'])) {
            $more = true;

        } elseif (isset($_POST['remove'])) {                                                        // This code is a work-around for the Kartik Select2 AJAX drop-down widget which won't show the current ministry_of upon page load.
            if ($profile->isIndividual($profile->type) && 
                $staff = Staff::find()
                    ->where(['ministry_id' => $profile->ministry_of])
                    ->andWhere(['ministry_of' => 1])
                    ->one()) {
                
                $ministryProfile = $this->findProfile($staff->ministry_id);
                $ministryProfileOwner = User::findOne($ministryProfile->user_id);
                MailController::initSendLink($profile, $ministryProfile, $ministryProfileOwner, 'PM', 'UL'); // Notify individual ministry profile owner of unlink
                
                $staff->delete();
            } else {

                $ministryProfile = $profile->ministryOf;
                $ministryProfileOwner = User::findOne($ministryProfile->user_id);
                MailController::initSendLink($profile, $ministryProfile, $ministryProfileOwner, 'PM', 'UL'); // Notify organization ministry profile owner of unlink

            }
            $profile->updateAttributes(['ministry_of' => NULL]);
            $ministryLink = NULL;
            
        } elseif (isset($_POST['removeM']) && $staff = Staff::findOne($_POST['removeM'])) {
            
            $ministryProfile = $this->findProfile($staff->ministry_id);
            $ministryProfileOwner = User::findOne($ministryProfile->user_id);
            MailController::initSendLink($profile, $ministryProfile, $ministryProfileOwner, 'PM', 'UL');    // Notify ministry profile owner of unlink
            
            $staff->delete();
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id, 'e' => $e]);             // Refresh page

        } elseif (isset($_POST['submit']) && 
            $profile->load(Yii::$app->request->Post()) &&
            $profile->handleFormPM()) {
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id, 'e' => $e]);             // Save select and refresh page

         } elseif (isset($_POST['submitM']) &&
            $profile->load(Yii::$app->request->Post()) &&
            $profile->handleFormPMM()) {
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id, 'e' => $e]);             // Save select and refresh page

        } elseif (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) &&
            $profile->setProgress($fmNum) &&
            $profile->handleFormPM() &&
            $profile->handleFormPMM()) { 
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        $ministryM = Staff::find()                                                                  // Find all "other" staff
            ->where(['staff_id' => $profile->id])
            ->andWhere(['ministry_other' => 1])
            ->all();

        if ($profile->isIndividual($profile->type)) {
            return ($profile->type == 'Staff' || $profile->type == 'Evangelist') ?
                $this->render($fm . '-ind', [
                    'profile' => $profile,
                    'ministryLink' => $ministryLink,
                    'ministryLabel' => $ministryLabel,
                    'ministryM' => $ministryM,
                    'more' => $more,
                    'pp' => $progressPercent,
                    'e' => $e]) :
                $this->render($fm . '-other', [
                    'profile' => $profile,
                    'ministryM' => $ministryM,
                    'more' => $more,
                    'pp' => $progressPercent,
                    'e' => $e]);
        } else {
            return $this->render($fm . '-org', [
                'profile' => $profile,
                'ministryLink' => $ministryLink,
                'ministryLabel' => $ministryLabel,
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

    /**
     * Programs (pg)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm11($id, $e=0)
    { 
        $fmNum = Self::$form['pg'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id); 
        $profile->scenario = 'pg';
       
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }

        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        $profile->status != Profile::STATUS_ACTIVE ?
            $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
            $progressPercent = NULL;

        if (isset($_POST['save']) && $profile->setProgress($fmNum)) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]);

        } elseif (isset($_POST['continue']) && $profile->setProgress($fmNum)) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);

        } elseif ($profile->load(Yii::$app->request->Post())) {
            $profile->handleFormPG();
        }

        $programs = $profile->program;                                                              // Array of linked programs
        $profile->select = NULL;                                                                    // Initialize select

        return $this->render($fm, [
            'profile' => $profile,
            'programs' => $programs,
            'pp' => $progressPercent,
            'e' => $e]);
    }

    /**
     * Schools Attended (sa)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm12($id, $e=0)
    {
        $fmNum = Self::$form['sa'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'sa';

       if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) && 
            $profile->handleFormSA() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]); 

        } else {  
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;
            $profile->select = $profile->school;                                                     // Array of previously selected schools
            
            $schools = School::find()->orderBy('id')->all();
            $i=0;
            foreach ($schools as $school) {
                if ($i > 1) {
                    $school->formattedNames = $school->school . 
                        ' (' . $school->city . ', ' . $school->st_prov_reg . ')';
                } else {
                    $school->formattedNames = $school->school;
                }
                $i++;
            }
            $list = ArrayHelper::map($schools, 'id', 'formattedNames');

            return $this->render($fm, [
                'profile' => $profile,
                'list' => $list, 
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

    /**
     * School Levels (sl)
     * Render: Form#
     *
     * @return mixed
     */
    public function actionForm13($id, $e=0)
    {
        $fmNum = Self::$form['sl'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'sl';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) && 
            $profile->handleFormSL() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]); 

        } else {
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;
            $profile->select = $profile->schoolLevel;                                               // Previously selected school levels
            
            return $this->render($fm, [
                'profile' => $profile, 
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

    /**
     * Distinctives (di)
     * Render: form#
     *
     * @return mixed
     */
    public function actionForm14($id, $e=0)
    {
        $fmNum = Self::$form['di'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'di';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) && 
            $profile->save() && 
            $profile->setUpdateDate() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);  

        } else {
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;

            return $this->render($fm, [
                'profile' => $profile, 
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

    /**
     * Mission Agency (ma)
     * Render:
     *     - form#-church
     *     - form#-missionary
     *
     * @return mixed
     */
    public function actionForm15($id, $e=0)
    {
        $fmNum = Self::$form['ma'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'ma-church';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if ($profile->type == 'Missionary') {
            $profile->missionary_id == NULL ?
                $missionary = new Missionary() :
                $missionary = $profile->missionary;
            $missionary->scenario = 'ma-missionary';
        }

    // ************************* Remove Packet *********************************
        if (isset($_POST['remove'])) {
            $profile->type == 'Church' ?
                $profile->updateAttributes(['packet' => NULL]) :
                $missionary->updateAttributes(['packet' => NULL]);
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id, 'e' => $e]); 

        } elseif (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

    // **************************** Church POST *********************************
        } elseif ($profile->type == 'Church' && 
            $profile->load(Yii::$app->request->Post()) && 
            $profile->handleFormMA() && 
            $profile->setProgress($fmNum)) {

            if ($_POST['Profile']['missHousing'] == 'Y') {
                return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);

            } else {
                $fmNum++;
                $profile->setProgress($fmNum);                                                      // Set form complete for missionary housing
                return isset($_POST['save']) ?
                    $this->redirect(['/preview/view-preview', 'id' => $id]) :
                    $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]); 
            } 

    // ************************** Missionary POST *******************************    
        } elseif ($profile->type == 'Missionary' && 
            $missionary->load(Yii::$app->request->Post()) && 
            $missionary->handleFormMA($profile) && 
            $profile->setProgress($fmNum)) { 
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => ($fmNum + 1), 'id' => $id, 'e' => $e]);    // Skip Missionary Housing if checkbox is not checked
            
        } else {
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;

    // **************************** Church Render *********************************
            if($profile->type == 'Church') {
                $list1 = ArrayHelper::map(MissionAgcy::find()->where('id < 3')->orderBy('mission')->all(), 'id', 'mission');    // Append "All" and "Independent" to top of list
                $list2 = ArrayHelper::map(MissionAgcy::find()->where('id > 2')->orderBy('mission')->all(), 'id', 'mission'); 
                $list = array_replace($list1, $list2);
                $profile->select = $profile->missionAgcy;
                empty($profile->miss_housing_id) ?
                    $profile->missHousing = 'N' :                                                   // pre-populate profile->missHousing
                    $profile->missHousing = 'Y';
                return $this->render($fm . '-church', [
                    'profile' => $profile,
                    'list' => $list,
                    'pp' => $progressPercent,
                    'e' => $e]);
            } else {

    // ************************** Missionary Render *******************************
                $list1 = ArrayHelper::map(MissionAgcy::find()->where('id = 2')->orderBy('mission')->all(), 'id', 'mission');    // Append "Independent" to top of list
                $list2 = ArrayHelper::map(MissionAgcy::find()->where('id > 2')->orderBy('mission')->all(), 'id', 'mission'); 
                $list = array_replace($list1, $list2);
                return $this->render($fm . '-missionary', [
                    'profile' => $profile, 
                    'missionary' => $missionary, 
                    'list' => $list,
                    'pp' => $progressPercent,
                    'e' => $e]);  
            }
        }
    }

    /**
     * Missions Housing (mh)
     * Render: form#
     *
     * @return mixed
     */
    public function actionForm16($id, $e=0)
    {
        $fmNum = Self::$form['mh'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'mh';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($profile->miss_housing_id) &&
            $missHousing = MissHousing::findOne($profile->miss_housing_id)) {
            $missHousing->select = $missHousing->missHousingVisibility;                             // DB relation via junction table
        } else {
            $missHousing = new MissHousing();
        }
        
        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($missHousing->load(Yii::$app->request->Post()) && 
            $missHousing->handleFormMH($profile) &&
            $profile->setUpdateDate() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]); 

        } else {
            $list = ArrayHelper::map(MissHousingVisibility::find()->all(), 'id', 'approved', 'distinctive');
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;

            return $this->render($fm, [
                'profile' => $profile,
                'missHousing' => $missHousing,
                'list' => $list, 
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }

    /**
     * Associations (AS)
     * Render: 
     *     form#-church
     *     form#-ind
     *     form#-school
     *
     * @return mixed
     */
    public function actionForm17($id, $e=0)
    {
        $fmNum = Self::$form['as'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'as';
        if($profile->type == 'School') {
            $profile->scenario = 'as-school';
        } elseif ($profile->type == 'Church') {
            $profile->scenario = 'as-church';
        } else {
            $profile->scenario = 'as-ind';
        }

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) && 
            $profile->handleFormAS() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);  

        } else {
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;

            if ($profile->type == 'School') {
                $profile->select = $profile->accreditation;                                         // DB relation via junction table
                return $this->render($fm . '-school', [
                    'profile' => $profile, 
                    'pp' => $progressPercent,
                    'e' => $e]);
            }

            return $profile->isIndividual($profile->type) ?
                $this->render($fm . '-ind', [
                    'profile' => $profile, 
                    'pp' => $progressPercent,
                    'e' => $e]) :
                $this->render($fm . '-church', [
                    'profile' => $profile, 
                    'pp' => $progressPercent,
                    'e' => $e]);
        }
    }

    /**
     * Tag (ta)
     * Render: form#
     *
     * @return mixed
     */
    public function actionForm18($id, $e=0)
    {
        $fmNum = Self::$form['ta'];
        $fm = 'forms/form' . $fmNum;
        $profile = $this->findProfile($id);
        $profile->scenario = 'ta';

        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) && 
            $profile->handleFormTA() && 
            $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id, 'e' => $e]);  

        } else {
            $profile->select = $profile->tag;
            $profile->status != Profile::STATUS_ACTIVE ?
                $progressPercent = $profile->getProgressPercent(self::$formArray[$profile->type]) :
                $progressPercent = NULL;

            return $this->render($fm, [
                'profile' => $profile, 
                'pp' => $progressPercent,
                'e' => $e]);
        }
    }


/***************************************************************************************************
 ***************************************************************************************************
 *
 * End of data collection forms 
 *
 ***************************************************************************************************
\**************************************************************************************************/


    /**
     * Renders missing-fields if required form data is missing from the profile
     * @param string $id
     * @param array $missing
     * @return mixed
     */
    public function actionMissingForms($id, $fmNum) 
    {
        $profile = $this->findProfile($id);
        return $this->render('missingFields', ['profile' => $profile, 'fmNum' => $fmNum]);                         
    }

    /**
     * Renders duplicate-profile if a duplicate profile is found
     * @param string $id
     * @param array $missing
     * @return mixed
     */
    public function actionDuplicateProfile($id, $dupId) 
    {
        $profile = $this->findProfile($id);
        $duplicate = $this->findProfile($dupId);
        return $this->render('duplicateProfile', ['profile' => $profile, 'duplicate' => $duplicate]);                         
    }

    /**
     * Looks for a duplicate profile and redirects to duplicate-profile if found
     * @param string $id
     * @return mixed
     */
    public function isDuplicate($id) 
    {
        $profile = $this->findProfile($id);
        if ($profile->isIndividual($profile->type)) {
            $duplicate = Profile::find()                                                            // Check to see if a duplicate profile exists
                ->select('*')
                ->where(['ind_first_name' => $profile->ind_first_name])
                ->andwhere(['ind_last_name' => $profile->ind_last_name])
                ->andwhere(['ind_city' => $profile->ind_city])
                ->andwhere(['ind_st_prov_reg' => $profile->ind_st_prov_reg])
                ->andwhere(['ind_country' => $profile->ind_country])
                ->andwhere('id <> ' . $profile->id)
                ->andwhere(['status' => Profile::STATUS_ACTIVE])
                ->indexBy('id')
                ->one();
        } else {
            $duplicate = Profile::find()                                                            // Check to see if a duplicate profile exists
                ->select('*')
                ->where(['org_name' => $profile->org_name])
                ->andwhere(['org_city' => $profile->org_city])
                ->andwhere(['org_st_prov_reg' => $profile->org_st_prov_reg])
                ->andwhere(['org_country' => $profile->org_country])
                ->andwhere('id <> ' . $profile->id)
                ->andwhere(['status' => Profile::STATUS_ACTIVE])
                ->indexBy('id')
                ->one();
        }
        if ($duplicate) {                                                                           // Duplicate profile returned? Redirect to warning of duplicate profile
            return $this->redirect(['duplicate-profile', 
                'id' => $profile->id, 
                'dupId' => $duplicate->id]);                            
        }
        return false;
    }

    /**
     * Retrieves a file and echos it to the browser.
     * @param string $path
     */
    public function actionDownload($path) {
        $pathParts = pathinfo($path);
        $name = $pathParts['filename'];
        $filecontent = file_get_contents($path);
        header("Content-Type: application/pdf");
        header("Content-disposition: attachment; filename=$name");
        header("Pragma: no-cache");
        echo $filecontent;
        exit;
    }

    /**
     * Process Ajax request from Programs search box for churches
     * Return a table of 10 or fewer results from db.
     */
    public function actionProgramListAjax($q = NULL, $id = NULL) 
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, type, status, org_name AS text, org_city, org_st_prov_reg')
                ->from('profile')
                ->where(['type' => 'Special Ministry'])
                ->andWhere(['status' => Profile::STATUS_ACTIVE])
                ->andWhere('((`org_city` LIKE "%' . $q . '%") OR (`org_name` LIKE "%' . $q . '%"))')
                ->limit(10);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Profile::find($id)->org_name];
        }
        return $out;
    }
 
    /**
     * Process Ajax request from Parent Ministry search box for ministries
     * Return a table of 10 or fewer results from db.
     */
    public function actionMinistryListAjax($q = NULL, $id = NULL) 
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, type, status, org_name AS text, org_city, org_st_prov_reg')
                ->from('profile')
                ->where(['type' => 'Association'])   
                ->orWhere(['type' => 'Camp'])
                ->orWhere(['type' => 'Church'])
                ->orWhere(['type' => 'Fellowship'])
                ->orWhere(['type' => 'Special Ministry'])
                ->orWhere(['type' => 'Mission Agency'])
                ->orWhere(['type' => 'Music Ministry'])
                ->orWhere(['type' => 'Print Ministry'])
                ->orWhere(['type' => 'School'])
                ->andWhere(['status' => Profile::STATUS_ACTIVE])
                ->andwhere('id <> ' . $id)
                ->andWhere('((`org_city` LIKE "%' . $q . '%") OR (`org_name` LIKE "%' . $q . '%"))')
                ->limit(10);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Profile::find($id)->org_name];
        }
        return $out;
    }

    /**
     * Process Ajax request from Parent Ministry search box for churches
     * Return a table of 10 or fewer results from db.
     */
    public function actionChurchListAjax($q = NULL, $id = NULL) 
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, type, status, org_name AS text, org_city, org_st_prov_reg')
                ->from('profile')
                ->where(['type' => 'Church'])
                ->andWhere(['status' => Profile::STATUS_ACTIVE])
                ->andWhere('((`org_city` LIKE "%' . $q . '%") OR (`org_name` LIKE "%' . $q . '%"))')
                ->limit(10);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Profile::find($id)->org_name];
        }
        return $out;
    }

    /**
     * Process "Create a Forwarding Email" modal form
     * Updates user email and sends notification email to admin.
     */
    public function actionForwardingEmailAjax($id)
    {
        $fmNum = Self::$form['lo'];
        $profile = $this->findProfile($id);
        $profile->scenario = '';

        $profile->isIndividual($profile->type) ?
            $profile->email = Profile::urlName($profile->ind_last_name) . $profile->id . '@ibnet.org' :
            $profile->email = Profile::urlName($profile->org_name) . $profile->id . '@ibnet.org';

        $profile->load(Yii::$app->request->Post());
    
        if (Mail::sendForwardingEmailRqst($id, $profile->email, $profile->email_pvt)) {             // Send request to admin
            Yii::$app->session->setFlash('success', 
                'Your new email is pending and should be visible on your profile within 48 hours.  
                You may proceed with creating or updating your profile.');
        }

        return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id, 'e' => $e]);
    }

    /**
     * Return custom toolbar for Kartik markdown extension
     */
    public function getMarkdownToolbar()
    {

        return $toolbar = [
            [
                'buttons' => [
                    MarkdownEditor::BTN_BOLD => ['icon' => 'bold', 'title' => 'Bold'],
                    MarkdownEditor::BTN_ITALIC => ['icon' => 'italic', 'title' => 'Italic'],
                    MarkdownEditor::BTN_PARAGRAPH => ['icon' => 'font', 'title' => 'Paragraph'],
                    MarkdownEditor::BTN_NEW_LINE => ['icon' => 'text-height', 'title' => 'Append Line Break'],
                    MarkdownEditor::BTN_HEADING => ['icon' => 'header', 'title' => 'Heading', 'items' => [
                    MarkdownEditor::BTN_H1 => ['label' => 'Heading 1', 'options' => ['class' => 'kv-heading-1', 'title' => 'Heading 1 Style']],
                    MarkdownEditor::BTN_H2 => ['label' => 'Heading 2', 'options' => ['class' => 'kv-heading-2', 'title' => 'Heading 2 Style']],
                    MarkdownEditor::BTN_H3 => ['label' => 'Heading 3', 'options' => ['class' => 'kv-heading-3', 'title' => 'Heading 3 Style']],
                    MarkdownEditor::BTN_H4 => ['label' => 'Heading 4', 'options' => ['class' => 'kv-heading-4', 'title' => 'Heading 4 Style']],
                    MarkdownEditor::BTN_H5 => ['label' => 'Heading 5', 'options' => ['class' => 'kv-heading-5', 'title' => 'Heading 5 Style']],
                    MarkdownEditor::BTN_H6 => ['label' => 'Heading 6', 'options' => ['class' => 'kv-heading-6', 'title' => 'Heading 6 Style']],
                    ]],
                ],
            ],
            [
                'buttons' => [
                    MarkdownEditor::BTN_LINK => ['icon' => 'link', 'title' => 'URL/Link'],
                ],
            ],
            [
                'buttons' => [
                    MarkdownEditor::BTN_INDENT_L => ['icon' => 'indent-left', 'title' => 'Indent Text'],
                    MarkdownEditor::BTN_INDENT_R => ['icon' => 'indent-right', 'title' => 'Unindent Text'],
                ],
            ],
            [
                'buttons' => [
                    MarkdownEditor::BTN_UL => ['icon' => 'list', 'title' => 'Bulleted List'],
                    MarkdownEditor::BTN_OL => ['icon' => 'list-alt', 'title' => 'Numbered List'],
                    MarkdownEditor::BTN_DL => ['icon' => 'th-list', 'title' => 'Definition List'],
                ],
            ],
            [
                'buttons' => [
                    MarkdownEditor::BTN_HR => ['label' => MarkdownEditor::ICON_HR, 'title' => 'Horizontal Line', 'encodeLabel' => false],
                ],
            ],
            [
                'buttons' => [
                    MarkdownEditor::BTN_MAXIMIZE => ['icon' => 'fullscreen', 'title' => 'Toggle full screen', 'data-enabled' => true]
                ],
                'options' => ['class' => 'pull-right'] 
            ],
        ];
    }
}                     