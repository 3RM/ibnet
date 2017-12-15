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
    public $layout = 'bg-gray';

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
        if ($profile->edit != Profile::EDIT_YES) {
            $profile->updateAttributes(['edit' => Profile::EDIT_YES]);
        }

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

            $social = $this->getSocial($profile);
            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewFlwshpAss', [
                'profile' => $profile,
                'social' => $social,
                'loc' => $loc,
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

            $social = $this->getSocial($profile);
            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewFlwshpAss', [
                'profile' => $profile,
                'social' => $social,
                'loc' => $loc,
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

            $parentMinistry = $profile->ministryOf;
            $social = $this->getSocial($profile);
            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'parentMinistry' => $parentMinistry,
                'social' => $social,
                'loc' => $loc,
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
        if (!$missionary = $profile->missionary) {
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
            $church = $profile->homeChurch;
            $flwshipArray = $profile->fellowship;
            $mission = $missionary->missionAgcy;
            $missionLink = $this->findActiveProfile($mission->profile_id);
            $otherMinistryArray = Staff::getOtherMinistries($profile->id);
            $schoolsAttended = $profile->school;
            $social = $this->getSocial($profile);
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
                'church' => $church,
                'mission' => $mission,
                'missionLink' => $missionLink,
                'otherMinistryArray' => $otherMinistryArray,
                'schoolsAttended' => $schoolsAttended,
                'flwshipArray' => $flwshipArray,
                'social' => $social,
                'loc' => $loc,
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
            if (!$pastor = Staff::getSrPastor($profile->id)) {
                $pastor = $profile->getformattedNames();
            } else {
                $pastor = $pastor->getformattedNames();
            }
            $ministryArray = $profile->ministry;
            $programArray = $profile->program;
            $flwshipArray = $profile->fellowship;
            $assArray = $profile->association;
            $social = $this->getSocial($profile);
            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewChurch', [
                'profile' => $profile,
                'pastor' => $pastor,
                'ministryArray' => $ministryArray,
                'programArray' => $programArray,
                'flwshipArray' => $flwshipArray,
                'assArray' => $assArray,
                'social' => $social,
                'loc' => $loc,
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
            $church = $profile->homeChurch;
            $parentMinistry = $profile->ministryOf;
            $otherMinistryArray = Staff::getOtherMinistries($profile->id);
            $flwshipArray = $profile->fellowship;
            $schoolsAttended = $profile->school;
            $social = $this->getSocial($profile);
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
                'church' => $church,
                'parentMinistry' => $parentMinistry,
                'otherMinistryArray' => $otherMinistryArray,
                'schoolsAttended' => $schoolsAttended,
                'fellowships' => $fellowships,
                'social' => $social,
                'loc' => $loc,
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

            $parentMinistry = $profile->ministryOf;
            $social = $this->getSocial($profile);
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
                'parentMinistry' => $parentMinistry,
                'social' => $social,
                'loc' => $loc,
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
            $church = $profile->homeChurch;
            $churchPlant = $missionary->churchPlant;
            $mission = $missionary->missionAgcy;
            $missionLink = $this->findActiveProfile($mission->profile_id);
            $otherMinistryArray = Staff::getOtherMinistries($profile->id);
            $schoolsAttended = $profile->school;
            $social = $this->getSocial($profile);
            if ($profile->show_map == Profile::MAP_PRIMARY && !empty($profile->ind_loc)) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                $loc = explode(',', $church->org_loc);
            } elseif ($churchPlant && $churchPlant->org_loc && $profile->show_map == Profile::MAP_CHURCH_PLANT) {
                $loc = explode(',', $churchPlant->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewMissionary', [
                'profile' => $profile,
                'missionary' => $missionary,
                'church' => $church,
                'mission' => $mission,
                'missionLink' => $missionLink,
                'churchPlant' => $churchPlant,
                'otherMinistryArray' => $otherMinistryArray,
                'schoolsAttended' => $schoolsAttended,
                'social' => $social,
                'loc' => $loc,
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

            $parentMinistry = $profile->ministryOf;
            $social = $this->getSocial($profile);
            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'parentMinistry' => $parentMinistry,
                'social' => $social,
                'loc' => $loc,
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
            $church = $profile->homeChurch;
            $flwshipArray = $profile->fellowship;
            $otherMinistryArray = Staff::getOtherMinistries($profile->id);
            $schoolsAttended = $profile->school;
            $social = $this->getSocial($profile);
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
                'church' => $church,
                'otherMinistryArray' => $otherMinistryArray,
                'schoolsAttended' => $schoolsAttended,
                'flwshipArray' => $flwshipArray,
                'social' => $social,
                'loc' => $loc,
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

            $parentMinistry = $profile->ministryOf;
            $social = $this->getSocial($profile);
            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'parentMinistry' => $parentMinistry,
                'social' => $social,
                'loc' => $loc,
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

            $parentMinistry = $profile->ministryOf;
            $schoolLevel = $profile->schoolLevel;                                                   // Create array of previously selected school levels
            usort($schoolLevel, [$this, 'level_sort']);                                             // Sort the multidimensional array
            $accreditations = $profile->accreditation;
            $social = $this->getSocial($profile);
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
                'schoolLevel' => $schoolLevel,
                'parentMinistry' => $parentMinistry,
                'accreditations' => $accreditations,
                'social' => $social,
                'loc' => $loc,
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

            $parentMinistry = $profile->ministryOf;
            $social = $this->getSocial($profile);
            if ($profile->show_map == Profile::MAP_PRIMARY && !empty($profile->org_loc)) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewOrg', [
                'profile' => $profile,
                'parentMinistry' => $parentMinistry,
                'social' => $social,
                'loc' => $loc,
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
            $parentMinistry = $profile->ministryOf;
            $church = $profile->homeChurch;
            $otherMinistryArray = Staff::getOtherMinistries($profile->id);
            $schoolsAttended = $profile->school;
            $social = $this->getSocial($profile);
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
                'church' => $church,
                'parentMinistry' => $parentMinistry,
                'otherMinistryArray' => $otherMinistryArray,
                'schoolsAttended' => $schoolsAttended,
                'social' => $social,
                'loc' => $loc,
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

    /**
     * Returns a social object.
     * @param string $id
     * @return model $social
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function getSocial($profile)
    {
        if ($profile->social_id) {
            if ($social = $profile->social && !(
                empty($social->sermonaudio) &&
                empty($social->facebook) &&
                empty($social->linkedin) &&
                empty($social->twitter) &&
                empty($social->google) &&
                empty($social->rss) &&
                empty($social->youtube) &&
                empty($social->vimeo) &&
                empty($social->pinterest) &&
                empty($social->tumblr) &&
                empty($social->soundcloud) &&
                empty($social->instagram) &&
                empty($social->flickr)
            )) {
                return $social;
            }
        }
        return NULL;
    }

}                     