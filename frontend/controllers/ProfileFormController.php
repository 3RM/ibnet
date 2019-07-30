<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\missionary\Missionary;
use common\models\profile\Association;
use common\models\profile\Country;
use common\models\profile\Fellowship;
use common\models\profile\FormsCompleted;
use common\models\profile\MissHousing;
use common\models\profile\MissionAgcy;
use common\models\profile\Profile;
use common\models\profile\ProfileForm;
use common\models\profile\ProfileMail;
use common\models\profile\School;
use common\models\profile\ServiceTime;
use common\models\profile\Social;
use common\models\profile\Staff;
use common\models\profile\Type;
use common\models\User;
use common\models\Utility;
use common\rbac\PermissionProfile;
use kartik\markdown\MarkdownEditor;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
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
    public $layout = 'main';

    // Determine sequence of forms
    public static $formArray = [     
                            // Form #   0   1   2   3   4   5   6   7   8   9   10  11  12  13  14  15  16  17  18 
        Profile::TYPE_PASTOR        => [1,  1,  1,  1,  1,  0,  0,  0,  1,  0,  1,  0,  1,  0,  0,  0,  0,  1,  0],  
        Profile::TYPE_EVANGELIST    => [1,  1,  1,  1,  1,  0,  0,  0,  1,  0,  1,  0,  1,  0,  0,  0,  0,  1,  0],
        Profile::TYPE_MISSIONARY    => [1,  1,  1,  1,  1,  0,  0,  1,  1,  1,  1,  0,  1,  0,  0,  1,  0,  0,  0],
        Profile::TYPE_CHAPLAIN      => [1,  1,  1,  1,  1,  0,  0,  1,  1,  0,  1,  0,  1,  0,  0,  1,  0,  0,  0],
        Profile::TYPE_STAFF         => [1,  1,  1,  1,  1,  0,  0,  0,  1,  0,  1,  0,  1,  0,  0,  0,  0,  0,  0],
        Profile::TYPE_CHURCH        => [1,  1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  1,  1,  1,  1,  0],
        Profile::TYPE_MISSION_AGCY  => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  0],
        Profile::TYPE_FELLOWSHIP    => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0],
        Profile::TYPE_ASSOCIATION   => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0],
        Profile::TYPE_CAMP          => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  0],
        Profile::TYPE_SCHOOL        => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  1,  0,  0,  0,  1,  0],
        Profile::TYPE_PRINT         => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  0],
        Profile::TYPE_MUSIC         => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  0],
        Profile::TYPE_SPECIAL       => [1,  1,  1,  1,  1,  1,  0,  0,  0,  0,  1,  0,  0,  0,  0,  0,  0,  0,  1]];

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
        'Field Information',
        'Home Church',
        'Church Plant',
        'Ministry or Organization',
        'Programs',
        'Schools Attended',
        'School Levels',
        'Distinctives',
        'Mission Agency',
        'Skip',
        'Associations/Fellowships',
        'Tags'
    ];

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
     * Render Forms list.  
     * User confirms agreement by clicking the "I Agree" button
     * @param integer $id
     * @return 
     */
    public function actionFormsMenu($id)
    {
        $profile = Profile::findProfile($id);
        $typeMask = self::$formArray[$profile->type];
        $pp = $profile->progressIfInactive;

        return $this->render('formsMenu', [
            'profile' => $profile,
            'formList' => self::$formList,
            'count' => count(Self::$form)-1,
            'typeMask' => $typeMask,
            'pp' => $pp]);
    }

    /**
     * FORM SEQUENCE
     * Redirect to the next form action, given a profile type and form number
     * Pass the profile id
     * @param integer $type Profile type
     * @param integer $fmNum Form number
     * @param integer $id
     * @return mixed
     */
    public function actionFormRoute($type, $fmNum, $id)
    {
        $value = 0;
        while ($value == 0) {
            $fmNum++;
            if ($fmNum > (count(Self::$form)-1)) {
                return $this->redirect(['/preview/view-preview', 'id' => $id]);
            }
            $value = self::$formArray[$type][$fmNum];
        }
        $action = 'form' . $fmNum;
        
        return $this->redirect([$action, 'id' => $id]);
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
     * @param integer $id
     * @return mixed
     */
    public function actionForm0($id)
    {
        $fmNum = Self::$form['nd'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        if ($profile->type == Profile::TYPE_FELLOWSHIP || $profile->type == Profile::TYPE_ASSOCIATION) {
            $profile->scenario = 'nd-flwsp_ass';
        } elseif ($profile->type == Profile::TYPE_MISSION_AGCY) {
            $profile->scenario = 'nd-miss_agency';
        } elseif ($profile->type == Profile::TYPE_SCHOOL) {
            $profile->scenario = 'nd-school';
        } else {
            $profile->category == Profile::CATEGORY_IND ?
                $profile->scenario = 'nd-ind':
                $profile->scenario = 'nd-org';
        }

        if (!Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 
        }
        if (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
        }
        if ($profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormND() 
            && $profile->setUpdateDate() 
            && $profile->setProgress($fmNum)) {

            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);

        } else {
            $toolbar = [
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
            $pp = $profile->progressIfInactive;

            switch($profile->type) {

                case Profile::TYPE_ASSOCIATION: 
                    $list = ArrayHelper::map(Association::find()
                        ->where(['status' => Profile::STATUS_ACTIVE])
                        ->andWhere('profile_id IS NULL')
                        ->orWhere(['profile_id' => $profile->id])
                        ->orderBy('name')
                        ->all(), 'id', 'name');
                    $toggle = NULL;
                    // Prepopulate select
                    if ($association = $profile->linkedAssociation) {
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
                        'pp' => $pp]);
                    break;

                case Profile::TYPE_FELLOWSHIP:
                    $list = ArrayHelper::map(Fellowship::find()
                        ->where(['status' => Profile::STATUS_ACTIVE])
                        ->andWhere('profile_id IS NULL')
                        ->orWhere(['profile_id' => $profile->id])
                        ->orderBy('name')
                        ->all(), 'id', 'name');
                    $toggle = NULL;
                    // Prepopulate select
                    if ($fellowship = $profile->linkedFellowship) {
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
                        'pp' => $pp]);
                    break;

                case Profile::TYPE_MISSION_AGCY:
                    $list = ArrayHelper::map(MissionAgcy::find()
                        ->where('id>2')
                        ->orWhere(['profile_id' => $profile->id])
                        ->orderBy('mission')
                        ->all(), 'id', 'mission');
                    $toggle = NULL;
                    // Prepopulate select
                    if ($missionAgcy = MissionAgcy::find()
                        ->where(['profile_id' => $profile->id])->one()) {
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
                        'pp' => $pp]);
                    break;

                case Profile::TYPE_SCHOOL: 
                    // Populate dropdown: array of all associations minus ones with other linked profiles
                    $schools = School::find()
                        ->select('id, school, city, st_prov_reg')
                        ->where(['>', 'id', 2]) // Exclude first two Ids (other secular, other Christian schools)
                        ->andWhere(['ib' => 1]) // Include only "IB" schools
                        ->andWhere(['<', 'closed', 1]) // Include only schools that are still open
                        ->andWhere(['IS', 'profile_id', NULL]) // List only schools that have not already been claimed by another profile
                        ->orWhere(['profile_id' => $profile->id])
                        ->indexBy('id')
                        ->orderBy('id')
                        ->all();
                    // Create 2D array of [id=>1][name=>XX] to format names as school (city, state)
                    foreach($schools as $i=>$school) {
                        $formatNames[$i]['id'] = $school->id;
                        $formatNames[$i]['name'] = $school->school . ' (' . 
                            $school->city . ', ' . $school->st_prov_reg . ')';
                    }
                    // Build map (key-value pairs) from 2D array [1=>XX] for use in dropdown list
                    $list = ArrayHelper::map($formatNames, 'name', 'name');
                    $toggle = NULL;
                    // Prepopulate select
                    if ($school = School::find()->where(['profile_id' => $profile->id])->one()) {
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
                        'pp' => $pp]);
                    break;  
                
                default:
                    return $profile->category == Profile::CATEGORY_IND ?
                        $this->render($fm . '-ind', [
                            'profile' => $profile,
                            'toolbar' => $toolbar, 
                            'pp' => $pp]) :
                        $this->render($fm . '-org', [
                            'profile' => $profile, 
                            'toolbar' => $toolbar, 
                            'pp' => $pp]);
                    break;
            }
        }
    }

    /**
     * Image1 (i1)
     * Render: form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm1($id)
    {
        $fmNum = Self::$form['i1'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'i1';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 
        }
        if (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
        }
        if (isset($_POST['banner1'])) {
            $profile->updateAttributes(['image1' => '@img.profile/banner1.jpg']);
            $profile->deleteOldImg('image1');
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id]);
        }
        if (isset($_POST['banner2'])) {
            $profile->updateAttributes(['image1' => '@img.profile/banner2.jpg']);
            $profile->deleteOldImg('image1');
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id]);
        }
        if (isset($_POST['banner3'])) {
            $profile->updateAttributes(['image1' => '@img.profile/banner3.jpg']);
            $profile->deleteOldImg('image1');
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id]);
        }
        if (isset($_POST['banner4'])) {
            $profile->updateAttributes(['image1' => '@img.profile/banner4.jpg']);
            $profile->deleteOldImg('image1');
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id]);
        }
        if (isset($_POST['banner5'])) {
            $profile->updateAttributes(['image1' => '@img.profile/banner5.jpg']);
            $profile->deleteOldImg('image1');
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id]);
        }
        if (isset($_POST['banner6'])) {
            $profile->updateAttributes(['image1' => '@img.profile/banner6.jpg']);
            $profile->deleteOldImg('image1');
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id]);
        }
        if (Yii::$app->request->Post()) {
            $profile->deleteOldImg('image1');
            if ($profile->save() && $profile->setUpdateDate() && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);  
            } 

        } else {   
            $pp = $profile->progressIfInactive;     

            return $this->render($fm, ['profile' => $profile, 'pp' => $pp]);
        }
    }

    /**
     * Image2 (i2)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm2($id)
    {
        $fmNum = Self::$form['i2'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'i2';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        // Handle linked image on church profile
        if ($profile->type == Profile::TYPE_CHURCH) {
            // User selected to remove linked image
            if (isset($_POST['remove'])) {
                $profile->updateAttributes(['image2' => NULL]);
                return $this->redirect(['form' . $fmNum, 'id' => $id]);
            // User selected to use pastor image for church profile
            } elseif (isset($_POST['use'])) {
                $profile->updateAttributes(['image2' => $_POST['use']]);
                return $this->redirect(['form' . $fmNum, 'id' => $id]);
            }        
        }
        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 
        }
        if (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
        }
        if (Yii::$app->request->Post()) {
            $profile->deleteOldImg('image2');
            if ($profile->save() 
                && $profile->setUpdateDate() 
                && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);  
            }

        } else {  // Give option to share linked pastor image with church profile
            
            $imageLink = NULL;
            if (($profile->type == Profile::TYPE_CHURCH) && ($pastorLink = $profile->srPastorChurchConfirmed)) {
                if (isset($pastorLink->image2)) {
                    $imageLink = $pastorLink->image2;
                }
            }
            $pp = $profile->progressIfInactive;

            return $this->render($fm, [
                'profile' => $profile, 
                'imageLink' => $imageLink, 
                'pp' => $pp]);
        } 
    }

    /**
     * Location (lo)
     * Render:
     *     - form#-ind
     *     - form#-org
     * @param integer $id
     * @return mixed
     */
    public function actionForm3($id)
    {
        $fmNum = Self::$form['lo'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->category == Profile::CATEGORY_IND ?
            $profile->scenario = 'lo-ind' :
            $profile->scenario = 'lo-org';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 
        }
        if (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
        }
        if ($profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormLO() 
            && $profile->setProgress($fmNum)) {
            if ($dup = $profile->duplicate) {
                return $this->redirect(['duplicate-profile', 'id' => $profile->id, 'dupId' => $dup->id]);
            }
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]); 

        } else {
            $pp = $profile->progressIfInactive;
            $profile->map = $profile->show_map == Profile::MAP_PRIMARY ? 1 : NULL;
            $list = ArrayHelper::map(Country::find()->where(['>', 'id', 1])->all(), 'printable_name', 'printable_name');
    
            if ($profile->scenario == 'lo-ind') {
                $title = 'Street or Mailing Address';
                return $this->render($fm . '-ind', [
                    'profile' => $profile, 
                    'title' => $title, 
                    'list' => $list,
                    'pp' => $pp]);
            } else {
                $profile->type == Profile::TYPE_SPECIAL ?
                    $title = 'Minsitry Address' :
                    $title = $profile->type . ' Address';
                return $this->render($fm . '-org', [
                    'profile' => $profile, 
                    'title' => $title, 
                    'list' => $list,
                    'pp' => $pp]);
            }
        } 
    }

    /**
     * Contact (co)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm4($id)
    {
        $fmNum = Self::$form['co'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'co';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (!$social = $profile->social) {
            $social = new Social();
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($social->load(Yii::$app->request->Post())
            && $profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormCO($social) 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);

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
            if (is_array($preferred) && empty($preferred[0])) {
                $preferred[0] = 'US';
            }

            $profile->category == Profile::CATEGORY_IND ?
                $ibnetEmail = Inflector::slug($profile->ind_last_name) . $profile->id . '@ibnet.org' :
                $ibnetEmail = Inflector::slug($profile->org_name) . $profile->id . '@ibnet.org';
            $pp = $profile->progressIfInactive;

            return $this->render($fm, [
                'profile' => $profile, 
                'social' => $social, 
                'preferred' => $preferred, 
                'ibnetEmail' => $ibnetEmail,
                'pp' => $pp]);
        }
    }

    /**
     * Staff (sf)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm5($id)
    {
        $fmNum = Self::$form['sf'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->type == Profile::TYPE_CHURCH ?
            $profile->scenario = 'sf-church' :
            $profile->scenario = 'sf-org';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

    // *********************** Add Staff *********************************
        if (isset($_POST['add'])) {
            if ($staff = Staff::findOne($_POST['add'])) {
                $staff->updateAttributes(['confirmed' => 1]);

                $staffProfile = Profile::findProfile($staff->staff_id);
                $staffProfileOwner = User::findOne($staffProfile->user_id);
                // Notify staff profile owner of unconfirmed status
                ProfileMail::initSendLink($profile, $staffProfile, $staffProfileOwner, 'SF', 'L');
            
            }
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id]);
    
    // *********************** Remove Staff *********************************    
        } elseif (isset($_POST['remove'])) {
            if ($staff = Staff::findOne($_POST['remove'])) {
                $staff->updateAttributes(['confirmed' => NULL]);

                $staffProfile = Profile::findProfile($staff->staff_id);
                $staffProfileOwner = User::findOne($staffProfile->user_id);
                // Notify staff profile owner of unconfirmed status
                ProfileMail::initSendLink($profile, $staffProfile, $staffProfileOwner, 'SF', 'UL');

            }
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id]);

    // *********************** Add Sr Pastor ******************************
        } elseif (isset($_POST['senior']) && $profile->handleFormSFSA()) {
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id]);
    
    // *********************** Remove Sr Pastor ******************************     
        } elseif (isset($_POST['clear']) && $profile->handleFormSFSR()) {
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id]);
        
        // Post coming from Org Staff page
        } elseif (isset($_POST['continue-org'])) {
            $profile->setProgress($fmNum);
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $profile->id]);
        
        } elseif (isset($_POST['exit-org'])) {
            $profile->setProgress($fmNum);
            return $this->redirect(['/profile-mgmt/my-profiles', 'id' => $profile->id]);        
        
        } elseif (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 
    
        } elseif ($profile->load(Yii::$app->request->Post()) 
            && $profile->validate() 
            && $profile->save() 
            && $profile->setUpdateDate() 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);      
        
        } else {

            $srPastor = $profile->srPastorChurchConfirmed;
            $staff = $profile->orgStaff;
            $pp = $profile->progressIfInactive;

            return $profile->type == Profile::TYPE_CHURCH ?
                $this->render($fm . '-church', [
                    'profile' => $profile, 
                    'srPastor' => $srPastor,
                    'staff' => $staff,
                    'pp' => $pp]) :
                $this->render($fm . '-org', [
                    'profile' => $profile, 
                    'staff' => $staff, 
                    'fmNum' => $fmNum, 
                    'pp' => $pp]);
        }
    }

    /**
     * Church Service Times (st)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm6($id)
    {
        $fmNum = Self::$form['st'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'st';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        // Load previously saved service times
        if ($serviceTime = $profile->serviceTime) {
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
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);

        } else {
            $pp = $profile->progressIfInactive;
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
                'pp' => $pp]);
        }
    }

    /**
     * Missionary Field Information (fi)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm7($id)
    {
        $fmNum = Self::$form['fi'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);

        if (!$missionary = $profile->missionary) {
            $missionary = new Missionary();
            $missionary->user_id = Yii::$app->user->identity->id;
        }
        $missionary->scenario = 'fi';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($missionary->load(Yii::$app->request->Post()) 
            && $missionary->save() 
            && $profile->setUpdateDate() 
            && $profile->setProgress($fmNum)) {
            if (!$profile->missionary) {
                // Link new missionary record to profile
                $profile->link('missionary', $missionary);
                // Link profile to new missionary record
                $missionary->link('profile', $profile);
            }
            $profile->updateAttributes(['url_loc' =>  $missionary->field]);

            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);     
        }
        $pp = $profile->progressIfInactive;

        return $this->render($fm, [
            'profile' => $profile, 
            'missionary' => $missionary, 
            'pp' => $pp]);
    }

    /**
     * Home Church (hc)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm8($id)
    {
        $fmNum = Self::$form['hc'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id); 
        $profile->scenario = 'hc';
        $churchLink = NULL;
   
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }
        $pp = $profile->progressIfInactive;

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post())) {
            $profile->handleFormHC(); 
            $profile->setProgress($fmNum);
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);

        }
        
        $initialData = NULL;
        if ($chLink = $profile->homeChurch) {  
            if ($chLink->status == Profile::STATUS_ACTIVE) { 
                $initialData = [$chLink->id => $chLink->org_name];
            } else {
                Yii::$app->session->setFlash('info', 'The profile for ' .
                    $chLink->org_name . ' is currently inactive. Reactivate the 
                    profile or choose a different home church to proceed.');
                $profile->home_church = NULL;
            }
        }
        // Exclude church plant church from home church search
        $exclude = ($missionary = $profile->missionary) ? $missionary->cp_pastor_at : NULL;  
              
        $profile->map = $profile->show_map == Profile::MAP_CHURCH ? 1 : NULL;

        return $this->render($fm, [
            'profile' => $profile,
            'initialData' => $initialData,
            'pp' => $pp,
            'exclude' => $exclude]);
    }

     /**
     * Church Plant (cp)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm9($id)
    {
        $fmNum = Self::$form['cp'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $missionary = $profile->missionary ?? new Missionary();
        $missionary->scenario = 'cp';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }
        if ($profile->sub_type != Profile::SUBTYPE_MISSIONARY_CP) {
            // Mark form as "reviewed"
            $profile->setProgress($fmNum);
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($missionary->load(Yii::$app->request->Post()) 
            && $missionary->handleFormCP($profile) 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);

        } else {

            $initialData = NULL;
            if ($churchPlant = $missionary->churchPlant) {
                if ($churchPlant->status == Profile::STATUS_ACTIVE) {
                    $initialData = [$churchPlant->id => $churchPlant->org_name];
                } else {
                    Yii::$app->session->setFlash('info', 'The profile for ' . 
                        $churchPlant->org_name . ' is currently inactive. Consider reactivating 
                        the profile in order to list it as a parent ministry here.');
                    $profile->cp_pastor = NULL;
                    $missionary->cp_pastor_at = NULL;
                }
            }
            $pp = $profile->progressIfInactive;
            $msg = $profile->show_map;
            $missionary->showMap = $profile->show_map == Profile::MAP_CHURCH_PLANT ? 1 : NULL;

            return $this->render($fm, [
                'profile' => $profile,
                'missionary' => $missionary,
                'initialData' => $initialData,
                'msg' => $msg,
                'pp' => $pp,]);
        }
    }

    /**
     * Parent Ministry (pm)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm10($id)
    { 
        $fmNum = Self::$form['pm'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id); 
        if ($profile->type == Profile::TYPE_STAFF) {
            $profile->scenario = 'pm-required';
        } elseif ($profile->category == Profile::CATEGORY_IND) {
            $profile->scenario = 'pm-ind';
        } else {
            $profile->scenario = 'pm-org';
        }

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }

        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        $initialData = NULL;
        if ($parentMinistry = $profile->parentMinistry) {
            if ($parentMinistry->status == Profile::STATUS_ACTIVE) {
                $initialData = [$parentMinistry->id => $parentMinistry->org_name];
            } else {
                Yii::$app->session->setFlash('info', 'The profile for ' . 
                    $parentMinistry->org_name . ' is currently inactive. Consider reactivating 
                    the profile in order to list it as a parent ministry here.');
                $profile->ministry_of = NULL;
            }
        }
        
        $profile->map = $profile->show_map == Profile::MAP_MINISTRY ? $profile->show_map : NULL;
        $pp = $profile->progressIfInactive;
        $more = false;

        if (isset($_POST['more'])) {
            $more = true;

        } elseif (isset($_POST['removeM']) 
            && $staff = Staff::findOne($_POST['removeM']) 
            && $parentMinistry = $profile->parentMinistry) {
            
            // Notify ministry profile owner of unlink
            $parentMinistryOwner = $parentMinistry->user;
            ProfileMail::initSendLink($profile, $parentMinistry, $parentMinistryOwner, 'PM', 'UL');
            $staff->delete();
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id]);

        } elseif (isset($_POST['submit-more']) 
            && $profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormPMM()) {
            return $this->redirect(['form' . $fmNum, 'id' => $profile->id]);

        } elseif (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) 
            && $profile->setProgress($fmNum) 
            && $profile->handleFormPM() 
            && $profile->handleFormPMM()) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        $otherMinistries = $profile->otherMinistries;
        if ($profile->category == Profile::CATEGORY_IND) {
            return ($profile->type == Profile::TYPE_STAFF || $profile->type == Profile::TYPE_EVANGELIST) ?
                $this->render($fm . '-ind', [
                    'profile' => $profile,
                    'initialData' => $initialData,
                    'otherMinistries' => $otherMinistries,
                    'more' => $more,
                    'pp' => $pp]) :
                $this->render($fm . '-other', [
                    'profile' => $profile,
                    'otherMinistries' => $otherMinistries,
                    'more' => $more,
                    'pp' => $pp]);
        } else {
            return $this->render($fm . '-org', [
                'profile' => $profile,
                'initialData' => $initialData,
                'pp' => $pp]);
        }
    }

    /**
     * Programs (pg)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm11($id)
    { 
        $fmNum = Self::$form['pg'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id); 
        $profile->scenario = 'pg';
       
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }

        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        $pp = $profile->progressIfInactive;

        if (isset($_POST['save']) && $profile->setProgress($fmNum)) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]);

        } elseif (isset($_POST['continue']) && $profile->setProgress($fmNum)) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);

        } elseif ($profile->load(Yii::$app->request->Post())) {
            $profile->handleFormPG();
        }

        $programs = $profile->programs;
        $profile->select = NULL;

        return $this->render($fm, [
            'profile' => $profile,
            'programs' => $programs,
            'pp' => $pp]);
    }

    /**
     * Schools Attended (sa)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm12($id)
    {
        $fmNum = Self::$form['sa'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'sa';

       if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormSA() 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]); 

        } else {  

            $pp = $profile->progressIfInactive;
            $profile->select = $profile->schoolsAttended;
            $first = School::find()->where('id <= 2')->orderBy('id')->all();
            $second = School::find()->where('id > 2')->orderBy('school')->all();
            $schools = array_merge($first, $second);
            foreach ($schools as $i=>$school) {
                if ($i > 1) {
                    $school->formattedNames = $school->school . 
                        ' (' . $school->city . ', ' . $school->st_prov_reg . ')';
                } else {
                    $school->formattedNames = $school->school;
                }
            }
            $list = ArrayHelper::map($schools, 'id', 'formattedNames');

            return $this->render($fm, [
                'profile' => $profile,
                'list' => $list, 
                'pp' => $pp]);
        }
    }

    /**
     * School Levels (sl)
     * Render: Form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm13($id)
    {
        $fmNum = Self::$form['sl'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'sl';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormSL() 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]); 

        } else {
            $pp = $profile->progressIfInactive;
            $profile->select = $profile->schoolLevels;
            
            return $this->render($fm, [
                'profile' => $profile, 
                'pp' => $pp]);
        }
    }

    /**
     * Distinctives (di)
     * Render: form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm14($id)
    {
        $fmNum = Self::$form['di'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'di';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) 
            && $profile->save() 
            && $profile->setUpdateDate() 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);  

        } else {
            $pp = $profile->progressIfInactive;

            return $this->render($fm, [
                'profile' => $profile, 
                'pp' => $pp]);
        }
    }

    /**
     * Mission Agency (ma)
     * Render:
     *     - form#-church
     *     - form#-missionary
     * @param integer $id
     * @return mixed
     */
    public function actionForm15($id)
    {
        $fmNum = Self::$form['ma'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'ma-church';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (($profile->type == Profile::TYPE_MISSIONARY) || ($profile->type == Profile::TYPE_CHAPLAIN)) {
            $missionary = $profile->missionary ?? new Missionary();
            $missionary->scenario = $profile->type == Profile::TYPE_MISSIONARY ? 'ma-missionary' : 'ma-chaplain';
        }

    // ************************* Remove Packet *********************************
        if (isset($_POST['remove'])) {
            $profile->type == Profile::TYPE_CHURCH ?
                $profile->updateAttributes(['packet' => NULL]) :
                $missionary->updateAttributes(['packet' => NULL]);
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id]); 

        } elseif (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

    // **************************** Church POST *********************************
        } elseif ($profile->type == Profile::TYPE_CHURCH 
            && $profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormMA() 
            && $profile->setProgress($fmNum)) {

            if ($_POST['Profile']['housingSelect'] == 'Y') {
                return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);

            } else {
                $fmNum++;
                $profile->setProgress($fmNum);
                return isset($_POST['save']) ?
                    $this->redirect(['/preview/view-preview', 'id' => $id]) :
                    $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]); 
            } 

    // ************************** Missionary POST *******************************    
        } elseif (($profile->type == Profile::TYPE_MISSIONARY || $profile->type == Profile::TYPE_CHAPLAIN) 
            && $missionary->load(Yii::$app->request->Post()) 
            && $missionary->handleFormMA($profile) 
            && $profile->setProgress($fmNum)) { 
            // Skip Missionary Housing if checkbox is not checked
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => ($fmNum + 1), 'id' => $id]);
            
        } else {
           $pp = $profile->progressIfInactive;

    // **************************** Church Render *********************************
            if($profile->type == Profile::TYPE_CHURCH) {
                $list1 = ArrayHelper::map(MissionAgcy::find()->where('id < 3')->orderBy('mission')->all(), 'id', 'mission');    // Append "All" and "Independent" to top of list
                $list2 = ArrayHelper::map(MissionAgcy::find()->where('id > 2')->orderBy('mission')->all(), 'id', 'mission'); 
                $list = array_replace($list1, $list2);
                $profile->select = $profile->missionAgcys;
                $profile->housingSelect = $profile->missHousing ? 'Y' : 'N';
                return $this->render($fm . '-church', [
                    'profile' => $profile,
                    'list' => $list,
                    'pp' => $pp]);
            } else {

    // ************************** Missionary Render *******************************
                $list1 = ArrayHelper::map(MissionAgcy::find()->where('id = 2')->orderBy('mission')->all(), 'id', 'mission');    // Append "Independent" to top of list
                $list2 = ArrayHelper::map(MissionAgcy::find()->where('id > 2')->orderBy('mission')->all(), 'id', 'mission'); 
                $list = array_replace($list1, $list2);
                return $this->render($fm . '-missionary', [
                    'profile' => $profile, 
                    'missionary' => $missionary, 
                    'list' => $list,
                    'pp' => $pp]);  
            }
        }
    }

    /**
     * Missions Housing (mh)
     * Render: form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm16($id)
    {
        $fmNum = Self::$form['mh'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'mh';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        $missHousing = $profile->missHousing ? $profile->missHousing : new MissHousing();
        
        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($missHousing->load(Yii::$app->request->Post()) 
            && $missHousing->handleFormMH($profile) 
            && $profile->setUpdateDate() 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]); 

        } else {
            $pp = $profile->progressIfInactive;

            return $this->render($fm, [
                'profile' => $profile,
                'missHousing' => $missHousing,
                'pp' => $pp]);
        }
    }

    /**
     * Associations (AS)
     * Render: 
     *     form#-church
     *     form#-ind
     *     form#-school
     * @param integer $id
     * @return mixed
     */
    public function actionForm17($id)
    {
        $fmNum = Self::$form['as'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        if($profile->type == Profile::TYPE_SCHOOL) {
            $profile->scenario = 'as-school';
        } elseif ($profile->type == Profile::TYPE_CHURCH) {
            $profile->scenario = 'as-church';
        } else {
            $profile->scenario = 'as-ind';
        }

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']);

        } elseif (isset($_POST['add']) 
            && $profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormAS()) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum-1, 'id' => $id]); 

        } elseif ($profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormAS() 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);  

        } else {
            $pp = $profile->progressIfInactive;

            if ($profile->type == Profile::TYPE_SCHOOL) {
                $profile->select = $profile->accreditations;
                return $this->render($fm . '-school', [
                    'profile' => $profile, 
                    'pp' => $pp]);
            } elseif ($profile->type == Profile::TYPE_CHURCH) {
                $profile->select = $profile->fellowships;
                $profile->selectM = $profile->associations;                                      
                return $this->render($fm . '-church', [
                    'profile' => $profile, 
                    'pp' => $pp]);
            } else {
                $profile->select = $profile->fellowships;
                return $this->render($fm . '-ind', [
                    'profile' => $profile, 
                    'pp' => $pp]);
            }

        }
    }

    /**
     * Tag (ta)
     * Render: form#
     * @param integer $id
     * @return mixed
     */
    public function actionForm18($id)
    {
        $fmNum = Self::$form['ta'];
        $fm = 'forms/form' . $fmNum;
        $profile = Profile::findProfile($id);
        $profile->scenario = 'ta';

        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!self::$formArray[$profile->type][$fmNum]) {
            return $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);
        }

        if (isset($_POST['cancel'])) {
            return $this->redirect(['/preview/view-preview', 'id' => $id]); 

        } elseif (isset($_POST['exit'])) {
            return $this->redirect(['/profile-mgmt/my-profiles']); 

        } elseif ($profile->load(Yii::$app->request->Post()) 
            && $profile->handleFormTA() 
            && $profile->setProgress($fmNum)) {
            return isset($_POST['save']) ?
                $this->redirect(['/preview/view-preview', 'id' => $id]) :
                $this->redirect(['form-route', 'type' => $profile->type, 'fmNum' => $fmNum, 'id' => $id]);  

        } else {
            $profile->select = $profile->tags;
            $pp = $profile->progressIfInactive;

            return $this->render($fm, [
                'profile' => $profile, 
                'pp' => $pp]);
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
     * @param integer $id
     * @param array $missing
     * @return mixed
     */
    public function actionMissingForms($id, $missing) 
    {
        $profile = Profile::findProfile($id);
        return $this->render('missingFields', ['profile' => $profile, 'missing' => $missing]);                         
    }

    /**
     * Renders duplicate-profile if a duplicate profile is found
     * @param integer $id
     * @param integer $dupId
     * @return mixed
     */
    public function actionDuplicateProfile($id, $dupId) 
    {
        $profile = Profile::findProfile($id);
        $duplicate = Profile::findProfile($dupId);
        return $this->render('duplicateProfile', ['profile' => $profile, 'duplicate' => $duplicate]);                         
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

}                     