<?php

namespace frontend\controllers;

use common\models\User;
use common\models\Utility;
use common\models\profile\Association;
use common\models\profile\Fellowship;
use common\models\profile\MissionAgcy;
use common\models\profile\Missionary;
use common\models\profile\Profile;
use common\models\profile\ProfileBrowse;
use common\models\profile\ProfileSearch;
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
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
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
    public function actionAssociation($id, $city, $name, $p=NULL)
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

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
            
            // ============================== Staff ============================   
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }

            // =========================== Member Churches =====================
                if ($ass = Association::findOne(['profile_id' => $profile->id])) {
                    $profiles = $ass->profile;
                    $ids = ArrayHelper::getColumn($profiles, 'id');
                    $churchArray = Profile::findAll($ids);
                    $i = 0;
                    foreach ($churchArray as $ch) {
                        if ($ch->type != 'Church') {
                            unset($churchArray[$i]);
                        }
                        $i++;
                    }
                }   
            }
            if ($p == 'history') {
                $events = $profile->history;
            }

            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileFlwshpAss', [
                'profile' => $profile,
                'loc' =>  $loc,
                'social' => $social,
                'staffArray' => $staffArray,
                'churchArray' => $churchArray,
                'events' => $events,
                'p' => $p,
            ]);
    
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);       // If user tries to access wrong profile action, reroute to the correct one
        } 
    }

    /**
     * Render fellowship profile
     * @return mixed
     */
    public function actionFellowship($id, $city, $name, $p=NULL)
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

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Staff ============================
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }

            // ============================== Members ==========================
                if ($flwship = Fellowship::findOne(['profile_id' => $profile->id])) {
                    $profileArray = $flwship->profile;

            // ========================= Individual Members ====================
                    $indvArray = $profileArray;
                    $i = 0;
                    foreach ($indvArray as $ind) {
                        if ($ind->category != Profile::CATEGORY_IND) {
                            unset($indvArray[$i]);
                        }
                        $i++;
                    }

            // ========================= Member Churches =======================
                    $churchArray = $profileArray;
                    $i = 0;
                    foreach ($churchArray as $ch) {
                        if ($ch->type != 'Church') {
                            unset($churchArray[$i]);
                        }
                        $i++;
                    }
                }

            }
            if ($p == 'history') {
                $events = $profile->history;
            }

            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileFlwshpAss', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'staffArray' => $staffArray,
                'indvArray' => $indvArray,
                'churchArray' => $churchArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render camp profile
     * @return mixed
     */
    public function actionCamp($id, $city, $name, $p=NULL)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Camp') {
            $parentMinistry = NULL;
            $parentMinistryLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $parentMinistry = $this->findActiveProfile($profile->ministry_of)) {
                $parentMinistryLink = $parentMinistry->org_name . ', ' . 
                    $parentMinistry->org_city . ', ' . $parentMinistry->org_st_prov_reg;
                $parentMinistry->org_country == 'United States' ? NULL : 
                    ($parentMinistryLink .= ', ' . $parentMinistry->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Staff ============================
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }
            }
            if ($p == 'history') {
                $events = $profile->history;
            }

            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'parentMinistry' => $parentMinistry,
                'parentMinistryLink' => $parentMinistryLink,
                'staffArray' => $staffArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render chaplain profile
     * @return mixed
     */
    public function actionChaplain($id, $city, $name, $p=NULL)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        $missionary = $profile->missionary;
        if ($profile && ($profile->type == 'Chaplain')) {
            $profile->getformattedNames();
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;
            $mission = NULL;
            $missionLink = NULL;
            if ($mission = $missionary->missionAgcy) {
                if ($mission->profile_id) {
                    $missionLink = $this->findActiveProfile($mission->profile_id);
                }
            }
            if ($profile->home_church && 
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

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Pastor ===========================
                if ($church) {
                    if ($staff = Staff::find()
                        ->where(['ministry_id' => $church->id])
                        ->andWhere(['sr_pastor' => 1])
                        ->andWhere(['confirmed' => 1])
                        ->one()) {
                        $pastor = $this->findActiveProfile($staff->staff_id);
                    }
                }

            // ======================= Other Ministries ========================
                if ($ministries = Staff::find()
                    ->where(['staff_id' => $profile->id])
                    ->andWhere(['ministry_other' => 1])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('id Asc')
                    ->all()) {
                    $i = 0;
                    foreach ($ministries as $mstry) {                                               // Combine multiple staff titles for same ministry
                        if ($i > 0 && ($mstry['ministry_id'] == $ministries[$i-1]['ministry_id'])) {
                            $ministries[$i-1]['staff_title'] .= ' &middot ' . $mstry['staff_title'];
                            unset($ministries[$i]);
                            $ministries = array_values($ministries);
                            continue;
                        }
                        $i++;
                    }
                    $ids = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $names = ArrayHelper::getColumn($ministries, 'staff_title');
                    $otherMinistryArray = Profile::findAll($ids);

                    $i = 0;
                    foreach ($otherMinistryArray as $min) {
                        $min->titleM = $names[$i];
                        $i++;
                    }
                }

            // ======================= Ministry Partners =======================
                if ($sChurch = (new \yii\db\Query())
                    ->select('staff_id')
                    ->from('staff')
                    ->where(['ministry_id' => $profile->home_church])
                    ->andWhere(['<>', 'staff_id', $profile->id])
                    ->andWhere(['sr_pastor' => NULL])
                    ->andWhere(['confirmed' => 1])
                    ->groupBy('staff_id')
                    ->all()) {
                    $sChurchIds = ArrayHelper::getColumn($sChurch, 'staff_id');
                    $sChurchArray = Profile::findAll($sChurchIds);
                }
                if ($ministries) {                                                                  // Ministry partners at other ministries
                    $otherArray = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $otherIds = implode (",", $otherArray);
                    if ($staffOther = (new \yii\db\Query())
                        ->select('staff_id, ministry_id')
                        ->from('staff')
                        ->where('ministry_id IN ("' . $otherIds . '")')
                        ->andWhere(['<>', 'staff_id', $profile->id])
                        ->andWhere(['confirmed' => 1])
                        ->groupBy('staff_id')
                        ->orderBy('id')
                        ->all()) {
                        $sOtherIds = ArrayHelper::getColumn($staffOther, 'staff_id');
                        $mOtherIds = ArrayHelper::getcolumn($staffOther, 'ministry_id');
                        $sOtherArray = Profile::findAll($sOtherIds);

                        $i = 0;
                        foreach ($sOtherArray as $sOther) {
                            $sOther->titleM = Profile::findOne($mOtherIds[$i])->org_name;
                            $i++;
                        }
                    }
                }
            
            // ===================== Fellow Church Members =====================
                $memberArray = User::find()
                    ->where(['<>', 'screen_name', NULL])
                    ->andWhere(['ind_act_profiles' => NULL])
                    ->andWhere(['home_church' => $profile->home_church])
                    ->andWhere(['<>', 'id', $profile->user_id])
                    ->andWhere(['role' => 'Church Member'])
                    ->all();

            }
            if ($p == 'history') {
                $events = $profile->history;
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
                'mission' => $mission,
                'missionLink' => $missionLink,
                'parentMinistry'  => NULL,
                'parentMinistryLink' => NULL,
                'schoolsAttended' => $schoolsAttended,
                'pastor' => $pastor,
                'otherMinistryArray' => $otherMinistryArray,
                'memberArray' => $memberArray,
                'sChurchArray' => $sChurchArray,
                'sOtherArray' => $sOtherArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render church profile
     * @return mixed
     */
    public function actionChurch($id, $city, $name, $p=NULL)
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
            $memberArray = NULL;
            $programArray = $profile->program;
            $assArray = NULL;
            $assLink = NULL;
            $flwshipArray = NULL;
            $flwshipLink = NULL;
            if ($staff = Staff::find()
                ->where(['ministry_id' => $profile->id])
                ->andWhere(['sr_pastor' => 1])
                ->andWhere(['confirmed' => 1])
                ->one()) {
                $pastor = $this->findActiveProfile($staff->staff_id);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }
            if (!$otherMinistryArray = Profile::find()
                ->where(['status' => Profile::STATUS_ACTIVE])
                ->andWhere(['ministry_of' => $profile->id])
                ->all()) {
                $otherMinistryArray = NULL;
            }
            $flwshipArray = $profile->fellowship;
            $assArray = $profile->association;

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Staff ============================
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['sr_pastor' => NULL])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();

                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $pos = strpos($staff[$i-1]['staff_title'], $staff[$i]['staff_title']);      // Remove duplicate staff roles for same individual
                        if ($pos === false) {                                                           // staff_title was not found in list of titles
                            $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];           // go ahead and add it to the list
                        } 
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }

            // ========================= Fellowships ===========================
                $flwships = $profile->fellowship;
                $flwshipIds = ArrayHelper::getColumn($flwships, 'profile_id');
                $fArray = Profile::findAll($flwshipIds);

            // ======================== Associations ===========================
                $asss = $profile->association;
                $assIds = ArrayHelper::getColumn($asss, 'profile_id');
                $aArray = Profile::findAll($assIds);

            // ======================= Church Members ==========================
                $memberArray = User::find()
                    ->select('id, screen_name')
                    ->where(['home_church' => $profile->id])
                    ->andWhere(['role' => 'Church Member'])
                    ->all();
                $i = 0;
                foreach($memberArray as $member) {                                                  // Remove members who have an individual profile already
                    if ($profileArray = Profile::find()
                        ->select('type')
                        ->where(['user_id' => $member->id])
                        ->all()) {
                        foreach ($profileArray as $pa) {
                            if ($pa->category == Profile::CATEGORY_IND) {
                                unset($memberArray[$i]);
                                $memberArray = array_values($memberArray);
                                break;
                            }
                        }
                    }
                    $i++;
                }

            }
            if ($p == 'history') {
                $events = $profile->history;
            }
            
            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileChurch', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'pastor' => $pastor,
                'otherMinistryArray' => $otherMinistryArray,
                'programArray' => $programArray,
                'assArray' => $assArray,                                                            // flwshipArray & assArray are arrays from fellowship and association tables (contains names with and without profiles)
                'flwshipArray' => $flwshipArray,
                'aArray' => $aArray,                                                                // fArray & aArray are arrays from profile table (contains only active profiles)
                'fArray' => $fArray,
                'staffArray' => $staffArray,
                'memberArray' => $memberArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render evangelist profile
     * @return mixed
     */
    public function actionEvangelist($id, $city, $name, $p=NULL)
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
            $parentMinistry = NULL;
            $parentMinistryLink = NULL;
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
                $parentMinistry = $this->findActiveProfile($profile->ministry_of)) {
                $parentMinistryLink = $parentMinistry->org_name . ', ' . $parentMinistry->org_city;
                $parentMinistry->org_st_prov_reg ? $parentMinistryLink.= ', ' . $parentMinistry->org_st_prov_reg : NULL;
                $parentMinistry->org_country == 'United States' ? NULL : 
                    ($parentMinistryLink .= ', ' . $parentMinistry->org_country);
            }
            $schoolsAttended = $profile->school;                                                    // relational db call
            if ($profile->social_id) {
                $social = $profile->social;
            }
            $fellowships = $profile->fellowship;

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Pastor ===========================
                if ($church) {
                    if ($staff = Staff::find()
                        ->where(['ministry_id' => $church->id])
                        ->andWhere(['sr_pastor' => 1])
                        ->andWhere(['confirmed' => 1])
                        ->one()) {
                        $pastor = $this->findActiveProfile($staff->staff_id);
                    }
                }

            // ======================== Parent Ministry ========================
                if ($profile->ministry_of) {
                    $parentMinistry = self::findActiveProfile($profile->ministry_of);
                }

            // =========================== Other Ministries ====================
                if ($ministries = Staff::find()
                    ->where(['staff_id' => $profile->id])
                    ->andWhere(['ministry_other' => 1])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('id Asc')
                    ->all()) {
                    $i = 0;
                    foreach ($ministries as $mstry) {                                                          // Combine multiple staff titles for same ministry
                        if ($i > 0 && ($mstry['ministry_id'] == $ministries[$i-1]['ministry_id'])) {
                            $ministries[$i-1]['staff_title'] .= ' &middot ' . $mstry['staff_title'];
                            unset($ministries[$i]);
                            $ministries = array_values($ministries);
                            continue;
                        }
                        $i++;
                    }
                    $ids = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $names = ArrayHelper::getColumn($ministries, 'staff_title');
                    $otherMinistryArray = Profile::findAll($ids);

                    $i = 0;
                    foreach ($otherMinistryArray as $min) {
                        $min->titleM = $names[$i];
                        $i++;
                    }
                }

            // ======================= Ministry Partners =======================
                $q = new Query();
                $q->select('staff_id, ministry_id')
                    ->from('staff')
                    ->where(['ministry_id' => $profile->home_church]);
                if (isset($parentMinistry)) {
                    $q->orWhere(['ministry_id' => $parentMinistry->id]);
                }
                $q->andWhere(['<>', 'staff_id', $profile->id])
                    ->andWhere(['sr_pastor' => NULL])
                    ->orderBy('staff_id');
                $staff = $q->all();
                
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple ministries for same individual
                    if ($min = $this->findActiveProfile($stf['ministry_id'])) {
                        if ($i > 0 && 
                            ($stf['staff_id'] == $staff[$i-1]['staff_id']) &&
                            ($stf['ministry_id'] == $staff[$i-1]['ministry_id'])) {                 // Remove duplicate ministries
                            unset($staff[$i]);
                            $staff = array_values($staff);
                            continue;
                        } elseif ($i > 0 && 
                            ($stf['staff_id'] == $staff[$i-1]['staff_id']) &&
                            ($stf['ministry_id'] != $staff[$i-1]['ministry_id'])) {                 // Combine multiple unique ministries
                            $staff[$i-1]['staff_title'] .= ' & <br>' . $min->org_name;
                            unset($staff[$i]);
                            $staff = array_values($staff);
                            continue;
                        } else {
                            $staff[$i]['staff_title'] = $min->org_name;
                        }
                    }
                    $i++;
                }
                $sChurchIds = ArrayHelper::getColumn($sChurch, 'staff_id');
                $sChurchNames = ArrayHelper::getColumn($sChurch, 'staff_title');
                $sChurchArray = Profile::findAll($sChurchIds);
                $i = 0;
                foreach ($sChurchArray as $stf) {
                    $stf->titleM = $sChurchNames[$i];
                    $i++;
                }

            // ===================== Fellow Church Members =====================
                $memberArray = User::find()
                    ->where(['<>', 'screen_name', NULL])
                    ->andWhere(['ind_act_profiles' => NULL])
                    ->andWhere(['home_church' => $profile->home_church])
                    ->andWhere(['<>', 'id', $profile->user_id])
                    ->andWhere(['role' => 'Church Member'])
                    ->all();

            }
            if ($p == 'history') {
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

            return $this->render('profilePages/profileEvangelist', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'fellowships' => $fellowships,
                'church' => $church,
                'churchLink' => $churchLink,
                'parentMinistry' => $parentMinistry,
                'parentMinistryLink' => $parentMinistryLink,
                'schoolsAttended' => $schoolsAttended,
                'pastor' => $pastor,
                'otherMinistryArray' => $otherMinistryArray,
                'sChurchArray' => $sChurchArray,
                'memberArray' => $memberArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render Mission Agency profile
     * @return mixed
     */
    public function actionMissionAgency($id, $city, $name, $p=NULL)
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

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Staff ============================
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }

            // ======================== Parent Ministry ========================
                if ($profile->ministry_of) {
                    $parentMinistry = self::findActiveProfile($profile->ministry_of);
                }

            // ========================= Missionaries ==========================
                $missAgcyId = MissionAgcy::find()
                    ->select('id')
                    ->where(['profile_id' => $profile->id])
                    ->one();
                $missionaryArray = Missionary::find()
                    ->joinWith('profile')
                    ->where(['missionary.mission_agcy_id' => $missAgcyId])
                    ->andWhere(['profile.status' => Profile::STATUS_ACTIVE])
                    ->all();

            }
            if ($p == 'history') {
                $events = $profile->history;
            }

            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'church' => $church,
                'churchLink' => $churchLink,
                'staffArray' => $staffArray,
                'missionaryArray' => $missionaryArray,
                'parentMinistry' => $parentMinistry,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name, 'parentMinistry' => $parentMinistry]);
        }
    }

    /**
     * Render missionary profile
     * @return mixed
     */
    public function actionMissionary($id, $city, $name, $p=NULL)
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

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // =========================== Pastor ==============================
                if ($church) {
                    if ($staff = Staff::find()
                        ->where(['ministry_id' => $church->id])
                        ->andWhere(['sr_pastor' => 1])
                        ->andWhere(['confirmed' => 1])
                        ->one()) {
                        $pastor = $this->findActiveProfile($staff->staff_id);
                    }
                }

            // ====================== Other Ministries =========================
                if ($ministries = Staff::find()
                    ->where(['staff_id' => $profile->id])
                    ->andWhere(['ministry_other' => 1])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('id Asc')
                    ->all()) {
                    $i = 0;
                    foreach ($ministries as $mstry) {                                               // Combine multiple staff titles for same ministry
                        if ($i > 0 && ($mstry['ministry_id'] == $ministries[$i-1]['ministry_id'])) {
                            $ministries[$i-1]['staff_title'] .= ' &middot ' . $mstry['staff_title'];
                            unset($ministries[$i]);
                            $ministries = array_values($ministries);
                            continue;
                        }
                        $i++;
                    }
                    $ids = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $names = ArrayHelper::getColumn($ministries, 'staff_title');
                    $otherMinistryArray = Profile::findAll($ids);

                    $i = 0;
                    foreach ($otherMinistryArray as $min) {
                        $min->titleM = $names[$i];
                        $i++;
                    }
                }

            // ======================= Ministry Partners =======================
                if ($sMinistry = (new \yii\db\Query())                                              // Ministry partners at primary ministry (if not home church)
                    ->select('staff_id')
                    ->from('staff')
                    ->where(['ministry_id' => $profile->ministry_of])
                    ->andWhere(['<>', 'staff_id', $profile->id])
                    ->andWhere(['<>', 'home_church', 1])
                    ->andWhere(['confirmed' => 1])
                    ->groupBy('staff_id')
                    ->all()) {
                    $sMinistryIds = ArrayHelper::getColumn($sMinistry, 'staff_id');
                    $sMinistryArray = Profile::findAll($sMinistryIds);

                    $i = 0;
                    foreach ($sMinistryArray as $staff) {
                        $staff->titleM = $names[$i];
                        $i++;
                    }
                }
                if ($ministries) {                                                                  // Ministry partners at other ministries
                    $otherArray = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $otherIds = implode (",", $otherArray);
                    if ($staffOther = (new \yii\db\Query())
                        ->select('staff_id, ministry_id')
                        ->from('staff')
                        ->where('ministry_id IN ("' . $otherIds . '")')
                        ->andWhere(['<>', 'staff_id', $profile->id])
                        ->andWhere(['confirmed' => 1])
                        ->groupBy('staff_id')
                        ->orderBy('id')
                        ->all()) {
                        $sOtherIds = ArrayHelper::getColumn($staffOther, 'staff_id');
                        $mOtherIds = ArrayHelper::getcolumn($staffOther, 'ministry_id');
                        $sOtherArray = Profile::findAll($sOtherIds);

                        $i = 0;
                        foreach ($sOtherArray as $sOther) {
                            $sOther->titleM = Profile::findOne($mOtherIds[$i])->org_name;
                            $i++;
                        }
                    }
                }
                if ($churchPlant && 
                    $staffCP = (new \yii\db\Query())
                        ->select('staff_id, ministry_id')
                        ->from('staff')
                        ->where(['ministry_id' => $churchPlant->id])
                        ->andWhere(['<>', 'staff_id', $profile->id])
                        ->andWhere(['confirmed' => 1])
                        ->groupBy('staff_id')
                        ->orderBy('id')
                        ->all()) {
                    $sCPIds = ArrayHelper::getColumn($staffCP, 'staff_id');
                    $mCPIds = ArrayHelper::getcolumn($staffCP, 'ministry_id');
                    $sCPArray = Profile::findAll($sCPIds);
                }

            // ===================== Fellow Church Members =====================
                $miss = $profile->missionary;
                $cpChurch = $miss->churchPlant;
                $memberArray = User::find()
                    ->where('screen_name IS NOT NULL')
                    ->andWhere(['ind_act_profiles' => NULL])
                    ->andWhere('home_church= "' . $profile->home_church . '" OR home_church="' . $cpChurch->id . '"')
                    ->andWhere(['<>', 'id', $profile->user_id])
                    ->andWhere(['role' => 'Church Member'])
                    ->all();
            }
            if ($p == 'history') {
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
                'missionLink' => $missionLink,
                'pastor' => $pastor,
                'otherMinistryArray' => $otherMinistryArray,
                'memberArray' => $memberArray,
                'sCPArray' => $sCPArray,
                'sMinistryArray' => $sMinistryArray,
                'sOtherArray' => $sOtherArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render Music Ministry profile
     * @return mixed
     */
    public function actionMusic($id, $city, $name, $p=NULL)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Music Ministry') {
            $parentMinistry = NULL;
            $parentMinistryLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $parentMinistry = $this->findActiveProfile($profile->ministry_of)) {
                $parentMinistryLink = $parentMinistry->org_name . ', ' . 
                    $parentMinistry->org_city . ', ' . $parentMinistry->org_st_prov_reg;
                $parentMinistry->org_country == 'United States' ? NULL : 
                    ($parentMinistryLink .= ', ' . $parentMinistry->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Staff ============================
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }
            }
            if ($p == 'history') {
                $events = $profile->history;
            }

            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'parentMinistry' => $parentMinistry,
                'parentMinistryLink' => $parentMinistryLink,
                'staffArray' => $staffArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render pastor profile
     * @return mixed
     */
    public function actionPastor($id, $city, $name, $p=NULL)
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
            $flwshipArray = $profile->fellowship;

            if ($p == 'connections') {

            // =========================== Other Ministries ====================
                if ($ministries = Staff::find()
                    ->where(['staff_id' => $profile->id])
                    ->andWhere(['ministry_other' => 1])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('id Asc')
                    ->all()) {
                    $i = 0;
                    foreach ($ministries as $mstry) {                                                          // Combine multiple staff titles for same ministry
                        if ($i > 0 && ($mstry['ministry_id'] == $ministries[$i-1]['ministry_id'])) {
                            $ministries[$i-1]['staff_title'] .= ' &middot ' . $mstry['staff_title'];
                            unset($ministries[$i]);
                            $ministries = array_values($ministries);
                            continue;
                        }
                        $i++;
                    }
                    $ids = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $names = ArrayHelper::getColumn($ministries, 'staff_title');
                    $otherMinistryArray = Profile::findAll($ids);

                    $i = 0;
                    foreach ($otherMinistryArray as $min) {
                        $min->titleM = $names[$i];
                        $i++;
                    }
                }

            // ===================== Fellow Church Members =====================
                $memberArray = User::find()
                    ->where('screen_name IS NOT NULL')
                    ->andWhere(['ind_act_profiles' => NULL])
                    ->andWhere(['home_church' => $profile->home_church])
                    ->andWhere(['<>', 'id', $profile->user_id])
                    ->andWhere(['role' => 'Church Member'])
                    ->all();

           // ======================= Ministry Partners =======================                
                if ($sChurch = (new \yii\db\Query())                                                // Ministry partners at church
                    ->select('staff_id')
                    ->from('staff')
                    ->where(['ministry_id' => $profile->home_church])
                    ->andWhere(['<>', 'staff_id', $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->groupBy('staff_id')
                    ->all()) {
                    $sChurchIds = ArrayHelper::getColumn($sChurch, 'staff_id');
                    $sChurchArray = Profile::findAll($sChurchIds);
                }
                if ($ministries) {                                                                  // Ministry partners at other ministries
                    $otherArray = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $otherIds = implode (",", $otherArray);
                    if ($staffOther = (new \yii\db\Query())
                        ->select('staff_id, ministry_id')
                        ->from('staff')
                        ->where('ministry_id IN ("' . $otherIds . '")')
                        ->andWhere(['<>', 'staff_id', $profile->id])
                        ->andWhere(['confirmed' => 1])
                        ->groupBy('staff_id')
                        ->orderBy('id')
                        ->all()) {
                        $sOtherIds = ArrayHelper::getColumn($staffOther, 'staff_id');
                        $mOtherIds = ArrayHelper::getcolumn($staffOther, 'ministry_id');
                        $sOtherArray = Profile::findAll($sOtherIds);

                        $i = 0;
                        foreach ($sOtherArray as $sOther) {
                            $sOther->titleM = Profile::findOne($mOtherIds[$i])->org_name;
                            $i++;
                        }
                    }
                }
            }
            if ($p == 'history') {
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
                'loc' => $loc,
                'churchLink' => $churchLink,
                'church' => $church,
                'social' => $social,
                'flwshipArray' => $flwshipArray,
                'schoolsAttended' => $schoolsAttended,
                'otherMinistryArray' => $otherMinistryArray,
                'sChurchArray' => $sChurchArray,
                'sOtherArray' => $sOtherArray,
                'memberArray' => $memberArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render print ministry profile
     * @return mixed
     */
    public function actionPrint($id, $city, $name, $p=NULL)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Print Ministry') {
            $parentMinistry = NULL;
            $parentMinistryLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $parentMinistry = $this->findActiveProfile($profile->ministry_of)) {
                $parentMinistryLink = $parentMinistry->org_name . ', ' . 
                    $parentMinistry->org_city . ', ' . $parentMinistry->org_st_prov_reg;
                $parentMinistry->org_country == 'United States' ? NULL : 
                    ($parentMinistryLink .= ', ' . $parentMinistry->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Staff ============================
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }
            }
            if ($p == 'history') {
                $events = $profile->history;
            }

            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'parentMinistry' => $parentMinistry,
                'parentMinistryLink' => $parentMinistryLink,
                'staffArray' => $staffArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render school profile
     * @return mixed
     */
    public function actionSchool($id, $city, $name, $p=NULL)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'School') {
            $parentMinistry = NULL;
            $parentMinistryLink = NULL;
            $social = NULL;
            $accreditation = NULL;
            if ($profile->ministry_of && 
                $parentMinistry = $this->findActiveProfile($profile->ministry_of)) {
                $parentMinistryLink = $parentMinistry->org_name . ', ' . 
                    $parentMinistry->org_city . ', ' . $parentMinistry->org_st_prov_reg;
                $parentMinistry->org_country == 'United States' ? NULL : 
                    ($parentMinistryLink .= ', ' . $parentMinistry->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }
            $schoolLevel = $profile->schoolLevel;                                                   // Create array of previously selected school levels
            usort($schoolLevel, [$this, 'level_sort']);                                             // Sort the multidimensional array
        
            $accreditations = $profile->accreditation;

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Staff ============================
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }
            }

            // ==================== Parent Ministry Pastor =====================
                if ($parentMinistry && 
                    $staff = Staff::find()
                        ->where(['ministry_id' => $parentMinistry->id])
                        ->andWhere(['sr_pastor' => 1])
                        ->andWhere(['confirmed' => 1])
                        ->one()) {
                    $pastor = $this->findActiveProfile($staff->staff_id);
                }

            if ($p == 'history') {
                $events = $profile->history;
            }

            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileSchool', [
                'profile' => $profile, 
                'loc' => $loc,
                'social' => $social,
                'schoolLevel' => $schoolLevel,
                'parentMinistry' => $parentMinistry,
                'parentMinistryLink' => $parentMinistryLink,
                'pastor' => $pastor,
                'accreditations' => $accreditations,
                'staffArray' => $staffArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render special ministry profile
     * @return mixed
     */
    public function actionSpecialMinistry($id, $city, $name, $p=NULL)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Special Ministry') {
            $parentMinistry = NULL;
            $parentMinistryLink = NULL;
            $social = NULL;
            if ($profile->ministry_of && 
                $parentMinistry = $this->findActiveProfile($profile->ministry_of)) {
                $parentMinistryLink = $parentMinistry->org_name . ', ' . 
                    $parentMinistry->org_city . ', ' . $parentMinistry->org_st_prov_reg;
                $parentMinistry->org_country == 'United States' ? NULL : 
                    ($parentMinistryLink .= ', ' . $parentMinistry->org_country);
            }
            if ($profile->social_id) {
                $social = $profile->social;
            }

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
             
            // ============================== Staff ============================
                $staff = Staff::find()->select('staff_id, staff_title')
                    ->where(['ministry_id' => $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('staff_id')
                    ->all();
                $i = 0;
                foreach ($staff as $stf) {                                                          // Combine multiple staff titles for same individual
                    if ($i > 0 && ($stf['staff_id'] == $staff[$i-1]['staff_id'])) {
                        $staff[$i-1]['staff_title'] .= ' &middot ' . $stf['staff_title'];
                        unset($staff[$i]);
                        $staff = array_values($staff);
                        continue;
                    }
                    $i++;
                }
                $ids = ArrayHelper::getColumn($staff, 'staff_id');
                $names = ArrayHelper::getColumn($staff, 'staff_title');
                $staffArray = Profile::findAll($ids);
                $i = 0;
                foreach ($staffArray as $stf) {
                    $stf->titleM = $names[$i];
                    $i++;
                }

            // ===================== Churches with Program =====================
                if (!($programChurchArray = $profile->churches)) {
                    $programChurchArray = NULL;
                }

            }
            if ($p == 'history') {
                $events = $profile->history;
            }

            ($profile->org_loc && $profile->show_map == Profile::MAP_PRIMARY) ?
                $loc = explode(',', $profile->org_loc) :
                $loc = NULL;

            return $this->render('profilePages/profileOrg', [
                'profile' => $profile,
                'loc' => $loc,
                'social' => $social,
                'parentMinistry' => $parentMinistry,
                'parentMinistryLink' => $parentMinistryLink,
                'programChurchArray' => $programChurchArray,
                'staffArray' => $staffArray,
                'events' => $events,
                'p' => $p,
            ]);
        } else {
            $this->redirect(['view-profile', 'id' => $id, 'city' => $city, 'name' => $name]);
        }
    }

    /**
     * Render evangelist profile
     * @return mixed
     */
    public function actionStaff($id, $city, $name, $p=NULL)
    {
        if (!$profile = $this->findViewProfile($id, $city, $name)) {
            if ($this->checkExpired($id)) {
                return $this->render('profilePages/profileExpired');
            }
        }
        if ($profile && $profile->type == 'Staff') {
            $profile->getformattedNames();
            $parentMinistry = NULL;
            $parentMinistryLink = NULL;
            $church = NULL;
            $churchLink = NULL;
            $social = NULL;
            $fellowship = NULL;
            $flwshipLink = NULL;
            if ($profile->ministry_of && 
                $parentMinistry = $this->findActiveProfile($profile->ministry_of)) {
                $parentMinistryLink = $parentMinistry->org_name . ', ' . $parentMinistry->org_city;
                $parentMinistry->org_st_prov_reg  ? ($parentMinistryLink .= ', ' . $parentMinistry->org_st_prov_reg) : NULL;
                $parentMinistry->org_country == 'United States' ? NULL : 
                    ($parentMinistryLink .= ', ' . $parentMinistry->org_country);
            }
            if($profile->home_church &&
                $profile->home_church != $profile->ministry_of &&
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

            if ($p == 'connections') {                                                              // Prepare connections list if connections link is clicked
                
            // ============================== Pastor ===========================
                if ($church) {
                    if ($staff = Staff::find()
                        ->where(['ministry_id' => $church->id])
                        ->andWhere(['sr_pastor' => 1])
                        ->andWhere(['confirmed' => 1])
                        ->one()) {
                        $pastor = $this->findActiveProfile($staff->staff_id);
                    }
                }

            // =========================== Other Ministries ====================
                if ($ministries = Staff::find()
                    ->where(['staff_id' => $profile->id])
                    ->andWhere(['ministry_other' => 1])
                    ->andWhere(['confirmed' => 1])
                    ->orderBy('id Asc')
                    ->all()) {
                    $i = 0;
                    foreach ($ministries as $mstry) {                                               // Combine multiple staff titles for same ministry
                        if ($i > 0 && ($mstry['ministry_id'] == $ministries[$i-1]['ministry_id'])) {
                            $ministries[$i-1]['staff_title'] .= ' &middot ' . $mstry['staff_title'];
                            unset($ministries[$i]);
                            $ministries = array_values($ministries);
                            continue;
                        }
                        $i++;
                    }
                    $ids = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $names = ArrayHelper::getColumn($ministries, 'staff_title');
                    $otherMinistryArray = Profile::findAll($ids);

                    $i = 0;
                    foreach ($otherMinistryArray as $min) {
                        $min->titleM = $names[$i];
                        $i++;
                    }
                }

            // ======================= Ministry Partners =======================
                if ($staffChurch = (new \yii\db\Query())                                            // Ministry partners at home church
                    ->select('staff_id')
                    ->from('staff')
                    ->where(['ministry_id' => $profile->home_church])
                    ->andWhere(['<>', 'staff_id', $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->groupBy('staff_id')
                    ->all()) {
                    $staffChurchIds = ArrayHelper::getColumn($staffChurch, 'staff_id');
                    $sChurchArray = Profile::findAll($staffChurchIds);
                }
                if ($staffMinistry = (new \yii\db\Query())                                          // Ministry partners at primary ministry (if not home church)
                    ->select('staff_id, ministry_id')
                    ->from('staff')
                    ->where(['ministry_id' => $profile->ministry_of])
                    ->andWhere(['<>', 'staff_id', $profile->id])
                    ->andWhere(['confirmed' => 1])
                    ->groupBy('staff_id')
                    ->all()) {
                    $sMinistryIds = ArrayHelper::getColumn($staffMinistry, 'staff_id');
                    $mMinistryIds = ArrayHelper::getcolumn($staffMinistry, 'ministry_id');
                    $sMinistryArray = Profile::findAll($sMinistryIds);

                    $i = 0;
                    foreach ($sMinistryArray as $sMinistry) {
                        $sMinistry->titleM = Profile::findOne($mMinistryIds[$i])->org_name;
                        $i++;
                    }
                }
                if ($ministries) {                                                                  // Ministry partners at other ministries
                    $otherArray = ArrayHelper::getColumn($ministries, 'ministry_id');
                    $otherIds = implode (",", $otherArray);
                    if ($staffOther = (new \yii\db\Query())
                        ->select('staff_id, ministry_id')
                        ->from('staff')
                        ->where('ministry_id IN ("' . $otherIds . '")')
                        ->andWhere(['<>', 'staff_id', $profile->id])
                        ->andWhere(['confirmed' => 1])
                        ->groupBy('staff_id')
                        ->orderBy('id')
                        ->all()) {
                        $sOtherIds = ArrayHelper::getColumn($staffOther, 'staff_id');
                        $mOtherIds = ArrayHelper::getcolumn($staffOther, 'ministry_id');
                        $sOtherArray = Profile::findAll($sOtherIds);

                        $i = 0;
                        foreach ($sOtherArray as $sOther) {
                            $sOther->titleM = Profile::findOne($mOtherIds[$i])->org_name;
                            $i++;
                        }
                    }
                }

            // ===================== Fellow Church Members =====================
                $memberArray = User::find()
                    ->where(['<>', 'screen_name', NULL])
                    ->andWhere(['ind_act_profiles' => NULL])
                    ->andWhere(['home_church' => $profile->home_church])
                    ->andWhere(['<>', 'id', $profile->user_id])
                    ->andWhere(['role' => 'Church Member'])
                    ->all();
            }
            if ($p == 'history') {
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
                'loc' => $loc,
                'social' => $social,
                'fellowship' => $fellowship,
                'flwshipLink' => $flwshipLink,
                'parentMinistry' => $parentMinistry,                                                // Primary ministry
                'parentMinistryLink' => $parentMinistryLink,
                'otherMinistryArray' => $otherMinistryArray,
                'church' => $church,
                'churchLink' => $churchLink,
                'schoolsAttended' => $schoolsAttended,
                'sChurchArray' => $sChurchArray,                                                    // Staff partners at church
                'sMinistryArray' => $sMinistryArray,                                                // Staff partners at primary ministry
                'sOtherArray' => $sOtherArray,                                                      // staff partners at other ministries
                'memberArray' => $memberArray,
                'pastor' => $pastor,
                'events' => $events,
                'p' => $p,
            ]);
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
     * Returns a fellowship object.
     * @param string $id
     * @return  fellowship model
     */
    public function findFellowship($id)
    {
        return Fellowship::find()
            ->where(['id' => $id])
            ->andWhere(['status' => Profile::STATUS_ACTIVE])
            ->one();
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