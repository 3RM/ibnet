<?php

namespace frontend\controllers;

use common\models\profile\Association;
use common\models\profile\Fellowship;
use common\models\profile\Profile;
use common\models\profile\ProfileBrowse;
use common\models\profile\ProfileSearch;
use common\models\profile\Staff;
use common\models\profile\Social;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class ProfileController extends Controller
{

    public static $profilePageArray = [                     // Relates profile types to their respective page view actions
            'Pastor' =>           'pastor',
            'Evangelist' =>       'evangelist',             // The various pastor profiles use the 'pastor' action, and are handled separately
            'Missionary' =>       'missionary', 
            'Chaplain' =>         'chaplain',
            'Staff' =>            'staff', 
            'Church' =>           'church',  
            'Mission Agency' =>   'mission-agency',  
            'Fellowship' =>       'fellowship',  
            'Association' =>      'association',  
            'Camp' =>             'camp',  
            'School' =>           'school',  
            'Print Ministry' =>   'print', 
            'Music Ministry' =>   'music',  
            'Special Ministry' =>  'special-ministry',
        ];     

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
     * Search Listings.
     * @return mixed
     */
    public function actionSearch($term)
    {
        $searchModel = new ProfileSearch();

        if ($searchModel->load(Yii::$app->request->Post())) {
            if ($searchModel->term == '') {
                return $this->redirect(['/site/index']);                                            // Redirect to index if user enters a blank search string
            }
            $term = $searchModel->term;                                                             // Process search string in $_POST coming from search page
            return $this->redirect(['/profile/search', 'term' => $term]);                           // Redirecting here instead of simply rendering the view allows the search string to be retained in the url and facilitates returning to the same search results when the "Return" link is clicked.
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
     *
     * @return mixed
     */
    public function actionBrowse()
    {
    
        $browseModel = new ProfileBrowse();
        $browseModel->scenario = 'browse';
        $session = Yii::$app->session;

        if (isset($_POST['clear'])) {
            $spatial = [
                'distance' => NULL,
                'location' => NULL,
                'lat'   => NULL,
                'lng'   => NULL
            ];
            $session->set('spatial', $spatial);
            return $this->redirect(['/facet/facet', 'constraint' => false, 'cat' => false]);
        
        } elseif ($browseModel->load(Yii::$app->request->post())&& $browseModel->validate()) {      // Process spatial browse
            $spatial = [
                'distance' => $browseModel->distance,
                'location' => $browseModel->location,
                'lat'   => NULL,
                'lng'   => NULL
            ];
            $session->set('spatial', $spatial);
            return $this->redirect(['/facet/facet', 'constraint' => false, 'cat' => false]);
        
        } else {

            if ($session->isActive) {                                                               // Reset all user selections
                $session->destroy();
            }

            $more = [                                                                               // 1=hide, 2=show                    
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
     * Redirect to the proper profile page, given the profile id, city, name
     * @return mixed
     */
    public function actionViewProfile($city=NULL, $name=NULL, $id)
    {
        if (!$profile = $this->findViewProfile($id)) {
            $this->checkExpired($id);  
        }
        $profilePage = self::$profilePageArray[$profile->type];
        
        return $this->redirect([$profilePage, 'id' => $profile->id, 'city' => $city, 'name' => $name]);
    }

    /**
     * Redirect to the proper profile page, given the profile id
     * @return mixed
     */
    public function actionViewProfileById($id)
    {
        if (!$profile = $this->findActiveProfile($id)) {
            $this->checkExpired($id);  
        }
        $profilePage = self::$profilePageArray[$profile->type];
        $city = $profile->url_city;
        $name = $profile->url_name;
        
        return $this->redirect([$profilePage, 'id' => $profile->id, 'city' => $city, 'name' => $name]);
    }

    /**
     * Render association profile
     * @return mixed
     */
    public function actionAssociation($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Association') {
            $social = NULL;
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->show_map == Profile::MAP_PRIMARY && !empty($profile->org_loc)) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileFlwshpAss', [
                'profile' => $profile,
                'loc' =>  $loc,
                'social' => $social]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);       // If user tries to access wrong profile action, reroute to the correct one
        } 
    }

    /**
     * Render fellowship profile
     * @return mixed
     */
    public function actionFellowship($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Fellowship') {
            $social = NULL;
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($profile->show_map == Profile::MAP_PRIMARY && !empty($profile->org_loc)) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileFlwshpAss', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

        /**
     * Render camp profile
     * @return mixed
     */
    public function actionCamp($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Camp') {
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

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render chaplain profile
     * @return mixed
     */
    public function actionChaplain($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && ($profile->type == 'Chaplain')) {
            $profile->getformattedNames();
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;
            if ($profile->ministry_of && 
                $church = $this->findActiveProfile($profile->ministry_of)) {
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

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileEvangelist', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'fellowship' => $fellowship,
                'flwshipLink' => $flwshipLink,
                'church' => $church,
                'churchLink' => $churchLink,
                'ministry'  => NULL,
                'ministryLink' => NULL,
                'schoolsAttended' => $schoolsAttended]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render church profile
     * @return mixed
     */
    public function actionChurch($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Church') {
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
            $fellowships = $profile->fellowship;
            $associations = $profile->association;
            
            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileChurch', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'pastorLink' => $pastorLink,
                'ministries' => $ministries,
                'programs' => $programs,
                'associations' => $associations,
                'fellowships' => $fellowships]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render evangelist profile
     * @return mixed
     */
    public function actionEvangelist($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && ($profile->type == 'Evangelist')) {
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
                $church->org_st_prov_reg ? $churchLink.= ', ' . $church->org_st_prov_reg : NULL;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            if ($profile->ministry_of && 
                $ministry = $this->findActiveProfile($profile->ministry_of)) {
                $ministryLink = $ministry->org_name . ', ' . $ministry->org_city;
                $ministry->org_st_prov_reg ? $ministryLink.= ', ' . $ministry->org_st_prov_reg : NULL;
                $ministry->org_country == 'United States' ? NULL : 
                    ($ministryLink .= ', ' . $ministry->org_country);
            }
            $schoolsAttended = $profile->school;                                                    // relational db call
            if ($profile->social_id) {
                $social = $profile->social;
            }
            $fellowships = $profile->fellowship;

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } elseif ($ministry && $ministry->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                 $loc = explode(',', $ministry->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileEvangelist', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'fellowships' => $fellowships,
                'church' => $church,
                'churchLink' => $churchLink,
                'ministry' => $ministry,
                'ministryLink' => $ministryLink,
                'schoolsAttended' => $schoolsAttended]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render Mission Agency profile
     * @return mixed
     */
    public function actionMissionAgency($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile->type == 'Mission Agency') {
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
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
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render missionary profile
     * @return mixed
     */
    public function actionMissionary($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        $missionary = $profile->missionary;
        if ($profile && $missionary && $profile->type == 'Missionary') {
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
                $churchLink = $church->org_name . ', ' . $church->org_city;
                $church->org_st_prov_reg ? $churchLink .= ', ' . $church->org_st_prov_reg : NULL;
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

            if ($profile->ind_loc && $profile->show_map == Profile::MAP_PRIMARY) {
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

            return $this->render('profilePages/profileMissionary', [
                'profile' => $profile,
                'loc' => $loc,
                'missionary' => $missionary,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'churchPlant' => $churchPlant,
                'churchPlantLink' => $churchPlantLink,
                'schoolsAttended' => $schoolsAttended,
                'mission' => $mission,
                'missionLink' => $missionLink]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render Music Ministry profile
     * @return mixed
     */
    public function actionMusic($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Music Ministry') {
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

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render special ministry profile
     * @return mixed
     */
    public function actionSpecialMinistry($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Special Ministry') {
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

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render pastor profile
     * @return mixed
     */
    public function actionPastor($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Pastor') {
            $profile->getformattedNames();
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;
            if ($profile->home_church && 
                $church = $this->findActiveProfile($profile->home_church)) {
                $churchLink = $church->org_name . ', ' . $church->org_city;
                $church->org_st_prov_reg ? ($churchLink .= ', ' . $church->org_st_prov_reg) : NULL;
                $church->org_country == 'United States' ? NULL : 
                    ($churchLink .= ', ' . $church->org_country);
            }
            $schoolsAttended = $profile->school;
            if ($profile->social_id) {
                $social = $profile->social;
            }
            $fellowships = $profile->fellowship;

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profilePastor', [
                'profile' => $profile,
                'loc' => $loc,
                'churchLink' => $churchLink,
                'church' => $church,
                'social' => $social,
                'fellowships' => $fellowships,
                'schoolsAttended' => $schoolsAttended]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render print ministry profile
     * @return mixed
     */
    public function actionPrint($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Print Ministry') {
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

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render school profile
     * @return mixed
     */
    public function actionSchool($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'School') {
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
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileSchool', [
                'profile' => $profile, 
                'loc' => $loc,
                'social' => $social,
                'schoolLevel' => $schoolLevel,
                'church' => $church,
                'churchLink' => $churchLink,
                'accreditations' => $accreditations
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render evangelist profile
     * @return mixed
     */
    public function actionStaff($id, $city, $name)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Staff') {
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

            if ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) {
                $loc = explode(',', $profile->org_loc);
            } elseif ($church && $church->org_loc && $profile->show_map == Profile::MAP_CHURCH) {
                 $loc = explode(',', $church->org_loc);
            } elseif ($ministry && $ministry->org_loc && $profile->show_map == Profile::MAP_MINISTRY) {
                 $loc = explode(',', $ministry->org_loc);
            } else {
                $loc = NULL;
            }

            return $this->render('profilePages/profileStaff', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'fellowship' => $fellowship,
                'flwshipLink' => $flwshipLink,
                'ministry' => $ministry,
                'ministryLink' => $ministryLink,
                'church' => $church,
                'churchLink' => $churchLink,
                'schoolsAttended' => $schoolsAttended]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
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
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function findProfile($id)
    {
        if ($profile = Profile::find()
            ->select('*')
            ->where(['id' => $id])
            ->andWhere('status <> ' . Profile::STATUS_TRASH)
            ->one()) {
            return $profile;
        }
        throw new NotFoundHttpException;
    }

    /**
     * Finds an active Profile model based on its primary key value.
     * @param string $id
     * @return Profile the loaded model
     */
    public static function findActiveProfile($id)
    {
        if ($profile = Profile::find()
            ->select('*')
            ->where(['id' => $id])
            ->andwhere(['status' => Profile::STATUS_ACTIVE])
            ->one()) {
            return $profile;
        }     
        return NULL;
    }

    /**
     * Finds an active Profile model based on id, city, and name.
     * @param string $id
     * @param string $city
     * @param string $name
     * @return Profile the loaded model
     */
    public function findViewProfile($id, $city=NULL, $name=NULL)
    {
        if ($profile = Profile::find()
            ->select('*')
            ->where(['id' => $id])
            ->andwhere(['url_city' => $city])
            ->andwhere(['url_name' => $name])
            ->andwhere(['status' => Profile::STATUS_ACTIVE])
            ->one()) {
            return $profile;
        } else {
            throw new NotFoundHttpException;
        }
    }

    /**
     * Returns a staff object, which contains profile id for each ministry staff member.
     * @param string $id
     * @return array $staff profile ids
     */
    public function findStaff($id)
    {
        if ($staff = Staff::find()
                ->select('staff_id')
                ->where(['ministry_id' => $id])
                ->indexBy('staff_id')
                ->column()) {
            return $staff;
        }
        return NULL;
    }

    /**
     * Returns a social object.
     * @param string $id
     * @return model $social
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findSocial($id)
    {
        if ($social = Social::findOne($id)) {
            return $social;
        }
        return NULL;
    }

    /**
     * Returns an association object.
     * @param string $id
     * @return Staff model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findAssociation($id)
    {
        if ($association = Association::find()
            ->where(['id' =>$id])
            ->andWhere(['status' => Profile::STATUS_ACTIVE])
            ->one()) {
            return $association;
        }
        return NULL;
    }

    /**
     * Returns an fellowship object.
     * @param string $id
     * @return Staff model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findFellowship($id)
    {
        if ($fellowship = Fellowship::find()
            ->where(['id' =>$id])
            ->andWhere(['status' => Profile::STATUS_ACTIVE])
            ->one()) {
            return $fellowship;
        }
        return NULL;
    }

    /**
     * Process post request from "Flag as Inappropriate" modal
     * Sends an email to admin.
     */
    public function actionFlagProfile()
    {
        if (Yii::$app->request->Post()) {
            $id = $_POST['flag'];
            $profile = $this->findProfile($id);
            if ($profile && $profile->inappropriate < 1) {
                $profile->updateAttributes(['inappropriate' => 1]);                                     // Set inappropriate flag
                $user = NULL;
                
                if (!Yii::$app->user->isGuest) {
                    $user = Yii::$app->user->identity->id;
                }
                
                $url = Url::base('http') . Url::toRoute(['/profile/view-profile-by-id', 'id' => $id]);
                
                Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'system/flag-profile-html'], 
                        ['url' => $url, 'user' => $user]
                    )
                    ->setFrom([\yii::$app->params['no-replyEmail']])
                    ->setTo([\yii::$app->params['adminEmail']])
                    ->setSubject('Inappropriate Profile Flag')
                    ->send();
            }
            Yii::$app->session->setFlash('success', 
                'Notification of inappropriate content received. This profile is now under review. 
                Thank you for bringing this to our attention.');
        }
    
        return $this->redirect(['view-profile', 'id' => $id, 'city' => $profile->url_city, 'name' => $profile->url_name]);
    }

    /**
     * Return true if profile is within 1 year of expiriation.  If not, throw 404 exception
     *
     */
    public function checkExpired($id)
    {
        if (($profile = $this->findProfile($id)) && ($profile->status != Profile::STATUS_NEW)) {
            $cutoffDate = strtotime($profile->inactivation_date . '+1 year');
            if (date("m-d-Y", $cutoffDate) > date("m-d-Y")) {
                return true;
            } else {
                throw new NotFoundHttpException; 
            }
        } else {
            throw new NotFoundHttpException; 
        } 
    }
}