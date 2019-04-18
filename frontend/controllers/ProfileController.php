<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\User;
use common\models\missionary\Missionary;
use common\models\profile\Association;
use common\models\profile\Fellowship;
use common\models\profile\MissionAgcy;
use common\models\profile\Profile;
use common\models\profile\ProfileBrowse;
use common\models\profile\ProfileGuestBrowse;
use common\models\profile\ProfileSearch;
use common\models\profile\ProfileGuestSearch;
use common\models\profile\Staff;
use common\models\profile\Social;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class ProfileController extends Controller
{

    /**
     * Relates profile types to their respective page view actions
     * The various pastor subtypes use the 'pastor' action, and are handled separately
     */
    public static $profilePageArray = [
            Profile::TYPE_PASTOR        => 'pastor',
            Profile::TYPE_EVANGELIST    => 'evangelist',
            Profile::TYPE_MISSIONARY    => 'missionary', 
            Profile::TYPE_CHAPLAIN      => 'chaplain',
            Profile::TYPE_STAFF         => 'staff', 
            Profile::TYPE_CHURCH        => 'church',  
            Profile::TYPE_MISSION_AGCY  => 'mission-agency',  
            Profile::TYPE_FELLOWSHIP    => 'fellowship',  
            Profile::TYPE_ASSOCIATION   => 'association',  
            Profile::TYPE_CAMP          => 'camp',  
            Profile::TYPE_SCHOOL        => 'school',  
            Profile::TYPE_PRINT         => 'print', 
            Profile::TYPE_MUSIC         => 'music',  
            Profile::TYPE_SPECIAL       => 'special-ministry',
        ];   

    public $layout="bg-gray";  

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => [],
                'rules' => [
                    [
                        'allow' => true,
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
     * Search listings
     * @param string $term The search term
     * @return mixed
     */
    public function actionSearch($term)
    {
        $this->layout="main";
        $searchModel = Yii::$app->user->isGuest ? new ProfileGuestSearch() : new ProfileSearch();

        if ($searchModel->load(Yii::$app->request->Post())) {
            if ($searchModel->term == '') {
                return $this->redirect(['/site/index']);
            }
            $term = $searchModel->term;
            // Redirecting here to retain the search string in the urls and facilitate
            // returning to the same search results when the "Return" link is clicked
            return $this->redirect(['/profile/search', 'term' => $term]);
        } else {                                                                                   
            $searchModel->term = $term;
            $dataProvider = $searchModel->query($term);
            return $this->render('search', [
                'searchModel' => $searchModel, 
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Browse listings.
     * @return mixed
     */
    public function actionBrowse()
    {
        $browseModel = Yii::$app->user->isGuest ? new ProfileGuestBrowse() : new ProfileBrowse();
        $browseModel->scenario = 'browse';
        $session = Yii::$app->session;
        $this->layout = "main";
        
        // Clear spatial search
        if (isset($_POST['clear'])) {
            $spatial = [
                'distance' => NULL,
                'location' => NULL,
                'lat'   => NULL,
                'lng'   => NULL
            ];
            $session->set('spatial', $spatial);
            return $this->redirect(['/facet/facet', 'constraint' => false, 'cat' => false]);
        
        // Process spatial search
        } elseif ($browseModel->load(Yii::$app->request->post()) && $browseModel->validate()) {
            $spatial = [
                'distance' => $browseModel->distance,
                'location' => $browseModel->location,
                'lat'   => NULL,
                'lng'   => NULL
            ];
            $session->set('spatial', $spatial);
            return $this->redirect(['/facet/facet', 'constraint' => false, 'cat' => false]);
        
        } else {

            // Reset all user selections
            if ($session->isActive) {
                $session->destroy();
            }

            $more = [ // 1=hide, 2=show                    
                'type'          => 1,
                'country'       => 1,
                'state'         => 1,
                'city'          => 1,
                'miss_status'   => 1,
                'miss_field'    => 1,
                'miss_agcy'     => 1,
                'level'         => 1,
                'sub_type'      => 1,
                'title'         => 1,
                'program'       => 1,
                'tag'           => 1,
                'bible'         => 1,
                'worship_style' => 1,
                'polity'        => 1,
            ]; 
            $spatial = [
                'distance'  => NULL,
                'location'  => NULL,
                'lat'       => NULL,
                'lng'       => NULL,
            ];
            $constraint = NULL;
            $cat = NULL;  
            $fqs = [];
            $query = $browseModel->query();
            $dataProvider = $browseModel->dataProvider($query);
            $resultSet = $browseModel->resultSet($query);

            $session->open('fqs'); 
            $session->open('more');
            $session->open('spatial');
            $session->open('constraint');
            $session->open('cat');
            $session->set('fqs', $fqs);
            $session->set('more', $more);
            $session->set('spatial', $spatial);
            $session->set('constraint', $constraint);
            $session->set('cat', $cat);

            $center = NULL;
            $markers[] = NULL;
            $browseModel->distance = 60;

            return $this->render('browse', [
                'fqs' => $fqs,
                'browseModel' => $browseModel,
                'more' => $more,
                'resultSet' => $resultSet,
                'dataProvider' => $dataProvider,
                'center' => $center,
                'markers' => $markers,
            ]);
        }
    }

    /**
     * Redirect to the proper profile page, given the profile id, location, name
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @return mixed
     */
    public function actionViewProfile($id, $urlLoc, $urlName)
    {
        if ($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) {
            $profilePage = self::$profilePageArray[$profile->type];
            return $this->redirect([$profilePage, 'id' => $profile->id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        } elseif (Profile::isExpired($id)) {
            $this->redirect(['profile-expired', 'id' => $id]);
        } else {
            throw new NotFoundHttpException;
        }
    }

    /**
     * Profile page has expired within the past year
     * @param int $id Profile id
     * @return mixed
     */
    public function actionProfileExpired($id)
    {
        if ($profile = Profile::findProfile($id)) {
            if ($profile->status === Profile::STATUS_ACTIVE) {
                return $this->redirect(['profile/view-profile', 'id' => $id, 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name]);
            } else {
                return $this->render('profilePages/profileExpired');
            }
        }
        throw new NotFoundHttpException;
    }

    /**
     * Αssociation profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionAssociation($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_ASSOCIATION) {
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $members = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {
                
                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $members Association member churches
                if ($members = $profile->associationMembers) {
                    $uids = $profile->filterUserIdsByProfile($members, $uids);
                }

                // $likeProfiles Like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;

            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileFlwshpAss', [
                'profile' => $profile,
                'loc' =>  $loc,
                'social' => $social,
                'staff' => $staff,
                'members' => $members,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
    
        } else { // If user tries to access wrong profile action, reroute to the correct one
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        } 
    }

    /**
     * Fellowship profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionFellowship($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_FELLOWSHIP) {
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $members = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {

                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $members Fellowship members (indvs and churches)
                if ($members = $profile->fellowshipMembers) {
                    $uids = $profile->filterUserIdsByProfile($members, $uids);
                }

                // $likeProfiles Likes
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;

            } elseif ($p == 'history') {
                $events = $profile->history;
            }


            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileFlwshpAss', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'staff' => $staff,
                'members' => $members,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike, 
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Camp profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionCamp($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_CAMP) {
            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $pastor = NULL;
            $parentMinistryStaff = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {

                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $pastor Pastor if parent ministry is a church
                if ($parentMinistry && ($parentMinistry->type == Profile::TYPE_CHURCH)
                    && ($pastor = $parentMinistry->srPastorChurchConfirmed)) {
                    $pastor = $profile->filterUsersByProfile($pastor, $uids);
                    $uids = $profile->filterUserIdsByProfile($pastor, $uids);
                }

                // $parentMinistryStaff Parent ministry staff
                if ($parentMinistry && ($parentMinistryStaff = $parentMinistry->orgStaffConfirmed)) {
                    $parentMinistryStaff = $profile->filterStaff($parentMinistryStaff, $uids);
                    $uids = $profile->filterUserIds($parentMinistryStaff, $uids, true);
                }

                // $likeProfiles Like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;  

            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'parentMinistry' => $parentMinistry,
                'staff' => $staff,
                'pastor' => $pastor,
                'parentMinistryStaff' => $parentMinistryStaff,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Chaplain profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionChaplain($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException;
        }

        if (($profile->type == Profile::TYPE_CHAPLAIN) && ($missionary = $profile->missionary)) {
            $church = $profile->homeChurch;
            $fellowships = $profile->fellowships;
            $missionAgcy = $missionary->missionAgcy;
            $missionAgcyProfile = $missionAgcy ? $missionAgcy->linkedProfile : NULL;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $pastor = NULL;
            $churchStaff = NULL;
            $otherMinistriesStaff = NULL;
            $churchMembers = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {
                
                $uids = NULL;
                
                // $pastor Home church pastor
                $pastor = $profile->srPastorChurchConfirmed;
                $uids = $pastor ? [$pastor->user_id, $profile->user_id] : [$profile->user_id];

                // $churchStaff Home church staff
                if ($church && $churchStaff = $church->orgStaffConfirmed) {
                    $churchStaff = $profile->filterStaff($churchStaff, $uids);
                    $uids = $profile->filterUserIds($churchStaff, $uids, true);
                }

                // $otherMinistriesStaff Other ministries staff
                if ($otherMinistries) {
                    foreach ($otherMinistries as $om) {
                        if ($omStaff = $om->ministry->orgStaffConfirmed) {
                            foreach ($omStaff as $s) {
                                $s->type = $om->ministry->type;
                                $s->name = $om->ministry->org_name;
                                $s->urlLoc = $om->ministry->url_loc;
                                $s->urlName = $om->ministry->url_name;
                            }
                            $otherMinistriesStaff = $otherMinistriesStaff ? 
                                array_merge($otherMinistriesStaff, $omStaff) : $omStaff;
                        }
                    }
                    $otherMinistriesStaff = $profile->filterStaff($otherMinistriesStaff, $uids);
                    $uids = $profile->filterUserIds($otherMinistriesStaff, $uids, true);
                }
            
                // $churchMembers Home church members
                if ($churchMembers = $profile->fellowChurchMembers) {
                    $churchMembers = $profile->filterUsers($churchMembers, $uids);
                    $uids = $profile->filterUserIds($churchMembers, $uids);
                }

                // $likeProfiles Like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;

            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            if ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && ($profile->show_map == Profile::MAP_CHURCH)) {
                 $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileEvangChaplain', [
                'profile' => $profile,
                'church' => $church,
                'fellowships' => $fellowships,
                'missionAgcy' => $missionAgcy,
                'missionAgcyProfile' => $missionAgcyProfile,
                'otherMinistries' => $otherMinistries,
                'schoolsAttended' => $schoolsAttended,
                'social' => $social,
                'loc' => $loc,
                'pastor' => $pastor,
                'churchStaff' => $churchStaff,
                'otherMinistriesStaff' => $otherMinistriesStaff,
                'churchMembers' => $churchMembers,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Church profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionChurch($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_CHURCH) {
            $pastor = $profile->srPastorChurchConfirmed;
            $fellowships = $profile->fellowships;
            $associations = $profile->associations;
            $ministries = $profile->ministries;
            $programs = $profile->programs;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $sentMissionaries = NULL;
            $churchMembers = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {

                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $sentMissionaries Sent missionaries (including chaplains & evangelists)
                if ($sentMissionaries = $profile->sentMissionaries) {
                    $sentMissionaries = $profile->filterUsersByProfile($sentMissionaries, $uids);
                    $uids = $profile->filterUserIdsByProfile($sentMissionaries, $uids);
                }
                
                // $churchMembers Church member
                if ($churchMembers = $profile->churchMembers) { //Utility::pp($churchMembers);
                    $churchMembers = $profile->filterUsers($churchMembers, $uids);
                    $uids = $profile->filterUserIds($churchMembers, $uids);
                }

                // $likeProfiles Like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }
            
            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileChurch', [
                'profile' => $profile,
                'pastor' => $pastor,
                'associations' => $associations,                                                           
                'fellowships' => $fellowships,
                'social' => $social,
                'loc' => $loc,
                'ministries' => $ministries,
                'programs' => $programs,
                'staff' => $staff,
                'sentMissionaries' => $sentMissionaries,
                'churchMembers' => $churchMembers,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'url_loc' => $url_loc, 'urlName' => $urlName]);
        }
    }

    /**
     * Evangelist profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionEvangelist($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException;
        }

        if ($profile->type == Profile::TYPE_EVANGELIST) {
            $church = $profile->homeChurch;
            $parentMinistry = $profile->parentMinistry;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $fellowships = $profile->fellowships;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $pastor = NULL;
            $churchStaff = NULL;
            $parentMinistryStaff = NULL;
            $otherMinistriesStaff = NULL;
            $churchMembers = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {

                $uids = NULL;
                                
                // $pastor Home church pastor
                $pastor = $profile->srPastorIndConfirmed;
                $uids = $pastor ? [$pastor->user_id, $profile->user_id] : [$profile->user_id];

                // $churchStaff Home church staff
                if ($church && $churchStaff = $church->orgStaffConfirmed) {
                    $churchStaff = $profile->filterStaff($churchStaff, $uids);
                    $uids = $profile->filterUserIds($churchStaff, $uids, true);
                }

                // $parentMinistryStaff Parent ministry staff
                if ($parentMinistry && ($parentMinistryStaff = $parentMinistry->orgStaffConfirmed)) {
                    $parentMinistryStaff = $profile->filterStaff($parentMinistryStaff, $uids);
                    $uids = $profile->filterUserIds($parentMinistryStaff, $uids, true);
                }

                // $otherMinistriesStaff Other ministries staff
                if ($otherMinistries) {
                    foreach ($otherMinistries as $om) {
                        if ($omStaff = $om->ministry->orgStaffConfirmed) {
                            foreach ($omStaff as $s) {
                                $s->type = $om->ministry->type;
                                $s->name = $om->ministry->org_name;
                                $s->urlLoc = $om->ministry->url_loc;
                                $s->urlName = $om->ministry->url_name;
                            }
                            $otherMinistriesStaff = $otherMinistriesStaff ? 
                                array_merge($otherMinistriesStaff, $omStaff) : $omStaff;
                        }
                    }
                    $otherMinistriesStaff = $profile->filterStaff($otherMinistriesStaff, $uids);
                    $uids = $profile->filterUserIds($otherMinistriesStaff, $uids, true);
                }

                // $churchMembers Home church members
                if ($churchMembers = $profile->fellowChurchMembers) {
                    $churchMembers = $profile->filterUsers($churchMembers, $uids);
                    $uids = $profile->filterUserIds($churchMembers, $uids);
                }

                // $likeProfiles like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } elseif ($parentMinistry && $parentMinistry->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                 $loc = explode(',', $parentMinistry->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileEvangChaplain', [
                'profile' => $profile,
                'church' => $church,
                'parentMinistry' => $parentMinistry,
                'otherMinistries' => $otherMinistries,
                'schoolsAttended' => $schoolsAttended,
                'fellowships' => $fellowships,
                'social' => $social,
                'loc' => $loc,
                'pastor' => $pastor,
                'churchStaff' => $churchStaff,
                'parentMinistryStaff' => $parentMinistryStaff,
                'otherMinistriesStaff' => $otherMinistriesStaff,
                'churchMembers' => $churchMembers,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Mission agency profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionMissionAgency($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_MISSION_AGCY) {
            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $pastor = NULL;
            $parentMinistryStaff = NULL;
            $missionaries = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {

                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $pastor Pastor if parent ministry is a churcη
                if ($parentMinistry && ($parentMinistry->type == Profile::TYPE_CHURCH)
                    && ($pastor = $parentMinistry->srPastorChurchConfirmed)) {
                    $pastor = $profile->filterUsersByProfile($pastor, $uids);
                    $uids = $profile->filterUserIdsByProfile($pastor, $uids);
                }

                // $parentMinistryStaff Parent ministry staff
                if ($parentMinistry && ($parentMinistryStaff = $parentMinistry->orgStaffConfirmed)) {
                    $parentMinistryStaff = $profile->filterStaff($parentMinistryStaff, $uids);
                    $uids = $profile->filterUserIds($parentMinistryStaff, $uids, true);
                }

                // $missionaries Missionaries
                if ($missionaries = $profile->linkedMissionAgcy->missionaries) {
                    $uids = $profile->filterUserIdsByProfile($missionaries, $uids, true);
                }

                // $likeProfiles Likes profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'social' => $social,
                'parentMinistry' => $parentMinistry,
                'loc' => $loc,
                'staff' => $staff,
                'pastor' => $pastor,
                'parentMinistryStaff' => $parentMinistryStaff,
                'missionaries' => $missionaries,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Missionary profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionMissionary($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException;
        }

        if (($profile->type == Profile::TYPE_MISSIONARY) && ($missionary = $profile->missionary)) {
            $church = $profile->homeChurch;
            $churchPlant = $missionary->churchPlant;
            $missionAgcy = $missionary->missionAgcy;
            $missionAgcyProfile = $missionAgcy ? $missionAgcy->linkedProfile : NULL;
            $updates = $missionary->publicUpdates;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $pastor = NULL;
            $churchStaff = NULL;
            $missionAgcyStaff = NULL;
            $churchPlantStaff = NULL;
            $otherMinistriesStaff = NULL;
            $churchMembers = NULL;
            $churchPlantMembers = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {
                
                $uids = NULL;
                
                // $pastor Home church pastor
                $pastor = $profile->srPastorIndConfirmed;
                $uids = $pastor ? [$pastor->user_id, $profile->user_id] : [$profile->user_id];

                // $churchStaff Home church staff
                if ($church && $churchStaff = $church->orgStaffConfirmed) {
                    $churchStaff = $profile->filterStaff($churchStaff, $uids);
                    $uids = $profile->filterUserIds($churchStaff, $uids, true);
                }

                // $missionAgcyStaff Mission agency staff
                if ($missionAgcyProfile && $missionAgcyStaff = $missionAgcyProfile->orgStaffConfirmed) {
                    $missionAgcyStaff = $profile->filterUsers($missionAgcyStaff, $uids);
                    $uids = $profile->filterUserIds($missionAgcyStaff, $uids, true);
                }

                // $churchPlantStaff Church plant staff
                if ($churchPlant && $churchPlantStaff = $churchPlant->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($churchPlantStaff, $uids, true);
                }

                // $otherMinistriesStaff Other ministries staff
                if ($otherMinistries) {
                    foreach ($otherMinistries as $om) {
                        if ($omStaff = $om->ministry->orgStaffConfirmed) {
                            foreach ($omStaff as $s) {
                                $s->type = $om->ministry->type;
                                $s->name = $om->ministry->org_name;
                                $s->urlLoc = $om->ministry->url_loc;
                                $s->urlName = $om->ministry->url_name;
                            }
                            $otherMinistriesStaff = $otherMinistriesStaff ? 
                                array_merge($otherMinistriesStaff, $omStaff) : $omStaff;
                        }
                    }
                    $otherMinistriesStaff = $profile->filterStaff($otherMinistriesStaff, $uids);
                    $uids = $profile->filterUserIds($otherMinistriesStaff, $uids, true);
                }

                // $churchMembers Home church members
                if ($churchMembers = $profile->fellowChurchMembers) {
                    $churchMembers = $profile->filterUsers($churchMembers, $uids);
                    $uids = $profile->filterUserIds($churchMembers, $uids);
                }

                // $churchPlantMembers Church plant members
                if ($churchPlant && $churchPlantMembers = $churchPlant->churchMembers) {
                    $churchPlantMembers = $profile->filterUsers($churchPlantMembers, $uids);
                    $uids = $profile->filterUserIds($churchPlantMembers, $uids);
                }

                // $likeProfiles Like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            if ($profile->ind_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->ind_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } elseif ($churchPlant && $churchPlant->org_loc && $profile->show_map == Profile::MAP_CHURCH_PLANT) {
                 $loc = explode(',', $churchPlant->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileMissionary', [
                'profile' => $profile,
                'missionary' => $missionary,
                'church' => $church,
                'missionAgcy' => $missionAgcy,
                'missionAgcyProfile' => $missionAgcyProfile,
                'churchPlant' => $churchPlant,
                'updates' => $updates,
                'schoolsAttended' => $schoolsAttended,
                'social' => $social,
                'loc' => $loc,
                'otherMinistries' => $otherMinistries,
                'pastor' => $pastor,
                'churchStaff' => $churchStaff,
                'missionAgcyStaff' => $missionAgcyStaff,
                'churchPlantStaff' => $churchPlantStaff,
                'otherMinistriesStaff' => $otherMinistriesStaff,
                'churchMembers' => $churchMembers,
                'churchPlantMembers' => $churchPlantMembers,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Music ministry profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionMusic($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_MUSIC) {
            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $pastor = NULL;
            $parentMinistryStaff = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {
                
                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                   $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $pastor Pastor if parent ministry is a church
                if ($parentMinistry && ($parentMinistry->type == Profile::TYPE_CHURCH)
                    && ($pastor = $parentMinistry->srPastorChurchConfirmed)) {
                    $pastor = $profile->filterUsersByProfile($pastor, $uids);
                    $uids = $profile->filterUserIdsByProfile($pastor, $uids);
                }

                // $parentMinistryStaff Parent ministry staff
                if ($parentMinistry && ($parentMinistryStaff = $parentMinistry->orgStaffConfirmed)) {
                    $parentMinistryStaff = $profile->filterStaff($parentMinistryStaff, $uids);
                    $uids = $profile->filterUserIds($parentMinistryStaff, $uids, true);
                }

                // $likeProfiles Likes
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'social' => $social,
                'parentMinistry' => $parentMinistry,
                'loc' => $loc,
                'staff' => $staff,
                'pastor' => $pastor,
                'parentMinistryStaff' => $parentMinistryStaff,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Render pastor profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionPastor($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException;
        }

        if ($profile->type == Profile::TYPE_PASTOR) {
            $church = $profile->homeChurch;
            $fellowships = $profile->fellowships;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $churchStaff = NULL;
            $otherMinistriesStaff = NULL;
            $churchMembers = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {

                $uids = [$profile->user_id];

                // $churchStaff Home church staff
                if ($church && $churchStaff = $church->orgStaffConfirmed) {
                    $churchStaff = $profile->filterStaff($churchStaff, $uids);
                    $uids = $profile->filterUserIds($churchStaff, $uids, true);
                }

                // $otherMinistriesStaff Other ministries staff
                if ($otherMinistries) {
                    foreach ($otherMinistries as $om) {
                        if ($omStaff = $om->ministry->orgStaffConfirmed) {
                            foreach ($omStaff as $s) {
                                $s->type = $om->ministry->type;
                                $s->name = $om->ministry->org_name;
                                $s->urlLoc = $om->ministry->url_loc;
                                $s->urlName = $om->ministry->url_name;
                            }
                            $otherMinistriesStaff = $otherMinistriesStaff ? 
                                array_merge($otherMinistriesStaff, $omStaff) : $omStaff;
                        }
                    }
                    $otherMinistriesStaff = $profile->filterStaff($otherMinistriesStaff, $uids);
                    $uids = $profile->filterUserIds($otherMinistriesStaff, $uids, true);
                }

                // $churchMembers Home church members
                if ($church && $churchMembers = $profile->fellowChurchMembers) {
                    $churchMembers = $profile->filterUsers($churchMembers, $uids);
                    $uids = $profile->filterUserIds($churchMembers, $uids);
                }

                // $likeProfiles likes
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profilePastor', [
                'profile' => $profile,
                'church' => $church,
                'fellowships' => $fellowships,
                'otherMinistries' => $otherMinistries,
                'schoolsAttended' => $schoolsAttended,
                'social' => $social,
                'loc' => $loc,
                'churchStaff' => $churchStaff,
                'otherMinistriesStaff' => $otherMinistriesStaff,
                'churchMembers' => $churchMembers,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Print ministry profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionPrint($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_PRINT) {
            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $pastor = NULL;
            $parentMinistryStaff = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {
                
                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $pastor Pastor if parent ministry is a church
                if ($parentMinistry && ($parentMinistry->type == Profile::TYPE_CHURCH)
                    && ($pastor = $parentMinistry->srPastorChurchConfirmed)) {
                    $pastor = $profile->filterUsersByProfile($pastor, $uids);
                    $uids = $profile->filterUserIdsByProfile($pastor, $uids);
                }

                // $parentMinistryStaff Parent ministry staff
                if ($parentMinistry && ($parentMinistryStaff = $parentMinistry->orgStaffConfirmed)) {
                    $parentMinistryStaff = $profile->filterStaff($parentMinistryStaff, $uids);
                    $uids = $profile->filterUserIds($parentMinistryStaff, $uids, true);
                }

                // $likeProfiles Like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'parentMinistry' => $parentMinistry,
                'social' => $social,
                'loc' => $loc,
                'staff' => $staff,
                'pastor' => $pastor,
                'parentMinistryStaff' => $parentMinistryStaff,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * School profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionSchool($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_SCHOOL) {
            $parentMinistry = $profile->parentMinistry;
            $schoolLevels = $profile->schoolLevels ? $profile->schoolLevels : NULL;
            // Sort the multidimensional array
            usort($schoolLevels, [$this, 'level_sort']);
            $accreditations = $profile->accreditations;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $pastor = NULL;
            $parentMinistryStaff = NULL;
            $alumni = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {
                
                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $pastor Pastor if parent ministry is a church
                if ($parentMinistry && ($parentMinistry->type == Profile::TYPE_CHURCH)
                    && ($pastor = $parentMinistry->srPastorChurchConfirmed)) {
                    $pastor = $profile->filterUsersByProfile($pastor, $uids);
                    $uids = $profile->filterUserIdsByProfile($pastor, $uids);
                }

                // $parentMinistryStaff Parent ministry staff
                if ($parentMinistry && ($parentMinistryStaff = $parentMinistry->orgStaffConfirmed)) {
                    $parentMinistryStaff = $profile->filterStaff($parentMinistryStaff, $uids);
                    $uids = $profile->filterUserIds($parentMinistryStaff, $uids, true);
                }

                // Alumni
                if ($alumni = $profile->alumni) {
                    $uids = $profile->filterUserIdsByProfile($alumni->profiles, $uids);
                }

                // $likeProfiles Like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileSchool', [
                'profile' => $profile, 
                'parentMinistry' => $parentMinistry,
                'schoolLevels' => $schoolLevels,
                'accreditations' => $accreditations,
                'social' => $social,
                'loc' => $loc,
                'staff' => $staff,
                'pastor' => $pastor,
                'parentMinistryStaff' => $parentMinistryStaff,
                'alumni' => $alumni,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Special ministry profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionSpecialMinistry($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if ($profile->type == Profile::TYPE_SPECIAL) {
            $parentMinistry = $profile->parentMinistry;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $staff = NULL;
            $pastor = NULL;
            $parentMinistryStaff = NULL;
            $programChurches = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {
             
                $uids = NULL;
                
                // $staff Staff
                if ($staff = $profile->orgStaffConfirmed) {
                    $uids = $profile->filterUserIds($staff, $uids, true);
                }

                // $pastor Pastor if parent ministry is a church
                if ($parentMinistry && ($parentMinistry->type == Profile::TYPE_CHURCH)
                    && ($pastor = $parentMinistry->srPastorChurchConfirmed)) {
                    $pastor = $profile->filterUsersByProfile($pastor, $uids);
                    $uids = $profile->filterUserIdsByProfile($pastor, $uids);
                }

                // $parentMinistryStaff Parent ministry staff
                if ($parentMinistry && ($parentMinistryStaff = $parentMinistry->orgStaffConfirmed)) {
                    $parentMinistryStaff = $profile->filterStaff($parentMinistryStaff, $uids);
                    $uids = $profile->filterUserIds($parentMinistryStaff, $uids, true);
                }

                // $programChurches Churches of this program
                $programChurches = $profile->programChurches;

                // $likeProfiles Likes
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            $loc = ($profile->org_loc && ($profile->show_map == Profile::MAP_PRIMARY)) ? 
                explode(',', $profile->org_loc) : NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'parentMinistry' => $parentMinistry,
                'social' => $social,
                'loc' => $loc,
                'staff' => $staff,
                'pastor' => $pastor,
                'parentMinistryStaff' => $parentMinistryStaff,
                'programChurches' => $programChurches,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Staff profile
     * @param int $id Profile id
     * @param string $urlLoc location slug
     * @param string $urlName org/indvidual name slug
     * @param bool $p whether user has chosed to view connections or history
     * @return mixed
     */
    public function actionStaff($id, $urlLoc, $urlName, $p=NULL)
    {
        if (!($profile = Profile::findViewProfile($id, $urlLoc, $urlName)) && Profile::isExpired($id)) {
            return $this->redirect(['profile/profile-expired', 'id' => $id]);
        }

        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException;
        }

        if ($profile->type == Profile::TYPE_STAFF) {
            $parentMinistry = $profile->parentMinistry;
            $church = $profile->homeChurch;
            $otherMinistries = $profile->otherMinistriesConfirmed;
            $schoolsAttended = $profile->schoolsAttended;
            $social = $profile->hasSocial;
            $likeCount = ($likes = $profile->likes) ? count($likes) : 0;
            $iLike = (!Yii::$app->user->isGuest && $profile->iLike) ? true : false;

            $pastor = NULL;
            $churchStaff = NULL;
            $parentMinistryStaff = NULL;
            $otherMinistriesStaff = NULL;
            $churchMembers = NULL;
            $likeProfiles = NULL;
            $events = NULL;
            if ($p == 'connections') {

                $uids = NULL;
                
                // $pastor Home church pastor
                $pastor = $profile->srPastorIndConfirmed;
                $uids = $pastor ? [$pastor->user_id, $profile->user_id] : [$profile->user_id];

                // $churchStaff Home church staff
                if ($church && $churchStaff = $church->orgStaffConfirmed) {
                    $churchStaff = $profile->filterStaff($churchStaff, $uids);
                    $uids = $profile->filterUserIds($churchStaff, $uids, true);
                }

                // $parentMinistryStaff Parent ministry staff
                if ($parentMinistry && ($parentMinistryStaff = $parentMinistry->orgStaffConfirmed)) {
                    $parentMinistryStaff = $profile->filterStaff($parentMinistryStaff, $uids);
                    $uids = $profile->filterUserIds($parentMinistryStaff, $uids, true);
                }
                
                //  $otherMinistriesStaff Other ministries staff
                if ($otherMinistries) {
                    foreach ($otherMinistries as $om) {
                        if ($omStaff = $om->ministry->orgStaffConfirmed) {
                            foreach ($omStaff as $s) {
                                $s->type = $om->ministry->type;
                                $s->name = $om->ministry->org_name;
                                $s->urlLoc = $om->ministry->url_loc;
                                $s->urlName = $om->ministry->url_name;
                            }
                            $otherMinistriesStaff = $otherMinistriesStaff ? 
                                array_merge($otherMinistriesStaff, $omStaff) : $omStaff;
                        }
                    }
                    $otherMinistriesStaff = $profile->filterStaff($otherMinistriesStaff, $uids);
                    $uids = $profile->filterUserIds($otherMinistriesStaff, $uids, true);
                }

                // $churchMembers Home church members
                if ($church && $churchMembers = $church->churchMembers) {
                    $churchMembers = $profile->filterUsers($churchMembers, $uids);
                    $uids = $profile->filterUserIds($churchMembers, $uids);
                }

                //  $likeProfiles Like profiles
                $likeProfiles = $likes ? $profile->getLikeProfiles($likes, $uids) : NULL;
            
            } elseif ($p == 'history') {
                $events = $profile->history;
            }

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } elseif ($parentMinistry && $parentMinistry->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                 $loc = explode(',', $parentMinistry->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileStaff', [
                'profile' => $profile,
                'parentMinistry' => $parentMinistry,
                'church' => $church,
                'otherMinistries' => $otherMinistries,
                'schoolsAttended' => $schoolsAttended,
                'social' => $social,
                'loc' => $loc,
                'pastor' => $pastor,
                'churchStaff' => $churchStaff,
                'parentMinistryStaff' => $parentMinistryStaff,
                'otherMinistriesStaff' => $otherMinistriesStaff,
                'churchMembers' => $churchMembers,
                'likeProfiles' => $likeProfiles,
                'likeCount' => $likeCount,
                'iLike' => $iLike,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $urlLoc, 'urlName' => $urlName]);
        }
    }

    /**
     * Custom sort function for usort of school levels
     * @param array $a
     * @param array $b
     * @return array
     */
    private function level_sort($a,$b) {
       return $a['id']>$b['id'];
    }

    /**
     * Process post request from "Flag as Inappropriate" modal
     * Sends an email to admin
     * @return mixed
     */
    public function actionFlagProfile()
    {
        if (Yii::$app->request->Post()) {
            $id = $_POST['flag'];
            $profile = Profile::findProfile($id);
            if ($profile->inappropriate < 1) {
                $profile->updateAttributes(['inappropriate' => 1]);                
                $user = Yii::$app->user->isGuest ? NULL : Yii::$app->user->identity->id;                
                $url = Url::base('http') . Url::toRoute(['/profile/view-profile', 'id' => $id, 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name]);
                
                Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'profile/flag-profile-html', 'text' => 'profile/flag-profile-text'], 
                        ['url' => $url, 'user' => $user]
                    )
                    ->setFrom([\yii::$app->params['email.no-reply']])
                    ->setTo([\yii::$app->params['email.admin']])
                    ->setSubject('Inappropriate Profile Flag')
                    ->send();
            }
            Yii::$app->session->setFlash('success', 
                'Notification of inappropriate content received. This profile is now under review. 
                Thank you for bringing this to our attention.');
        }
        
        return $this->redirect(['view-profile', 'id' => $id, 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name]);
    }
}
