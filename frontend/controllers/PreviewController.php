<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\profile\Association;
use common\models\profile\Fellowship;
use common\models\profile\Profile;
use common\models\profile\Staff;
use common\models\profile\Social;
use common\rbac\PermissionProfile;
use frontend\controllers\ProfileFormController;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        // Handle all other profile types
        $previewPage = 'preview-' . ProfileController::$profilePageArray[$profile->type];
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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_ASSOCIATION) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $social = $profile->hasSocial;
            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_FELLOWSHIP) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $social = $profile->hasSocial;
            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_CAMP) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;
            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!$missionary = $profile->missionary) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_CHAPLAIN) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = $profile->homeChurch;
            $missionary = $profile->missionary;
            $fellowships = $profile->fellowships;
            $missionAgcy = $missionary->missionAgcy;
            $missionAgcyProfile = $missionAgcy ? $missionAgcy->linkedProfile : NULL;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;
            if ($profile->ind_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($church &&  $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewEvangChaplain', [
                'profile' => $profile,
                'church' => $church,
                'missionary' => $missionary,
                'missionAgcy' => $missionAgcy,
                'missionAgcyProfile' => $missionAgcyProfile,
                'otherMinistries' => $otherMinistries,
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
     * Render church profile preview
     * @return mixed
     */
    public function actionPreviewChurch($id)
    {
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_CHURCH) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $pastor = $profile->srPastorChurchConfirmed;
            $ministries = $profile->ministries;
            $programs = $profile->programs;
            $fellowships = $profile->fellowships;
            $associations = $profile->associations;
            $social = $profile->hasSocial;

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewChurch', [
                'profile' => $profile,
                'pastor' => $pastor,
                'ministries' => $ministries,
                'programs' => $programs,
                'fellowships' => $fellowships,
                'associations' => $associations,
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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_EVANGELIST) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = $profile->homeChurch;
            $parentMinistry = $profile->parentMinistry;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $fellowships = $profile->fellowships;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;

            if ($profile->ind_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                $loc = explode(',', $church->org_loc);
            } elseif ($parentMinistry && $parentMinistry->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                $loc = explode(',', $parentMinistry->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewEvangChaplain', [
                'profile' => $profile,
                'church' => $church,
                'parentMinistry' => $parentMinistry,
                'otherMinistries' => $otherMinistries,
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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_MISSION_AGCY) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if (!$missionary = $profile->missionary) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_MISSIONARY) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = $profile->homeChurch;
            $churchPlant = $missionary->churchPlant;
            $missionAgcy = $missionary->missionAgcy;
            $missionAgcyProfile = $missionAgcy ? $missionAgcy->linkedProfile : NULL;
            $updates = $missionary->publicUpdates;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;

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
                'missionAgcy' => $missionAgcy,
                'missionAgcyProfile' => $missionAgcyProfile,
                'churchPlant' => $churchPlant,
                'updates' => $updates,
                'otherMinistries' => $otherMinistries,
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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_MUSIC) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_PASTOR) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $church = $profile->homeChurch;
            $fellowships = $profile->fellowships;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;

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
                'otherMinistries' => $otherMinistries,
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
     * Render print ministry profile preview
     * @return mixed
     */
    public function actionPreviewPrint($id)
    {
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_PRINT) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_SCHOOL) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $parentMinistry = $profile->parentMinistry;
            $schoolLevels = $profile->schoolLevels;
            // Sort the multidimensional array
            usort($schoolLevels, [$this, 'level_sort']);
            $accreditations = $profile->accreditations;
            $social = $profile->hasSocial;

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewSchool', [
                'profile' => $profile, 
                'schoolLevels' => $schoolLevels,
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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_SPECIAL) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;

             $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

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
        $profile = Profile::findProfile($id);
        if (!\Yii::$app->user->can(PermissionProfile::UPDATE, ['profile' => $profile]) || !$profile->validType()) {
            throw new NotFoundHttpException;
        }
        if ($profile->type == Profile::TYPE_STAFF) {

            if (isset($_POST['activate'])) {
                return $this->redirect(['/profile-mgmt/activate', 'id' => $id]); 
            } elseif (Yii::$app->request->post()) {
                $profile->setUpdateDate();
                return $this->redirect(['/profile-mgmt/my-profiles']);
            }

            $parentMinistry = $profile->parentMinistry;
            $church = $profile->homeChurch;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } elseif ($parentMinistry && $parentMinistry->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                 $loc = explode(',', $parentMinistry->org_loc);
            } else {
                $loc = NULL;
            }

            $typeMask = ProfileFormController::$formArray[$profile->type];
            $profile->status == Profile::STATUS_ACTIVE ? $activate = NULL : $activate = 1;

            return $this->render('previewStaff', [
                'profile' => $profile,
                'church' => $church,
                'parentMinistry' => $parentMinistry,
                'otherMinistries' => $otherMinistries,
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

}                     