<?php

namespace frontend\controllers;

use common\models\profile\Association;
use common\models\profile\Fellowship;
use common\models\profile\Profile;
use common\models\profile\Staff;
use common\models\profile\Social;
use frontend\controllers\ProfileFormController;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class PreviewController extends ProfileFormController
{

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
     * Redirect to the proper profile preview page, given the profile id
     * @return mixed
     */
    public function actionViewPreview($id, $city=NULL, $name=NULL)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        $previewPage = 'preview-' . ProfileController::$profilePageArray[$profile->type];                                     // Handle all other profile types
        
        return $this->redirect([$previewPage, 'id' => $profile->id, 'city' => $city, 'name' => $name]);
    }

    /**
     * Render association profile preview
     * @return mixed
     */
    public function actionPreviewAssociation($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Association') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $social = NULL;
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewFlwshpAss', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);       // If user tries to access wrong profile action, reroute to the correct one
        } 
    }

    /**
     * Render fellowship profile preview
     * @return mixed
     */
    public function actionPreviewFellowship($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Fellowship') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $social = NULL;
            if ($profile->social_id) {
                $social = $profile->social;
            }
            $typeMask = ProfileFormController::$formArray[$profile->type];

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewFlwshpAss', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id,]);
        }
    }

        /**
     * Render camp profile preview
     * @return mixed
     */
    public function actionPreviewCamp($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Camp') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $church = $this->findActiveProfile($profile->ministry_of)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render chaplain profile preview
     * @return mixed
     */
    public function actionPreviewChaplain($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Chaplain') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $profile->getformattedNames();
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;
            if ($profile->ministry_of && 
                $church = $this->findActiveProfile($profile->home_church)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            $schoolsAttended = $profile->school;                                                    // relational db call
            if ($profile->social_id) {
                $social = $profile->social;
            }
            if ($profile->flwship_id) {                                                             // Retrieve fellowship
                $fellowship = $this->findFellowship($profile->flwship_id);                              
                $flwshipLink = $this->findActiveProfile($fellowship->profile_id);                   // Only link to active profiles
            }

            if ($profile->ind_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($church &&  $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewEvangelist', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'fellowship' => $fellowship,
                'flwshipLink' => $flwshipLink,
                'church' => $church,
                'churchLink' => $churchLink,
                'schoolsAttended' => $schoolsAttended,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render church profile preview
     * @return mixed
     */
    public function actionPreviewChurch($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Church') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $profile->getformattedNames();
            $pastorLink = NULL;
            $social = NULL;
            $programs = $profile->program;
            $association = NULL;
            $assLink = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;
            if ($staff = Staff::find()
                ->where(['ministry_id' => $profile->id])
                ->andWhere(['sr_pastor' => 1])
                ->one()) {
                $pastorLink = $this->findActiveProfile($staff->staff_id);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }
            if (!$ministries = Profile::find()
                ->where(['status' => Profile::STATUS_ACTIVE])
                ->andWhere(['ministry_of' => $profile->id])
                ->all()) {
                $ministries = NULL;
            }
            if ($profile->ass_id) {                                                                 // Retrieve fellowship
                $association = $this->findAssociation($profile->ass_id);
                $assLink = $this->findActiveProfile($association->profile_id);                      // Only link to active profiles
            }
            if ($profile->flwship_id) {                                                             // Retrieve fellowship
                $fellowship = $this->findFellowship($profile->flwship_id);                              
                $flwshipLink = $this->findActiveProfile($fellowship->profile_id);                   // Only link to active profiles
            }
            
            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewChurch', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'pastorLink' => $pastorLink,
                'association' => $association,
                'assLink' => $assLink,
                'ministries' => $ministries,
                'programs' => $programs,
                'fellowship' => $fellowship,
                'flwshipLink' => $flwshipLink,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render evangelist profile preview
     * @return mixed
     */
    public function actionPreviewEvangelist($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Evangelist') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $profile->getformattedNames();
            $church = NULL;
            $churchLink = NULL;
            $ministry = NULL;
            $ministryLink = NULL;
            $social = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;
            if ($profile->home_church && 
                $church = $this->findActiveProfile($profile->home_church)) {
                $churchLink = $church->org_name . ', ' . $church->org_city;
                $church->org_st_prov_reg ? $churchLink .= ', ' . $church->org_st_prov_reg : NULL;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->home_church && 
                $church = $this->findActiveProfile($profile->home_church)) {
                $churchLink = $church->org_name . ', ' . $church->org_city;
                $church->org_st_prov_reg ? $churchLink .= ', ' . $church->org_st_prov_reg : NULL;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->ministry_of && 
                $ministry = $this->findActiveProfile($profile->ministry_of)) {
                $ministryLink = $ministry->org_name . ', ' . $ministry->org_city;
                $ministry->org_st_prov_reg ? $ministryLink .= ', ' . $ministry->org_st_prov_reg : NULL;
                $ministry->org_country == 'United States' ? NULL : 
                    ($ministryLink .= ', ' . $ministry->org_country);
            }
            $schoolsAttended = $profile->school;                                                    // relational db call
            if ($profile->social_id) {
                $social = $profile->social;
            }
            if ($profile->flwship_id) {                                                             // Retrieve fellowship
                $fellowship = $this->findFellowship($profile->flwship_id);                              
                $flwshipLink = $this->findActiveProfile($fellowship->profile_id);                   // Only link to active profiles
            }

            if ($profile->ind_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                $loc = explode(',', $church->org_loc);
            } elseif ($ministry && $ministry->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                $loc = explode(',', $ministry->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewEvangelist', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'fellowship' => $fellowship,
                'flwshipLink' => $flwshipLink,
                'church' => $church,
                'churchLink' => $churchLink,
                'ministry' => $ministry,
                'ministryLink' => $ministryLink,
                'schoolsAttended' => $schoolsAttended,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render Mission Agency profile preview
     * @return mixed
     */
    public function actionPreviewMissionAgency($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Mission Agency') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $ministry = NULL;
            $ministryLink = NULL;
            $social = NULL;
            $church = NULL;
            $churchLink = NULL;
            if ($profile && 
                $profile->ministry_of && 
                $church = $this->findActiveProfile($profile->ministry_of)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render missionary profile preview
     * @return mixed
     */
    public function actionPreviewMissionary($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!$missionary = $profile->missionary) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Missionary') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $profile->getformattedNames();
            $church = NULL;
            $churchLink = NULL;
            $churchPlant = NULL;
            $churchPlantLink = NULL;
            $social = NULL;
            $m = NULL;
            $mission = NULL;
            $missionLink = NULL;
            if ($profile->home_church && $church = $this->findActiveProfile($profile->home_church)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                if ($church->org_country != 'United States') { 
                    ($churchLink .= ', ' . $church->org_country);
                }
            }
            if ($missionary->cp_pastor_at && $churchPlant = $missionary->churchPlant) {
                $churchPlantLink = $churchPlant->org_name . ', ' . 
                $churchPlant->org_city . ', ' . $churchPlant->org_st_prov_reg;
                if ($churchPlant->org_country != 'United States') { 
                    ($churchPlantLink .= ', ' . $churchPlant->org_country);
                }
            }
            if ($mission = $missionary->missionAgcy) {
                if ($mission->profile_id) {
                    $missionLink = $this->findActiveProfile($mission->profile_id);
                }
            }

            if ($profile->show_map == Profile::MAP_PRIMARY && !empty($profile->ind_loc)) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                $loc = explode(',', $church->org_loc);
            } elseif ($churchPlant && $churchPlant->org_loc && $profile->show_map == Profile::MAP_CHURCH_PLANT) {
                $loc = explode(',', $churchPlant->org_loc);
            } else {
                $loc = NULL;
            }

            $schoolsAttended = $profile->school;
            if ($profile->social_id) {
                $social = $profile->social;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewMissionary', [
                'profile' => $profile,
                'loc' => $loc,
                'missionary' => $missionary,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'churchPlant' => $churchPlant,
                'churchPlantLink' => $churchPlantLink,
                'schoolsAttended' => $schoolsAttended,
                'mission' => $mission,
                'missionLink' => $missionLink,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render Music Ministry profile preview
     * @return mixed
     */
    public function actionPreviewMusic($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Music Ministry') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $church = $this->findActiveProfile($profile->ministry_of)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render special organization profile preview
     * @return mixed
     */
    public function actionPreviewOrganization($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Special') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $church = $this->findActiveProfile($profile->ministry_of)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render pastor profile preview
     * @return mixed
     */
    public function actionPreviewPastor($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Pastor') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $profile->getformattedNames();
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;
            if ($profile->home_church && 
                $church = $this->findActiveProfile($profile->home_church)) {
                $churchLink = $church->org_name . ', ' . $church->org_city;
                $church->org_st_prov_reg ? $churchLink .= ', ' . $church->org_st_prov_reg : NULL;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            $schoolsAttended = $profile->school;
            if ($profile->social_id) {
                $social = $profile->social;
            }
            if ($profile->flwship_id) {                                                             // Retrieve fellowship
                $fellowship = $this->findFellowship($profile->flwship_id);                              
                $flwshipLink = $this->findActiveProfile($fellowship->profile_id);                   // Only link to active profiles
            }

            if ($profile->ind_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewPastor', [
                'profile' => $profile,
                'loc' => $loc,
                'churchLink' => $churchLink,
                'church' => $church,
                'social' => $social,
                'fellowship' => $fellowship,
                'flwshipLink' => $flwshipLink,
                'schoolsAttended' => $schoolsAttended,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render print ministry profile preview
     * @return mixed
     */
    public function actionPreviewPrint($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Print Ministry') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $church = $this->findActiveProfile($profile->ministry_of)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render school profile preview
     * @return mixed
     */
    public function actionPreviewSchool($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'School') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            $accreditation = NULL;
            if ($profile->ministry_of && 
                $church = $this->findActiveProfile($profile->ministry_of)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }
            $schoolLevel = $profile->schoolLevel;                                                   // Create array of previously selected school levels
            usort($schoolLevel, [$this, 'level_sort']);                                             // Sort the multidimensional array
            
            $accreditations = $profile->accreditation;

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewSchool', [
                'profile' => $profile, 
                'loc' => $loc,
                'social' => $social,
                'schoolLevel' => $schoolLevel,
                'church' => $church,
                'churchLink' => $churchLink,
                'accreditations' => $accreditations,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render special ministry profile
     * @return mixed
     */
    public function actionPreviewSpecialMinistry($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Special Ministry') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }
        
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $church = $this->findActiveProfile($profile->ministry_of)) {
                $churchLink = $church->org_name . ', ' . 
                    $church->org_city . ', ' . $church->org_st_prov_reg;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->show_map == Profile::MAP_PRIMARY && !empty($profile->org_loc)) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Render evangelist profile preview
     * @return mixed
     */
    public function actionPreviewStaff($id)
    {
        if (!$profile = $this->findProfile($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->user->can('updateProfile', ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == 'Staff') {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $profile->getformattedNames();
            $ministry = NULL;
            $ministryLink = NULL;
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;

            if ($profile->ministry_of && 
                $ministry = $this->findActiveProfile($profile->ministry_of)) {
                $ministryLink = $ministry->org_name . ', ' . $ministry->org_city;
                $ministry->org_st_prov_reg  ? ($ministryLink .= ', ' . $ministry->org_st_prov_reg) : NULL;
                $ministry->org_country == 'United States' ? NULL : 
                    ($ministryLink .= ', ' . $ministry->org_country);
            }
            if($profile->home_church && 
                $church = $this->findActiveProfile($profile->home_church)) {
                $churchLink = $church->org_name . ', ' . $church->org_city;
                $church->org_st_prov_reg ? ($churchLink .= ', ' . $church->org_st_prov_reg) : NULL;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            $schoolsAttended = $profile->school;                                                    // relational db call
            if ($profile->social_id) {
                $social = $profile->social;
            }
            if ($profile->flwship_id) {                                                             // Retrieve fellowship
                $fellowship = $this->findFellowship($profile->flwship_id);                              
                $flwshipLink = $this->findActiveProfile($fellowship->profile_id);                   // Only link to active profiles
            }

            if ($profile->ind_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($ministry && $ministry->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                $loc = explode(',', $ministry->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewStaff', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'fellowship' => $fellowship,
                'flwshipLink' => $flwshipLink,
                'ministry' => $ministry,
                'ministryLink' => $ministryLink,
                'church' => $church,
                'churchLink' => $churchLink,
                'schoolsAttended' => $schoolsAttended,
                'formList' => ProfileFormController::$formList,
                'typeMask' => $typeMask,
                'activate' => $activate]);
        } else {
            $this->redirect(['view-profile', 'id' => $id]);
        }
    }

    /**
     * Custom sort function for usort of school levels
     * @return array
     */
    private function level_sort($a,$b) {
       return $a['id']>$b['id'];
    }

}                     