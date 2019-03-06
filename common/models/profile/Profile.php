<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\profile;

use borales\extensions\phoneInput\PhoneInputBehavior;
use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\missionary\Missionary;
use common\models\profile\Country;
use common\models\profile\FormsCompleted;
use common\models\profile\ProfileHasLike;
use common\models\profile\ProfileMail;
use common\models\profile\State;
use common\models\profile\Type;
use common\models\User;
use common\models\Utility;
use frontend\controllers\ProfileController;
use frontend\controllers\ProfileFormController;
use frontend\controllers\ProfileMailController;
use frontend\models\GeoCoder;
use sadovojav\cutter\behaviors\CutterBehavior;
use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int $user_id FOREIGN KEY (user_id) REFERENCES  user (id)
 * @property string $transfer_token
 * @property int $reviewed
 * @property string $type
 * @property string $sub_type
 * @property int $category
 * @property string $profile_name
 * @property string $url_name
 * @property string $url_loc
 * @property string $created_at
 * @property string $last_update User update
 * @property string $last_modified record altered
 * @property string $renewal_date
 * @property string $inactivation_date
 * @property int $has_been_inactivated
 * @property int $status
 * @property int $edit
 * @property string $tagline
 * @property string $title
 * @property string $description
 * @property int $ministry_of
 * @property int $home_church
 * @property string $image1
 * @property string $image2
 * @property string $flwsp_ass_level
 * @property string $org_name
 * @property string $org_address1
 * @property string $org_address2
 * @property string $org_po_box
 * @property string $org_city
 * @property string $org_st_prov_reg
 * @property string $org_state_long
 * @property string $org_zip
 * @property string $org_country
 * @property string $org_loc
 * @property string $org_po_address1
 * @property string $org_po_address2
 * @property string $org_po_city
 * @property string $org_po_st_prov_reg
 * @property string $org_po_state_long
 * @property string $org_po_zip
 * @property string $org_po_country
 * @property string $ind_first_name
 * @property string $ind_last_name
 * @property string $spouse_first_name
 * @property string $ind_address1
 * @property string $ind_address2
 * @property string $ind_po_box
 * @property string $ind_city
 * @property string $ind_st_prov_reg
 * @property string $ind_state_long
 * @property string $ind_zip
 * @property string $ind_country
 * @property string $ind_loc
 * @property string $ind_po_address1
 * @property string $ind_po_address2
 * @property string $ind_po_city
 * @property string $ind_po_st_prov_reg
 * @property string $ind_po_state_long
 * @property string $ind_po_zip
 * @property string $ind_po_country
 * @property int $show_map
 * @property string $phone
 * @property string $email
 * @property string $email_pvt
 * @property int $email_pvt_status
 * @property string $website
 * @property int $pastor_interim
 * @property int $cp_pastor
 * @property string $bible
 * @property string $worship_style
 * @property string $polity
 * @property string $packet
 * @property int $inappropriate
 */

class Profile extends yii\db\ActiveRecord
{

    /**
     * @const string $TYPE_* The profile types
     */
    const TYPE_PASTOR = 'Pastor';
    const TYPE_EVANGELIST = 'Evangelist';
    const TYPE_MISSIONARY = 'Missionary';
    const TYPE_CHAPLAIN = 'Chaplain';
    const TYPE_STAFF = 'Staff';
    const TYPE_CHURCH = 'Church';
    const TYPE_MISSION_AGCY = 'Mission Agency';
    const TYPE_FELLOWSHIP = 'Fellowship';
    const TYPE_ASSOCIATION = 'Association';
    const TYPE_CAMP = 'Camp';
    const TYPE_SCHOOL = 'School';
    const TYPE_PRINT = 'Print Ministry';
    const TYPE_MUSIC = 'Music Ministry';
    const TYPE_SPECIAL = 'Special Ministry';

    /**
     * @const string $SUBTYPE_* The profile subtypes
     */
    const SUBTYPE_PASOTR_ASSOCIATE = 'Associate Pastor';
    const SUBTYPE_PASTOR_ASSISTANT = 'Assistant Pastor';
    const SUBTYPE_PASTOR_MUSIC = 'Music Pastor';
    const SUBTYPE_PASTOR_PASTOR = 'Pastor';
    const SUBTYPE_PASTOR_EMERITUS = 'Pastor Emeritus';
    const SUBTYPE_PASTOR_SENIOR = 'Senior Pastor';
    const SUBTYPE_PASTOR_YOUTH = 'Youth Pastor';
    const SUBTYPE_PASTOR_ELDER = 'Elder';
    const SUBTYPE_MISSIONARY_CP = 'Church Planter';
    const SUBTYPE_MISSIONARY_BT = 'Bible Translator';
    const SUBTYPE_MISSIONARY_MM = 'Medical Missionary';
    const SUBTYPE_CHAPLAIN_J = 'Jail Chaplain';
    const SUBTYPE_CHAPLAIN_M = 'Military Chaplain';

    /**
     * @const int $CATEGORY_* The category (individual or organization) of the profile.
     */
    const CATEGORY_IND = 10;
    const CATEGORY_ORG = 20;

    /**
     * @const int $STATUS_* The status of the profile.
     */
    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20;
    const STATUS_EXPIRED = 25;
    const STATUS_TRASH = 30;

    /**
     * @const int $PRIVATE_EMAIL_* Private email request status
     * Default is 0
     */
    const PRIVATE_EMAIL_NONE = 0;
    const PRIVATE_EMAIL_ACTIVE = 10;
    const PRIVATE_EMAIL_PENDING = 20;

    /**
     * @const int $MAP_* Map choices to determine which map to display on profile
     * *_GPS indicates user entered GPS coordinates
     * Default is NULL
     */
    const MAP_PRIMARY = 10;
    const MAP_CHURCH = 20;
    const MAP_MINISTRY = 30;
    const MAP_CHURCH_PLANT = 40;

    /**
     * @const int $EDIT_* Indicates if profile is newly created or edited as existing; Affects progression through profile forms.
     */
    const EDIT_NO = 0;      // Profile is new
    const EDIT_YES = 10;    // Profile is existing

    /**
    * @var array $icon Outputs html markup for glyphicon icons for each profile type
    */
    public static $icon = [
        self::TYPE_ASSOCIATION => '<span class="glyphicons glyphicons-group"></span>',
        self::TYPE_CAMP => '<span class="glyphicons glyphicons-camping"></span>',
        self::TYPE_CHAPLAIN => '<span class="glyphicons glyphicons-shield"></span>',
        self::TYPE_CHURCH => '<span class="glyphicons glyphicons-temple-christianity-church type-icon"></span>',
        self::TYPE_EVANGELIST => '<span class="glyphicons glyphicons-fire"></span>',
        self::TYPE_FELLOWSHIP => '<span class="glyphicons glyphicons-handshake"></span>',
        self::TYPE_MISSION_AGCY => '<span class="glyphicons glyphicons-globe-af"></span>',
        self::TYPE_MISSIONARY => '<span class="glyphicons glyphicons-person-walking"></span>',
        self::TYPE_MUSIC => '<span class="glyphicons glyphicons-music"></span>',
        self::TYPE_PASTOR => '<span class="glyphicons glyphicons-book-open"></span>',
        self::TYPE_PRINT => '<span class="glyphicons glyphicons-book"></span>',
        self::TYPE_SCHOOL => '<span class="glyphicons glyphicons-education"></span>',
        self::TYPE_SPECIAL => '<span class="glyphicons glyphicons-global"></span>',
        self::TYPE_STAFF => '<span class="glyphicons glyphicons-briefcase"></span>',
    ];

    /**
     * @var string $ptype User entered pastor sub-type
     */
    public $ptype;

    /**
     * @var string $mtype User entered missionary sub-type
     */
    public $mtype;

    /**
     * @var string $ctype User entered chaplain sub-type
     */
    public $ctype;

    /**
     * @var string $name User entered fellowship or association name
     */
    public $name;

    /**
     * @var string $acronym User entered fellowship or association acronym
     */
    public $acronym;

    /**
     * @var string $aName User entered alternate name for various forms/scenarios
     */
    public $aName;

    /**
     * @var string $aAcronym User entered alternate acronym for various forms/scenarios
     */
    public $aAcronym;

    /**
     * @var string $select User selection for primary data list on form
     */
    public $select;

    /**
     * @var string $selectM User selection for "add another ministry"
     */
    public $selectM;

    /**
     * @var string $titleM User entered title for "add another ministry"
     */
    public $titleM;

    /**
     * @var int $duplicateId Id of a duplicate profile in the event user tries to create a duplicate
     */
    public $duplicateId;

    /**
     * @var string $housingSelect User selection of whether church has missions housing
     */
    public $housingSelect;

    /**
     * @var string $location Stores city and state for geolookup on browse page
     */
    public $location;

    /**
     * @var string $phoneFull Stores fully formatted phone number on contact page
     */
    public $phoneFull;

    /**
     * @var int $map Checkbox entry for show_map
     */
    public $map;

    /**
     * @var string $staff Hidden input on form6
     */
    public $staff;

    /**
     * @var array $unconfirmed Ids of any unconfirmed staff profiles associated with an organization
     */
    public $unconfirmed;

     /**
     * @var bool $events Indicates if a profile has associated timeline events
     */
    public $events;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {   
        return 'profile';
    }

    public function behaviors()
    {   
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => 'last_modified',
                'value' => new Expression('NOW()'),                                                 // UTC time; this is important for Solr delta-import, which checks records against UTC time
            ],
            'image' => [
                'class' => CutterBehavior::className(),
                'attributes' => ['image1', 'image2'],
                'baseDir' => '/uploads/image',
                'basePath' => '@webroot',
            ],
            'phoneInput' => PhoneInputBehavior::className(),
        ];
    }

    
    public function scenarios() {
        return[
    // create a new profile
            'create' => ['profile_name', 'type', 'ptype', 'mtype'],
    // transfer a profile
            'transfer' => ['select'],
    // nd-org: Name & Description Organization
            'nd-org' => ['org_name', 'tagline', 'description', 'url_name'],
    // nd-ind: Name & Description Individual
            'nd-ind' => ['ind_first_name', 'spouse_first_name', 'ind_last_name', 'title', 'tagline', 'description', 'url_name'],
    // nd-flwsp_ass: Name & Description Fellowship  or Association
            'nd-flwsp_ass' => ['select', 'org_name', 'name', 'acronym', 'flwsp_ass_level', 'tagline', 'description', 'url_name'],
    // nd-miss_agency: Name & Description Mission Agency
            'nd-miss_agency' => ['select', 'name', 'acronym', 'tagline', 'description', 'url_name'],
    // nd-school: Name & Description School
            'nd-school' => ['select', 'name', 'tagline', 'description', 'url_name'],
    // i1: Image 1
            'i1' => ['image1'],
    // i2: Image 2
            'i2' => ['image2'],
    // lo-org: Location Organization
            'lo-org' => ['org_address1', 'org_address2', 'org_city', 'org_st_prov_reg', 'org_zip', 'org_country', 'map', 'org_loc', 'org_po_address1', 'org_po_address2', 'org_po_box', 'org_po_city', 'org_po_st_prov_reg', 'org_po_zip', 'org_po_country', 'url_loc'],
    // lo-ind: Location Individual
            'lo-ind' => ['ind_address1', 'ind_address2', 'ind_city', 'ind_st_prov_reg', 'ind_zip', 'ind_country', 'map', 'ind_loc', 'ind_po_address1', 'ind_po_address2', 'ind_po_box', 'ind_po_city', 'ind_po_st_prov_reg', 'ind_po_zip', 'ind_po_country', 'url_loc'],
    // co: Contact
            'co' => ['phone', 'email', 'email_pvt', 'website'],
    // co: Contact - Forwarding Email
            'co-fe' => ['phone', 'email', 'email_pvt', 'website'],
    // sf: Staff - Church
            'sf-church' => ['ind_first_name', 'ind_last_name', 'spouse_first_name', 'pastor_interim', 'cp_pastor'],
    // sf-org: Staff Organization
            'sf-org' => ['staff'],
    // hc: Home Church
            'hc' => ['home_church', 'map'],
    // pm-required: Parent Ministry for Staff
            'pm-required' => ['ministry_of', 'selectM', 'titleM', 'map'],
    // pm-ind: Other Ministries of individuals
            'pm-ind' => ['ministry_of', 'selectM', 'titleM', 'map'],
    // pm-org: Parent Ministry
            'pm-org' => ['ministry_of', 'map'],
    // pg: Programs
            'pg' => ['select'],
    // sa: Schools Attended
            'sa' => ['select'],
    // sl: School Levels
            'sl' => ['select'],
    // ma-church: Mission Agencies - Church
            'ma-church' => ['select', 'housingSelect', 'packet'],
    // di: Distinctives
            'di' => ['bible', 'worship_style', 'polity'],
    // as-school: Associations for school
            'as-school' => ['select'],
    // as-ind: Associations for individuals
            'as-ind' => ['select', 'name', 'acronym'],
    // as-church: Associations for churches
            'as-church' => ['select', 'name', 'acronym', 'selectM', 'aName', 'aAcronym'],
    // ta: Tag
            'ta' => ['select'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
    // create a new profile
            [['profile_name', 'type'], 'required', 'on' => 'create'],
            ['ptype', 'required', 'when' => function($profile) {
                return $profile->type == 'Pastor';
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-type').val() == 'Pastor';
            }", 'message' => 'Pastor type is required.', 'on' => 'create'],
            ['mtype', 'required', 'when' => function($profile) {
                return $profile->type == self::TYPE_MISSIONARY;
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-type').val() == '" . self::TYPE_MISSIONARY . "';
            }", 'message' => 'Missionary type is required.', 'on' => 'create'],
            ['profile_name', 'string', 'max' => 60, 'on' =>'create'],
            ['profile_name', 'filter', 'filter' => 'strip_tags', 'on' => 'create'],

    // transfer a profile
            ['select', 'email', 'message' => 'Please enter a valid email', 'on' => 'transfer'],

    // nd-org: Name & Description Organization ('org_name', 'tagline', 'description')
            [['org_name', 'description'], 'required', 'on' => 'nd-org'],
            ['tagline', 'default', 'value' => NULL,'on' => 'nd-org'],
            [['org_name', 'tagline'], 'string', 'max' => 60, 'on' => 'nd-org'],
            [['org_name', 'tagline'], 'trim', 'on' => 'nd-org'],
            ['description', 'string', 'max' => 1500, 'on' => 'nd-org', 'message' => 'Your text exceeds 1500 characters.'],
            [['org_name', 'tagline', 'description'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'nd-org'],
            [['url_name'], 'safe', 'on' => 'nd-org'],

    // nd-ind: Name & Description Individual ('ind_first_name', 'spouse_first_name', 'ind_last_name', 'title', 'tagline', 'description', 'select')
            [['ind_first_name', 'ind_last_name', 'description'], 'required', 'on' => 'nd-ind'],
            [['spouse_first_name', 'tagline'], 'default', 'value' => NULL,'on' => 'nd-ind'],
            [['ind_first_name', 'spouse_first_name'], 'string', 'max' => 20, 'on' => 'nd-ind'],
            ['ind_last_name', 'string', 'max' => 40, 'on' => 'nd-ind'],
            [['title', 'tagline'], 'string', 'max' => 60, 'on' => 'nd-ind'],
            [['ind_first_name', 'ind_last_name', 'spouse_first_name', 'title', 'tagline'], 'trim', 'on' => 'nd-ind'],
            ['description', 'string', 'max' => 1500, 'on' => 'nd-ind', 'message' => 'Your text exceeds 1500 characters.'],      
            [['ind_first_name', 'ind_last_name', 'spouse_first_name', 'title', 'tagline'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'nd-ind'],
            [['url_name'], 'safe', 'on' => 'nd-ind'],

    // nd-flwsp_ass: Name & Description Fellowship or Association ('select', 'name', 'acronym', 'flwsp_ass_level', 'tagline', 'description')
            [['flwsp_ass_level', 'description'], 'required', 'on' => 'nd-flwsp_ass'],
            ['select', 'required', 'when' => function($profile) {
                return $profile->name == NULL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-name').val() == '';
            }", 'message' => 'A name is required.', 'on' => 'nd-flwsp_ass'],
            [['name', 'acronym'], 'required', 'when' => function($profile) {
                return $profile->select == NULL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-select').val() == '';
            }", 'message' => 'Name and acronym are required.', 'on' => 'nd-flwsp_ass'],
            [['name', 'acronym', 'tagline'], 'default', 'value' => NULL,'on' => 'nd-flwsp_ass'],
            [['name','tagline'], 'string', 'max' => 60, 'on' => 'nd-flwsp_ass'],
            [['acronym'], 'string', 'max' => 20, 'on' => 'nd-flwsp_ass'],
            [['description'], 'string', 'max' => 1500, 'on' => 'nd-flwsp_ass', 'message' => 'Your text exceeds 1500 characters.'],
            [['name', 'acronym', 'tagline', 'description'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'nd-flwsp_ass'],
            [['select', 'url_name'], 'safe', 'on' => 'nd-flwsp_ass'],

    // nd-miss_agency: Name & Description Mission Agency ('select', 'name', 'acronym', 'tagline', 'description')
            ['description', 'required', 'on' => 'nd-miss_agency'],
            ['select', 'required', 'when' => function($profile) {
                return $profile->name == NULL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-name').val() == '';
            }", 'message' => 'A name is required.', 'on' => 'nd-miss_agency'],
            [['name', 'acronym'], 'required', 'when' => function($profile) {
                return $profile->select == NULL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-select').val() == '';
            }", 'message' => 'Name and acronym are required.', 'on' => 'nd-miss_agency'],
            [['name', 'acronym', 'tagline'], 'default', 'value' => NULL, 'on' => 'nd-miss_agency'],
            [['name', 'tagline'], 'string', 'max' => 60, 'on' => 'nd-miss_agency'],
            [['acronym'], 'string', 'max' => 20, 'on' => 'nd-miss_agency'],
            [['description'], 'string', 'max' => 1500, 'on' => 'nd-miss_agency', 'message' => 'Your text exceeds 1500 characters.'],
            [['name', 'acronym', 'tagline', 'description'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'nd-miss_agency'],
            [['select', 'url_name'], 'safe', 'on' => 'nd-miss_agency'],

    // nd-school: Name & Description School ('select', 'schoolName', 'tagline', 'description')
            ['description', 'required', 'on' => 'nd-school'],
            ['select', 'required', 'when' => function($profile) {
                return $profile->name == NULL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-name').val() == '';
            }", 'message' => 'School name is required.', 'on' => 'nd-school'],
            ['name', 'required', 'when' => function($profile) {
                return $profile->select == NULL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-select').val() == '';
            }", 'message' => 'School name is required.', 'on' => 'nd-school'],
            [['schoolName', 'tagline'], 'default', 'value' => NULL, 'on' => 'nd-school'],
            [['schoolName', 'tagline'], 'string', 'max' => 60, 'on' => 'nd-school'],
            [['description'], 'string', 'max' => 1500, 'on' => 'nd-school', 'message' => 'Your text exceeds 1500 characters.'],
            [['schoolName', 'tagline', 'description'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'nd-school'],
            [['select', 'url_name'], 'safe', 'on' => 'nd-school'],

    // i1: Image 1 ('image1')
            ['image1', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 4000, 'skipOnEmpty' => true, 'on' => 'i1'],

    // i2: Image 2 ('image2')
            ['image2', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 1, 'maxSize' => 1024 * 10000, 'skipOnEmpty' => true, 'on' => 'i2'],

    // lo-org: Location Organization ('org_address1', 'org_address2', 'org_city', 'org_st_prov_reg', 'org_zip', 'org_country', 'map', 'org_po_address1', 'org_po_address2', 'org_po_box', 'org_po_city', 'org_po_st_prov_reg', 'org_po_zip', 'org_po_country')
            ['org_address1', 'required', 'when' => function($profile) {                             // address1 is required if po_address1 and po_box are missing
                return (empty($profile->org_po_address1) && empty($profile->org_po_box));
            }, 'whenClient' => "function (attribute, value) {
                return (($('#profile-org_po_address1').val() == '') && ($('#profile-org_po_box').val() == ''));
            }", 'message' => 'A physical or mailing address is required.', 'on' => 'lo-org'],
             ['org_po_address1', 'required', 'when' => function($profile) {                         // po_address1 is required if address1 and po_box are missing
                return (empty($profile->org_address1) && empty($profile->org_po_box));
            }, 'whenClient' => "function (attribute, value) {
                return (($('#profile-org_address1').val() == '') && ($('#profile-org_po_box').val() == ''));
            }", 'message' => 'A physical or mailing address is required.', 'on' => 'lo-org'],
             ['org_po_box', 'required', 'when' => function($profile) {                              // po_box is required if address1 and po_address1 are missing
                return (empty($profile->org_address1) && empty($profile->org_po_address1));
            }, 'whenClient' => "function (attribute, value) {
                return (($('#profile-org_address1').val() == '') && ($('#profile-org_po_address1').val() == ''));
            }", 'message' => 'A physical or mailing address is required.', 'on' => 'lo-org'],
            ['org_st_prov_reg', 'exist', 'targetClass' => 'common\models\profile\State', 'targetAttribute' => 'state', 'when' => function($profile) {  // state is required and must be in state table for US physical ddress
                return $profile->org_country == 'United States';
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-org_country').val() == 'United States';
            }", 'message' => 'Enter a valid US state', 'on' => 'lo-org'],
            ['org_zip', 'required', 'when' => function($profile) {                                  // zip is required for US physical address
                return $profile->org_country == 'United States';
            }, 'whenClient' => "function (attribute, value) { 
                return $('#profile-org_country').val() == 'United States';
            }", 'message' => 'Zip code is required for a US address.', 'on' => 'lo-org'],
            ['org_po_st_prov_reg', 'exist', 'targetClass' => 'common\models\profile\State', 'targetAttribute' => 'state', 'when' => function($profile) {  // po_state is required and must be in state table for US mailing address
                return $profile->org_po_country == 'United States';
            }, 'whenClient' => "function (attribute, value) { 
                return $('#profile-org_po_country').val() == 'United States';
            }", 'message' => 'Enter a valid US state', 'on' => 'lo-org'],
            ['org_po_zip', 'required', 'when' => function($profile) {                               // po_zip is required for US mailing address
                return $profile->org_po_country == 'United States';
            }, 'whenClient' => "function (attribute, value) { 
                return $('#profile-org_po_country').val() == 'United States';
            }", 'message' => 'Zip code is required for a US address.', 'on' => 'lo-org'],
            [['org_address1', 'org_address2', 'org_st_prov_reg', 'org_zip', 'org_country', 'org_po_address1', 'org_po_address2', 'org_po_box', 'org_po_st_prov_reg', 'org_po_zip', 'org_po_country'], 'default', 'value' => NULL, 'on' => 'lo-org'],
            [['org_address1', 'org_address2', 'org_city', 'org_po_address1', 'org_po_address2', 'org_po_city'], 'string', 'max' => 60, 'on' => 'lo-org'],
            [['org_loc'], 'string', 'max' => 24, 'on' => 'lo-org'],
            [['org_po_box'], 'string', 'max' => 6, 'on' => 'lo-org'],
            [['org_st_prov_reg', 'org_po_st_prov_reg'], 'string', 'max' => 50, 'on' => 'lo-org'],
            [['org_zip', 'org_po_zip'], 'string', 'max' => 20, 'on' => 'lo-org'],
            [['org_address1', 'org_address2', 'org_city', 'org_st_prov_reg', 'org_zip', 'org_po_address1', 'org_po_address2', 'org_po_city', 'org_po_st_prov_reg', 'org_po_zip', 'org_loc'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'lo-org'],
            [['org_address1', 'org_address2', 'org_city', 'org_st_prov_reg', 'org_zip', 'org_po_address1', 'org_po_address2', 'org_po_city', 'org_po_st_prov_reg', 'org_po_zip', 'org_loc'], 'trim', 'skipOnEmpty' => true, 'on' => 'lo-org'],
            [['org_address1'], 'validatePhysicalAddress', 'on' => 'lo-org'],
            [['org_po_address1'], 'validateMailingAddress', 'on' => 'lo-org'],
            [['org_po_box'], 'validateMailingAddress', 'on' => 'lo-org'],
            [['org_po_address1, org_po_box'], 'validateMailingAddress', 'on' => 'lo-org'],
            [['map', 'url_loc'], 'safe', 'on' => 'lo-org'],

    // lo-ind: Location Individual ('ind_address1', 'ind_address2', 'ind_city', 'ind_st_prov_reg', 'ind_zip', 'ind_country', 'map', 'ind_po_address1', 'ind_po_address2', 'ind_po_box', 'ind_po_city', 'ind_po_st_prov_reg', 'ind_po_zip', 'ind_po_country')
            ['ind_address1', 'required', 'when' => function($profile) {                             // address1 is required if po_address1 and po_box are missing
                return (empty($profile->ind_po_address1) && empty($profile->ind_po_box));
            }, 'whenClient' => "function (attribute, value) {
                return (($('#profile-ind_po_address1').val() == '') && ($('#profile-ind_po_box').val() == ''));
            }", 'message' => 'A physical or mailing address is required.', 'on' => 'lo-ind'],
            ['ind_po_address1', 'required', 'when' => function($profile) {                         // po_address1 is required if address1 and po_box are missing
                return (empty($profile->ind_address1) && empty($profile->ind_po_box));
            }, 'whenClient' => "function (attribute, value) {
                return (($('#profile-ind_address1').val() == '') && ($('#profile-ind_po_box').val() == ''));
            }", 'message' => 'A physical or mailing address is required.', 'on' => 'lo-ind'],
            ['ind_po_box', 'required', 'when' => function($profile) {                              // po_box is required if address1 and po_address1 are missing
                return (empty($profile->ind_address1) && empty($profile->ind_po_address1));
            }, 'whenClient' => "function (attribute, value) {
                return (($('#profile-ind_address1').val() == '') && ($('#profile-ind_po_address1').val() == ''));
            }", 'message' => 'A physical or mailing address is required.', 'on' => 'lo-ind'],
            ['ind_st_prov_reg', 'exist', 'targetClass' => 'common\models\profile\State', 'targetAttribute' => 'state', 'when' => function($profile) {  // state is required and must be in state table for US physical ddress
                return $profile->ind_country == 'United States';
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-ind_country').val() == 'United States';
            }", 'message' => 'Enter a valid US state', 'on' => 'lo-ind'],
            ['ind_zip', 'required', 'when' => function($profile) {                                  // zip is required for US physical address
                return $profile->ind_country == 'United States';
            }, 'whenClient' => "function (attribute, value) { 
                return $('#profile-ind_country').val() == 'United States';
            }", 'message' => 'Zip code is required for a US address.', 'on' => 'lo-ind'],
            ['ind_po_st_prov_reg', 'exist', 'targetClass' => 'common\models\profile\State', 'targetAttribute' => 'state', 'when' => function($profile) {  // po_state is required and must be in state table for US mailing address
                return $profile->ind_po_country == 'United States';
            }, 'whenClient' => "function (attribute, value) { 
                return $('#profile-ind_po_country').val() == 'United States';
            }", 'message' => 'Enter a valid US state', 'on' => 'lo-ind'],
            ['ind_po_zip', 'required', 'when' => function($profile) {                               // po_zip is required for US mailing address
                return $profile->ind_po_country == 'United States';
            }, 'whenClient' => "function (attribute, value) { 
                return $('#profile-ind_po_country').val() == 'United States';
            }", 'message' => 'Zip code is required for a US address.', 'on' => 'lo-ind'],
            [['ind_address1', 'ind_address2', 'ind_st_prov_reg', 'ind_zip', 'ind_country', 'ind_po_address1', 'ind_po_address2', 'ind_po_box', 'ind_po_st_prov_reg', 'ind_po_zip', 'ind_po_country'], 'default', 'value' => NULL, 'on' => 'lo-ind'],
            [['ind_address1', 'ind_address2', 'ind_city', 'ind_po_address1', 'ind_po_address2', 'ind_po_city'], 'string', 'max' => 60, 'on' => 'lo-ind'],
            [['ind_loc'], 'string', 'max' => 24, 'on' => 'lo-ind'],
            [['ind_po_box'], 'string', 'max' => 6, 'on' => 'lo-ind'],
            [['ind_st_prov_reg', 'ind_po_st_prov_reg'], 'string', 'max' => 50, 'on' => 'lo-ind'],
            [['ind_zip', 'ind_po_zip'], 'string', 'max' => 20, 'on' => 'lo-ind'],
            [['ind_address1', 'ind_address2', 'ind_city', 'ind_st_prov_reg', 'ind_zip', 'ind_po_address1', 'ind_po_address2', 'ind_po_city', 'ind_po_st_prov_reg', 'ind_po_zip', 'ind_loc'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'lo-ind'],
            [['ind_address1', 'ind_address2', 'ind_city', 'ind_st_prov_reg', 'ind_zip', 'ind_po_address1', 'ind_po_address2', 'ind_po_city', 'ind_po_st_prov_reg', 'ind_po_zip', 'ind_loc'], 'trim', 'skipOnEmpty' => true, 'on' => 'lo-ind'],
            [['ind_address1'], 'validatePhysicalAddress', 'on' => 'lo-ind'],
            [['ind_po_address1'], 'validateMailingAddress', 'on' => 'lo-ind'],
            [['ind_po_box'], 'validateMailingAddress', 'on' => 'lo-ind'],
            [['ind_po_address1, ind_po_box'], 'validateMailingAddress', 'on' => 'lo-ind'],
            [['map', 'url_loc'], 'safe', 'on' => 'lo-ind'],

    // co: Contact ('phone', 'email', 'email_pvt', 'website')
            [['phone', 'email'], 'required', 'on' => 'co'],
            [['phone'], 'string', 'on' => 'co'],
            [['phone'], PhoneInputValidator::className(),'on' => 'co'],
            [['email', 'email_pvt'], 'string', 'max' => 60, 'on' => 'co'],
            [['email', 'email_pvt'], 'email', 'message' => 'Please enter a valid email', 'on' => 'co'],
            [['website'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' =>true, 'on' => 'co'],
        // Backend email forwarding ('email', 'email_pvt')
            [['email'], 'string', 'max' => 30, 'on' => 'default'],
            [['email_pvt'], 'string', 'max' => 10, 'on' => 'default'],
            [['email', 'email_pvt'], 'email', 'message' => 'Please enter a valid email', 'on' => 'default'],

    // co-fe: Contact - Forwarding Email ('phone', 'email', 'email_pvt', 'website')
            [['phone', 'email_pvt'], 'required', 'on' => 'co-fe'],
            [['phone'], 'string', 'on' => 'co-fe'],
            [['phone'], PhoneInputValidator::className(),'on' => 'co-fe'],
            [['email', 'email_pvt'], 'string', 'max' => 60, 'on' => 'co-fe'],
            [['email', 'email_pvt'], 'email','message' => 'Please enter a valid email', 'on' => 'co-fe'],
            [['website'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' =>true, 'on' => 'co-fe'],

    // sf-church: Staff Church ('ind_first_name', 'ind_last_name', 'spouse_first_name', 'pastor_interim', 'cp_pastor')
            [['ind_first_name', 'ind_last_name'], 'required', 'on' => 'sf-church'],
            [['spouse_first_name', 'pastor_interim', 'cp_pastor'], 'default', 'value' => NULL, 'on' => 'sf-church'],
            [['ind_first_name', 'spouse_first_name'], 'string', 'max' => 20, 'on' => 'sf-church'],
            ['ind_last_name', 'string', 'max' => 40, 'on' => 'sf-church'],
            [['ind_first_name', 'ind_last_name', 'spouse_first_name'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'sf-church'],
    
    // sf-org: Staff Organization ('staff')
            ['staff', 'safe', 'on' => 'sf-org'],

    // hc: Home Church ('home_church', 'map')
            ['home_church', 'required', 'on' => 'hc'],
            ['map', 'safe', 'on' => 'hc'],

    // pm-required: Parent Ministry for Staff ('select', 'selectM', 'titleM', 'map')
            ['ministry_of', 'required', 'on' => 'pm-required'],
            ['titleM', 'string', 'max' => 60, 'on' => 'pm-required'],
            ['map', 'safe', 'on' => 'pm-required'],

    // pm-ind: Other Ministries for Individuals ('select', 'selectM', 'titleM', 'map')
            ['titleM', 'string', 'max' => 60, 'on' => 'pm-ind'],
            [['ministry_of', 'selectM', 'map'], 'safe', 'on' => 'pm-ind'],

    // pm-org: Parent Ministry ('ministry_of', 'map')
            ['ministry_of', 'safe', 'on' => 'pm-org'],
            [['map'], 'safe', 'on' => 'pm-org'],

    // pg: Programs ('select')
            ['select', 'safe', 'on' => 'pg'],

    // sa: Schools Attended ('select')
            ['select', 'safe', 'on' => 'sa'],

    // sl: School Levels ('select')
            ['select', 'safe', 'on' => 'sl'],

    // ma-church: Mission Agencies - Church ('select', 'housingSelect', 'packet')
            ['select', 'default', 'value' => NULL, 'on' => 'ma-church'],
            ['housingSelect', 'default', 'value' => 'N', 'on' => 'ma-church'],
            ['packet', 'file', 'extensions' => 'pdf', 'mimeTypes' => 'application/pdf', 'maxFiles' => 1, 'maxSize' => 1024 * 6000, 'skipOnEmpty' => true, 'on' => 'ma-church'],

    // di: Distinctives ('bible', 'worship_style', 'polity')
            [['bible', 'worship_style', 'polity'], 'required', 'on' => 'di'],

    // as-school: Associations - School ('select')
            ['select', 'safe', 'on' => 'as-school'],

    // as-ind: Associations - Individual ('select', 'name', 'acronym')
            ['select', 'safe', 'on' => 'as-ind'],
            [['name', 'acronym'], 'default', 'value' => NULL, 'on' => 'as-ind'],
            ['name', 'string', 'max' => 60, 'on' => 'as-ind'],
            ['acronym', 'string', 'max' => 10, 'on' => 'as-ind'],
            [['name', 'acronym'], 'filter', 'filter' => 'strip_tags', 'on' => 'as-ind'],
            ['acronym', 'validateAcronym', 'on' => 'as-ind'],
            ['name', 'validateUniqueFellowship', 'on' => 'as-ind'],
            ['acronym', 'validateUniqueFlwshpAcronym', 'on' => 'as-ind'],

    // as-church: Associations - Church ('select', 'name', 'acronym', 'selectM', 'aName', 'aAcronym')
            [['select', 'selectM'], 'safe', 'on' => 'as-church'],
            [['name', 'acronym', 'ass_id', 'aName', 'aAcronym'], 'default', 'value' => NULL, 'on' => 'as-church'],
            [['name', 'aName'], 'string', 'max' => 60, 'on' => 'as-church'],
            [['acronym', 'aAcronym'], 'string', 'max' => 10, 'on' => 'as-church'],
            [['name', 'acronym', 'aName', 'aAcronym'], 'filter', 'filter' => 'strip_tags', 'on' => 'as-church'],
            [['acronym', 'associationAcronym'], 'validateAcronym', 'on' => 'as-church'],
            ['name', 'validateUniqueFellowship', 'on' => 'as-church'],
            ['acronym', 'validateUniqueFlwshpAcronym', 'on' => 'as-church'],
            ['aName', 'validateUniqueAssociation', 'on' => 'as-church'],
            ['aAcronym', 'validateUniqueAssAcronym', 'on' => 'as-church'],

    // ta: Tag ('select')
            ['select', 'safe', 'on' => 'ta'],
        ];
    }

    /**
     * @inheritdoc Assign attribute labels by scenario
     */
    public function attributeLabels()
    {
        switch ($this->scenario) {
    
    // create a new profile
            case 'create':
            return [
                'profile_name' => 'Name this profile',
                'type' => 'Profile Type',
                'ptype' => 'Pastor Type',
                'mtype' => 'Missionary Type',
                'ctype' => 'Chaplain Type',
            ];
            break;

    // transfer a profile
            case 'transfer':
            return [
                'select' => '',
            ];
            break;
            
    // nd-org: Name & Description Organization ('org_name', 'tagline', 'description')
            case 'nd-org':
            return [
                'org_name' => $this->orgNameLabel,
                'tagline' => 'Ministry Tagline',
                'description' => 'Description',
            ];
            break;

    // nd-ind: Name & Description Individual ('ind_first_name', 'spouse_first_name', 'ind_last_name', 'title', 'tagline', 'description')
            case 'nd-ind':
            return [
                'ind_first_name' => 'First Name',
                'ind_last_name' => 'Last Name',
                'spouse_first_name' => 'Spouse First Name',
                'title' => 'Ministry Title',
                'tagline' => 'Personal or Ministry Tagline',
                'description' => 'Description',
            ];
            break;

    // nd-flwsp_ass: Name & Description Fellowship ('select', 'name', 'acronym', 'tagline', 'description')
            case 'nd-flwsp_ass':
            return [
                'select' => $this->type . ' Name',
                'name' => 'Or enter a new ' . $this->type . ' name here',
                'acronym' => 'Acronym',
                'tagline' => $this->type . ' Tagline',
                'flwsp_ass_level' => $this->type . ' Level',
                'description' => 'Description',
            ];
            break;

    // nd-miss_agency: Name & Description Mission Agency ('select', 'name', 'acronym', 'tagline', 'description')
            case 'nd-miss_agency':
            return [
                'select' => 'Mission Agency Name',
                'name' => 'Or enter a new name here',
                'acronym' => 'Acronym',
                'tagline' => 'Mission Agency Tagline',
                'description' => 'Description',
            ];
            break;

    // nd-school: Name & Description School ('select', 'schoolName', 'tagline', 'description')
            case 'nd-school':
            return [
                'select' => 'School Name',
                'tagline' => 'School Tagline',
                'description' => 'Description',
            ];
            break;

    // i1: Image 1 ('image1')
            case 'i1':
            return ['image1' => ''];
            break;

    // i2: Image 2 ('image2')
            case 'i2':
            return ['image2' => ''];
            break;

    // lo-org: Location Organization ('org_address1', 'org_address2', 'org_city', 'org_st_prov_reg', 'org_zip', 'org_country', 'map', 'org_po_address1', 'org_po_address2', 'org_po_box', 'org_po_city', 'org_po_st_prov_reg', 'org_po_zip', 'org_po_country')
            case 'lo-org':
            return [
                'org_address1' => 'Street Address 1',
                'org_address2' => 'Street Address 2',
                'org_city' => 'City',
                'org_st_prov_reg' => 'State/Providence/Region',
                'org_zip' => 'Postal Code',
                'org_country' => 'Country',
                'org_loc' => 'GPS Coordinates',
                'map' => 'Show Google Map of this address on Profile',
                'org_po_address1' => 'Street Address 1',
                'org_po_address2' => 'Street Address 2',
                'org_po_box' => 'PO Box #',
                'org_po_city' => 'City',
                'org_po_st_prov_reg' => 'State/Providence/Region',
                'org_po_zip' => 'Postal Code',
                'org_po_country' => 'Country',
            ];
            break;

    // lo-ind: Location Individual ('ind_address1', 'ind_address2', 'ind_city', 'ind_st_prov_reg', 'ind_zip', 'ind_country', 'map', 'ind_po_address1', 'ind_po_address2', 'ind_po_box', 'ind_po_city', 'ind_po_st_prov_reg', 'ind_po_zip', 'ind_po_country')
            case 'lo-ind':
            return [
                'ind_address1' => 'Street Address 1',
                'ind_address2' => 'Street Address 2',
                'ind_city' => 'City',
                'ind_st_prov_reg' => 'State/Providence/Region',
                'ind_zip' => 'Postal Code',
                'ind_country' => 'Country',
                'ind_loc' => 'GPS Coordinates',
                'map' => 'Show a Google Map of this address on my Profile',
                'ind_po_address1' => 'Street Address 1',
                'ind_po_address2' => 'Street Address 2',
                'ind_po_box' => 'PO Box #',
                'ind_po_city' => 'City',
                'ind_po_st_prov_reg' => 'State/Providence/Region',
                'ind_po_zip' => 'Postal Code',
                'ind_po_country' => 'Country',
            ];
            break;

    // co: Contact ('phone', 'email', 'email_pvt', 'website')
            case 'co':
            return [
                'phone' => 'Phone',
                'email' => 'Email',
                'email_pvt' => '',
                'website' => 'Website',
            ];
            break;

    // co-fe: Contact ('phone', 'email', 'email_pvt', 'website')
            case 'co':
            return [
                'phone' => 'Phone',
                'email' => 'Email',
                'email_pvt' => '',
                'website' => 'Website',
            ];
            break;

    // sf-church: Staff Church ('ind_first_name', 'ind_last_name', 'spouse_first_name', 'pastor_interim', 'cp_pastor')
            case 'sf-church':
            return [
                'ind_first_name' => 'First Name',
                'ind_last_name' => 'Last Name',
                'spouse_first_name' => 'Spouse First Name',
                'pastor_interim' => 'Interim Pastor',
                'cp_pastor' => 'Church-planting Pastor',
            ];
            break;

    // hc: Home Church ('home_church')
            case 'hc':
            return [
                'home_church' => $this->churchLabel,
                'map' => 'Show a Google map of this ministry on my profile',
            ];
            break;

    // pm-required: Parent Ministry for Staff ('ministry_of', 'selectM', 'titleM', 'map')
            case 'pm-required':
            return [
                'ministry_of' => $this->parentMinistryLabel,
                'selectM' =>  'Ministry',
                'titleM' => 'Title',
                'map' => 'Show a Google map of this ministry on my profile',
            ];
            break;

    // pm-ind: Other Ministries for Individuals ('ministry_of', 'selectM', 'titleM', 'map')
            case 'pm-ind':
            return [
                'ministry_of' => $this->parentMinistryLabel,
                'selectM' =>  'Ministry',
                'titleM' => 'Title',
                'map' => 'Show a Google map of this ministry on my profile',
            ];
            break;

    // pm: Parent Ministry ('ministry_of', 'map')
            case 'pm-org':
            return [
                'ministry_of' => $this->parentMinistryLabel,
                'map' => 'Show a Google map of this ministry on my profile',
            ];
            break;

    // pg: Programs ('select')
            case 'pg':
            return ['select' => 'Church Program'];
            break;

    // sa: Schools Attended ('select')
            case 'sa':
            return ['select' => 'School(s) Attended'];
            break;

    // sl: School Levels ('select')
            case 'sl':
            return ['select' => 'School Levels Offered'];
            break;

    // ma-church: Mission Agencies - Church ('select', 'housingSelect', 'packet')
            case 'ma-church':
            return ['housingSelect' => 'Does the church have mission housing or motorhome/trailer parking?'];
            break;

    // di: Distinctives ('bible', 'worship_style', 'polity')
            case 'di':
            return [
                'bible' => 'Bible',
                'worship_style' => 'Worship Style',
                'polity' => 'Church Government',
            ];
            break;

    // as-school: Associations - School ('select')
            case 'as-school':
            return ['select' => 'Accreditation or Association'];
            break;

    // as-ind: Associations - Individual ('select', 'name', 'acronym')
            case 'as-ind':
            return [
                'select' => 'Fellowship(s)',
                'name' => 'Or enter a new name here',
                'acronym' => 'Acronym',
            ];
            break;

    // as-church: Associations - Church ('flwship_id', 'name', 'acronym', 'ass_id', 'aName', 'aAcronym')
            case 'as-church':
            return [
                'select' => 'Fellowship(s)',
                'name' => 'Or a new name here',
                'acronym' => 'Acronym',
                'selectM' => 'Association(s)',
                'aName' => 'Or enter a new name here',
                'aAcronym' => 'Acronym',
            ];
            break;

    // Default labels
            default:
            break;
        }
    }

    /**
     * Create a new profile
     * @return Profile the loaded model
     */
    public function profileCreate()
    {
        $this->status = self::STATUS_NEW;
        $this->user_id = Yii::$app->user->identity->id;

        // Set Subtype
        if ($this->type == self::TYPE_PASTOR) {
            $this->sub_type = $this->ptype;
        } elseif ($this->type == self::TYPE_MISSIONARY) {
            $this->sub_type = $this->mtype;
        } elseif ($this->type == self::TYPE_CHAPLAIN) {
            $this->sub_type = $this->ctype;
        } else {
            $this->sub_type = $this->type;
        }

        $type = Type::findOne(['type' => $this->type]);
        $type->group == 'Individuals' ? 
            $this->category = self::CATEGORY_IND : 
            $this->category = self::CATEGORY_ORG;

        if ($this->validate() && $this->getIsNewRecord() && $this->save()) {
            ProfileMail::sendAdminNewProfile($this->id);
           
            return $this;
        }
        return false;
    }




/***************************************************************************************************
 ***************************************************************************************************
 *
 * The following functions process the incoming data from the profile data collection forms 
 *
 ***************************************************************************************************
\**************************************************************************************************/

    

    /**
     * handleFormND: Name & Description
     * 
     * @return mixed
     */
    public function handleFormND()
    {
    // ************************ Mission Agency**********************************
        if ($this->type == self::TYPE_MISSION_AGCY) {
            if ($this->select) {
                $mission = MissionAgcy::findOne($this->select);
                if ($mission && ($this->getOldAttribute('org_name') != $mission->mission)) {
                    $this->org_name = $mission->mission;
                    // Unlink old mission agcency                                                     
                    if ($oldA = MissionAgcy::find()->where(['profile_id' => $this->id])->one()) {
                        $oldA->unlink('linkedProfile', $this);
                    }
                    // link mission agency in mission agency table
                    $mission->link('linkedProfile', $this);
                }
            // Check for duplicate
            } elseif ($this->name && !MissionAgcy::find()
                ->where(['mission' => $this->name])->exists()) {
                $this->org_name = $this->name;
                // Add to mission agency table
                $mission = new MissionAgcy();
                $mission->mission = $this->name;
                $mission->mission_acronym = $this->acronym;
                $mission->profile_id = $this->id;
                $mission->validate();
                $mission->save();
            }
        }

    // *************************** Fellowship **********************************
        if ($this->type == Profile::TYPE_FELLOWSHIP) {  
            if ($this->select
                && ($fellowship = Fellowship::findOne($this->select))
                && ($this->getOldAttribute('org_name') != $fellowship->name)) {
                    $this->org_name = $fellowship->name;                                                      
                    // Unlink old fellowship
                    if ($oldA = $this->linkedFellowship) {
                        $oldA->unlink('linkedProfile', $this);
                    }
                    // link new fellowship
                    $fellowship->link('linkedProfile', $this);
            // Check for duplicate
            } elseif ($this->name
                && !Fellowship::find()->where(['name' => $this->name])->exists()) {
                $this->org_name = $this->name;
                // Add to fellowship table
                $fellowship = new Fellowship();
                $fellowship->name = $this->name;
                $fellowship->acronym = $this->acronym;
                $fellowship->profile_id = $this->id;
                $fellowship->validate();
                $fellowship->save();
            }
        }

        // ************************** Association ******************************
        if ($this->type == self::TYPE_ASSOCIATION) {
            if ($this->select) {
                $association = Association::findOne($this->select);
                if ($association && ($this->getOldAttribute('org_name') != $association->name)) {
                    $this->org_name = $association->name;
                    // Unlink old association                                                
                    if ($oldA = Association::find()->where(['profile_id' => $this->id])->one()) {
                        $oldA->unlink('linkedProfile', $this);
                    }
                    // link association in association table
                    $association->link('linkedProfile', $this);
                }
            // Check for duplicate
            } elseif ($this->name && !Association::find()
                ->where(['name' => $this->name])->exists()) {
                $this->org_name = $this->name;
                // Add to association table
                $association = new Association();
                $association->name = $this->name;
                $association->acronym = $this->acronym;
                $association->profile_id = $this->id;
                $association->validate();
                $association->save();
            }
        }

    // ***************************** School ************************************
        if ($this->type == self::TYPE_SCHOOL) {
            if ($this->select) {
                $name = explode('(', $this->select, 2);
                $school = School::find()->where(['school' => $name[0]])->one();
                if ($school && ($this->getOldAttribute('org_name') != $name[0])) {
                    $this->org_name = $school->school;
                    $this->org_city = $school->city;
                    $this->org_st_prov_reg = $school->st_prov_reg;
                    $this->org_country = $school->country;
                    
                    // Unlink old school
                    if ($oldSchool = School::find()->where(['profile_id' => $this->id])->one()) {
                        $oldSchool->unlink('linkedProfile', $this);
                    }
                    // link school in school table
                    $school->link('linkedProfile', $this);                                       
                }
            } elseif ($this->name) {
                $this->org_name = $this->name;
            }
        }

        // Update url name
        $this->category == self::CATEGORY_IND ?
            $this->url_name = Inflector::slug($this->ind_last_name) :
            $this->url_name = Inflector::slug($this->org_name);

    // ***************************** Save **************************************
        if ($this->validate() && $this->save() && $this->setUpdateDate()) {
            return $this;
        }
        return False;
    }

    /**
     * handleFormLO: Location
     *
     * @return mixed
     */
    public function handleFormLO()
    { 
        if ($this->validate()) {

    // ************************* Individual Address ******************************
            if ($this->category == self::CATEGORY_IND) {

                // if physical address is empty, populate city, state, country, and zip from mailing address
                if (empty($this->ind_city)) {
                    $this->ind_city = $this->ind_po_city;
                    $this->ind_st_prov_reg = $this->ind_po_st_prov_reg;
                    $this->ind_zip = $this->ind_po_zip;
                    $this->ind_country = $this->ind_po_country;
                }
                $oldAddr = $this->getOldAttribute('ind_address1') 
                    . $this->getOldAttribute('ind_address2')
                    . $this->getOldAttribute('ind_city')
                    . $this->getOldAttribute('ind_st_prov_reg')
                    . $this->getOldAttribute('ind_country');
                $addr = $this->ind_address1
                    . $this->ind_address2
                    . $this->ind_city
                    . $this->ind_st_prov_reg
                    . $this->ind_country;
                if ($this->ind_loc == NULL || ($addr != $oldAddr)) {
                    // Format address string for geocoding (123+Main+St,+Mullingar,+Westmeath,+Ireland)
                    $this->ind_address1 ? $address = $this->ind_address1 . ',+' : $address = '';
                    $address .= $this->ind_city . ',+';
                    $address .= $this->ind_st_prov_reg . ',+';
                    $address .= $this->ind_country;
                    $this->ind_loc = GeoCoder::getCoordinates($address, Yii::$app->params['apiKey.Google-server']);

                }
                // Convert US states to abbreviations
                if ($this->ind_country == 'United States') {
                    $state = State::find()
                        ->where(['state' => $this->ind_st_prov_reg])
                        ->one();
                    $this->ind_st_prov_reg = $state->abbreviation;
                    $this->ind_state_long = $state->long;
                }
                if ($this->ind_po_country == 'United States') {
                    $po_state = State::find()
                        ->where(['state' => $this->ind_po_st_prov_reg])
                        ->one();
                    $this->ind_po_st_prov_reg = $po_state->abbreviation;
                    $this->ind_po_state_long = $po_state->long;
                }

    // ************************** Organization Address *****************************
            } else {

                // if physical address is empty, populate city, state, country, and zip from mailing address
                if (empty($this->org_city)) {
                    $this->org_city = $this->org_po_city;
                    $this->org_st_prov_reg = $this->org_po_st_prov_reg;
                    $this->org_zip = $this->org_po_zip;
                    $this->org_country = $this->org_po_country;
                }

                $oldAddr = $this->getOldAttribute('org_address1') 
                    . $this->getOldAttribute('org_address2')
                    . $this->getOldAttribute('org_city')
                    . $this->getOldAttribute('org_st_prov_reg')
                    . $this->getOldAttribute('org_country');
                $addr = $this->org_address1
                    . $this->org_address2
                    . $this->org_city
                    . $this->org_st_prov_reg
                    . $this->org_country;
                if ($this->org_loc == NULL || ($addr != $oldAddr)) {
                    // Format address string for geocoding (123+Main+St,+Mullingar,+Westmeath,+Ireland)
                    $this->org_address1 ? $address = $this->org_address1 . ',+' : $address = '';
                    $address .= $this->org_city . ',+';
                    $address .= $this->org_st_prov_reg . ',+';
                    $address .= $this->org_country;
                    $this->org_loc = GeoCoder::getCoordinates($address, Yii::$app->params['apiKey.Google-server']);
                }
                // Convert US states to abbreviations
                if ($this->org_country == 'United States') {
                    $state = State::find()
                        ->where(['state' => $this->org_st_prov_reg])
                        ->one();
                    $this->org_st_prov_reg = $state->abbreviation;
                    $this->org_state_long = $state->long;
                }
                if ($this->org_po_country == 'United States') {
                    $po_state = State::find()
                        ->where(['state' => $this->org_po_st_prov_reg])
                        ->one();
                    $this->org_po_st_prov_reg = $po_state->abbreviation;
                    $this->org_po_state_long = $po_state->long;
                }
            }
            $this->updateMap(self::MAP_PRIMARY);
            if ($this->type != self::TYPE_MISSIONARY) {
                // Update Url location
                $this->url_loc = ($this->category == self::CATEGORY_IND) ?
                    Inflector::slug($this->ind_city) :
                    Inflector::slug($this->org_city);
            }

    // ***************************** Save **************************************
            if ($this->save() && $this->setUpdateDate()) {
                return $this;
            }
        }
        return false;
    }

    /**
     * handleFormCO: Contact
     * 
     * @return mixed
     */
    public function handleFormCO($social)
    {
        if ($this->validate() && $this->save() && $social->validate() && $social->save()) {
            $this->setUpdateDate();
            if (!$this->social) {
                $social->link('profile', $this);
            }
            return $this;
        }
        return false;
    }

    /**
     * handleFormSFSA: Staff Senior Pastor Add
     * 
     * @return mixed
     */
    public function handleFormSFSA()
    {
        $ids = explode('+', $_POST['senior']);
        $pastor = $this->findProfile($ids[0]);
        $staff = Staff::findOne($ids[1]);
        if ($pastor && $staff) {
            $this->updateAttributes([
                'ind_first_name' => $pastor->ind_first_name,
                'ind_last_name' => $pastor->ind_last_name,
                'spouse_first_name' => $pastor->spouse_first_name,
            ]);
            $staff->updateAttributes(['sr_pastor' => 1, 'confirmed' => 1]);

            $pastorProfileOwner = User::findOne($pastor->user_id);
            // Notify staff profile owner of unconfirmed status
            ProfileMailController::initSendLink($this, $pastor, $pastorProfileOwner, 'SFSA', 'L');

            return $this;
        }        
        return false;
    }

    /**
     * handleFormSFSR: Staff Senior Pastor Remove
     * 
     * @return mixed
     */
    public function handleFormSFSR()
    {
        if (!$staff = Staff::find()
            ->where(['staff_id' => $_POST['clear']])
            ->andWhere(['ministry_id' => $this->id])
            ->andWhere(['sr_pastor' => 1])
            ->one()) {
            return false;
        }
        $staff->updateAttributes([
            'confirmed' => NULL, 
            'sr_pastor' => NULL]);

        // remove all reference to pastor for the church profile
        $this->updateAttributes([
            'ind_first_name' => NULL,                                       
            'spouse_first_name' => NULL,
            'ind_last_name' => NULL,
            'cp_pastor' => NULL,
            'pastor_interim' => NULL,
        ]);
        
        $pastor = $this->findProfile($staff->staff_id);
        $pastorProfileOwner = User::findOne($pastor->user_id);
        ProfileMailController::initSendLink($this, $pastor, $pastorProfileOwner, 'SFSA', 'UL');     // Notify staff profile owner of unconfirmed status

        return $this;
    }

    /**
     * handleFormHC: Home Church
     * 
     * @return mixed
     */
    public function handleFormHC()
    {
        if (!$staff = $this->staffHC) {
            $staff = new Staff();
            $staff->save();
        }
        $staff->updateAttributes([
            'staff_id' => $this->id, 
            'staff_type' => $this->type,
            'staff_title' => $this->sub_type,
            'ministry_id' => $this->home_church,
            'home_church' => 1
        ]);
        // Only add pastors to staff table on home church form; other staff will be added on staff form
        if ($this->type == self::TYPE_PASTOR) { 
            $staff->updateAttributes(['church_pastor' => 1]); 
        }

        // Notify church profile owners of link changes
        $oldHc = $this->getOldAttribute('home_church');
        $hcProfile = $this->homeChurch;
        $hcProfileOwner = $hcProfile->user;
        if ($oldHc === NULL) {
            ProfileMailController::initSendLink($this, $hcProfile, $hcProfileOwner, 'PM', 'L');

        // Changed home church
        } elseif ($oldHc != $this->home_church) {
            if ($oldStaff = Staff::find()
                ->where(['staff_id' => $this->id])
                ->andWhere(['ministry_id' => $oldHc])
                ->andWhere(['staff_title' => $this->sub_type])
                ->andWhere(['home_church' => 1])
                ->one()) {
                $oldStaff->delete();
            }
            $oldHcProfile = $this->findProfile($oldHc);
            $oldHcProfileOwner = $oldHcProfile->user;
            ProfileMailController::initSendLink($this, $oldHcProfile, $oldHcProfileOwner, 'PM', 'UL');
            ProfileMailController::initSendLink($this, $hcProfile, $hcProfileOwner, 'PM', 'L');
        }

        $this->updateMap(self::MAP_CHURCH);
        if ($this->save() && $this->setUpdateDate()) {

            // Update role to SafeUser
            if ($this->category == self::CATEGORY_IND 
                && array_keys(Yii::$app->authManager->getRolesByUser($this->user_id))[0] == User::ROLE_USER) {
                // Revoke current User role
                $auth = Yii::$app->authManager;
                $item = $auth->getRole(User::ROLE_USER);
                $auth->revoke($item, $this->user_id);  
                // Set user role to SafeUser         
                $auth = Yii::$app->authManager;
                $userRole = $auth->getRole(User::ROLE_SAFEUSER);
                $auth->assign($userRole, $this->user_id);
            }

            return $this;
        }
        return false;
    }

    /**
     * handleFormPM: Parent Ministry
     * 
     * @return mixed
     */
    public function handleFormPM()
    {
        if ($this->ministry_of != NULL) {
            // If individual, Update staff table regardless of new or existing connection
            if ($this->category == self::CATEGORY_IND) {
                $this->aName = ($this->type == self::TYPE_STAFF) ? $this->title : $this->sub_type;
                // Add to staff table if not already there

                if (!$staff = $this->staffPM) {
                    $staff = new Staff();
                    $staff->save();
                }
                $staff->updateAttributes([
                    'staff_id' => $this->id, 
                    'staff_type' => $this->type,
                    'staff_title' => $this->aName,
                    'ministry_id' => $this->ministry_of,
                    'ministry_of' => 1]);
            }
            // Notify profile owners of link changes
            $oldMin = $this->getOldAttribute('ministry_of');
            $minProfile = $this->parentMinistry;
            $minProfileOwner = $minProfile->user;
            if ($oldMin == NULL) {
                ProfileMailController::initSendLink($this, $minProfile, $minProfileOwner, 'PM', 'L');
            // Changed parent ministry
            } elseif ($oldMin != $this->ministry_of) {
                if (($this->category == self::CATEGORY_IND)
                    && ($oldStaff = Staff::find()
                        ->where(['staff_id' => $this->id])
                        ->andWhere(['ministry_id' => $oldMin])
                        ->andWhere(['staff_title' => $this->aName])
                        ->one())) {
                    $oldStaff->delete();
                }
                $oldMinProfile = $this->findProfile($oldMin);
                $oldMinProfileOwner = $oldMinProfile->user;
                ProfileMailController::initSendLink($this, $oldMinProfile, $oldMinProfileOwner, 'PM', 'UL');
                ProfileMailController::initSendLink($this, $minProfile, $minProfileOwner, 'PM', 'L');
            }
        }
        $this->updateMap(self::MAP_MINISTRY);
        if ($this->save() && $this->setUpdateDate()) {
            return $this;
        }
        return false;
    }

    /**
     * handleFormPMM: Other Ministries
     * 
     * @return mixed
     */
    public function handleFormPMM()
    {
        if ($this->selectM != NULL) {
            if (!$staff = Staff::find()
                ->where(['staff_id' => $this->id])
                ->andWhere(['staff_type' => $this->type]) // Allow for multiple staff roles at same church
                ->andWhere(['staff_title' => $this->titleM])
                ->andWhere(['ministry_id' => $this->selectM])
                ->andWhere(['ministry_other' => 1])
                ->one()) {
                $staff = new Staff();
                $staff->save();
            }

            // Send mail to notify ministry profile owner of new link
            if ($staff->ministry_id != $this->selectM) {
                $ministryProfile = self::findProfile($this->selectM);
                $ministryProfileOwner = User::findOne($ministryProfile->user_id);
                ProfileMailController::initSendLink($this, $ministryProfile, $ministryProfileOwner, 'PM', 'L');   
            }
                
            $staff->updateAttributes([
                'staff_id' => $this->id, 
                'staff_type' => $this->type,
                'staff_title' => $this->titleM,
                'ministry_id' => $this->selectM,
                'ministry_other' => 1]);

        }
        return $this;
    }

    /**
     * handleFormPG: Programs
     * 
     * @return mixed
     */
    public function handleFormPG()
    {
        if (isset($_POST['remove'])) {
            $pgProfile = Profile::findOne($_POST['remove']);

            $pgProfileOwner = User::findOne($pgProfile->user_id);
            // Notify program profile owner of unlinked church
            ProfileMailController::initSendLink($this, $pgProfile, $pgProfileOwner, 'PG', 'UL');

            $this->unlink('programs', $pgProfile, $delete = true);
        
        } elseif ($this->select != NULL) {
            $pgProfile = $this->findOne($this->select);
            $linked = false;
            if ($pgs = $this->program) {
                // Check to see if program is already linked to profile
                foreach($pgs as $pg) {
                    if ($pg->id == $pgProfile->id) {
                        $linked = true;
                    }
                }
            }
            // Link program to file
            if (!$linked && $this->setUpdateDate()) {
                $this->link('programs', $pgProfile);
            }

            $pgProfileOwner = User::findOne($pgProfile->user_id);
            // Notify program profile owner of unlinked church
            ProfileMailController::initSendLink($this, $pgProfile, $pgProfileOwner, 'PG', 'L');

        }
        return $this;
    }

    /**
     * handleFormSA: Schools Attended
     * Process checkboxList of schools
     * @return mixed
     */
    public function handleFormSA()
    {
        if ($this->validate() && $this->setUpdateDate()) {
            $oldSelect = arrayHelper::map($this->schoolsAttended, 'id', 'id');
            // handle case of new selection
            if (!$oldSelect && ($select = $this->select) != NULL) {
                foreach ($select as $value) {
                    $sc = School::findOne($value);
                    // Link new schools
                    $this->link('schoolsAttended', $sc);

                    $scProfile = $sc->linkedProfile;
                    if ($scProfile && ($scProfileOwner = $scProfile->user)) {
                        // notify school profile owner of link
                        ProfileMailController::initSendLink($this, $scProfile, $scProfileOwner, 'SA', 'L');
                    }
                }
            }
            // handle case of all unselected
            if (!empty($oldSelect) && empty($this->select))  {
                $schoolArray = $this->schoolsAttended;
                foreach($schoolArray as $sc) {
                    
                    $scProfile = $sc->linkedProfile;
                    if ($scProfile && ($scProfileOwner = $scProfile->user)) {
                        // notify school profile owner of unlink
                        ProfileMailController::initSendLink($this, $scProfile, $scProfileOwner, 'SA', 'UL');
                    }

                    // unlink all schools
                    $sc->unlink('profiles', $this, $delete = true);

                }
            }
            // handle all other cases of change in selection
            if (!empty($oldSelect) && ($select = $this->select) != NULL) {
                // link any new selections
                foreach($select as $value) {
                    if(!in_array($value, $oldSelect)) {
                        $sc = School::findOne($value);
                        $this->link('schoolsAttended', $sc);

                        $scProfile = $sc->linkedProfile;
                        if ($scProfile && ($scProfileOwner = $scProfile->user)) {
                            // notify school profile owner of link
                            ProfileMailController::initSendLink($this, $scProfile, $scProfileOwner, 'SA', 'L');
                        }

                    }
                }
                // unlink any selections that were removed
                foreach($oldSelect as $value) {
                    if(!in_array($value, $select)) {
                        $sc = School::findOne($value);

                        $scProfile = $sc->linkedProfile;
                        if ($scProfile && ($scProfileOwner = $scProfile->user)) {
                            ProfileMailController::initSendLink($this, $scProfile, $scProfileOwner, 'SA', 'UL');  // Notify school profile owner of unlink
                        }

                        $this->unlink('schools', $sc, $delete = true);
                
                    }
                }
            }
            return $this;
        }
        return false;
    }

    /**
     * handleFormSL: School Levels
     * Process checkboxList of school_levels
     * @return mixed
     */
    public function handleFormSL()
    {
        if ($this->validate() && $this->setUpdateDate()) {
            $oldSelect = arrayHelper::map($this->schoolLevels, 'id', 'id');
            // handle case of new selection
            if (empty($oldSelect) && ($select = $this->select) != NULL) {
                foreach ($select as $value) {
                    $s = SchoolLevel::findOne($value);
                    $this->link('schoolLevels', $s);
                }
            }
            // handle case of all unselected
            if ($oldSelect && !$this->select)  {
                $s = $this->schoolLevels;
                foreach($s as $model) {
                    $model->unlink('profiles', $this, $delete = TRUE);
                }
            }
            // handle all other cases of change in selection
            if ($oldSelect && ($select = $this->select) != NULL) {
                // link any new selections
                foreach($select as $value) {
                    if(!in_array($value, $oldSelect)) {
                        $s = SchoolLevel::findOne($value);
                        $this->link('schoolLevels', $s);
                    }
                }
                // unlink any selections that were removed
                foreach($oldSelect as $value) {
                    if(!in_array($value, $select)) {
                        $s = SchoolLevel::findOne($value);
                        $this->unlink('schoolLevels', $s, $delete = TRUE);
                    }
                }
            }
            return $this;
        }
        return false;
    }

    /**
     * handleFormMA: Missions Agencies
     * Process selection of mission agencies
     * @return mixed
     */
    public function handleFormMA()
    {
    // *********************** Missions Packet *********************************
        // Create subfolders on server and store uploaded pdf
        if ($uploadPacket = UploadedFile::getInstance($this, 'packet')) {
            $fileName = md5(microtime() . $uploadPacket->name);
            $fileExt = strrchr($uploadPacket->name, '.');
            $fileDir = substr($fileName, 0, 2);
            
            $fileBasePath = Yii::getAlias('@webroot') . Yii::getAlias('@packet');
            if (!is_dir($fileBasePath)) {
                mkdir($fileBasePath, 0755, true);
            }
            $relativePath = Yii::getAlias('@packet') . DIRECTORY_SEPARATOR . $fileDir;
            $filePath = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $relativePath;
            if (!is_dir($filePath)) {
                mkdir($filePath, 0755, true);
            }
            $uploadPacket->saveAs($filePath . DIRECTORY_SEPARATOR . $fileName . $fileExt);
            $this->packet =  $filePath . DIRECTORY_SEPARATOR . $fileName . $fileExt;
        } else {
            $this->packet = $this->getOldAttribute('packet');
        }

    // **************************** Church *************************************
        $oldSelect = arrayHelper::map($this->missionAgcys, 'id', 'id');
        // handle case of new selection
        if (!$oldSelect && ($select = $this->select)) {
            foreach ($select as $value) {
                $a = MissionAgcy::findOne($value);
                    $this->link('missionAgcys', $a);
            }
        }
        // handle case of all unselected
        if ($oldSelect && !$this->select)  {
            $a = $this->missionAgcys;
            foreach($a as $model) {
                $model->unlink('profiles', $this, $delete = TRUE);
            }
        }
        // handle all other cases of change in selection
        if ($oldSelect && ($select = $this->select)) {
            // link any new selections
            foreach($select as $value) {
                if(!in_array($value, $oldSelect)) {
                    $a = MissionAgcy::findOne($value);
                    $this->link('missionAgcys', $a);
                }
            }
            // unlink any selections that were removed
            foreach($oldSelect as $value) {
                if(!in_array($value, $select)) {
                    $a = MissionAgcy::findOne($value);
                    $this->unlink('missionAgcys', $a, $delete = true);
                }
            }
        }

    // *********************** Missions Housing ********************************
        // Handle case of deleting mission housing
        if (($housing = $this->missHousing) && $this->housingSelect == 'N') { 
            $this->unlink('missHousing', $housing);
            $housing->delete();                     
        }

    // ***************************** Save **************************************
        // Save Profile instance
        if ($this->validate() && $this->save() && $this->setUpdateDate()) {            
            return $this;
        }
        return false;
    }

    /**
     * handleFormAS: Associations
     * 
     * @return mixed
     */
    public function handleFormAS()
    {
    // *************************** School **************************************
        if($this->type == 'School') {

            $oldSelect = arrayHelper::map($this->accreditations, 'id', 'id');
            // handle case of new selection
            if (!$oldSelect && ($select = $this->select) != NULL) {
                foreach ($select as $value) {
                    $a = Accreditation::findOne($value);
                        $this->link('accreditations', $a);
                }
            }
            // handle case of all unselected
            if ($oldSelect && !$this->select)  {
                $a = $this->accreditations;
                foreach($a as $model) {
                    $model->unlink('profiles', $this, $delete = TRUE);
                }
            }
            // handle all other cases of change in selection
            if ($oldSelect && ($select = $this->select) != NULL) {
                // link any new selections
                foreach($select as $value) {
                    if(!in_array($value, $oldSelect)) {
                        $a = Accreditation::findOne($value);
                        $this->link('accreditations', $a);
                    }
                }
                 // unlink any selections that were removed
                foreach($oldSelect as $value) {
                    if(!in_array($value, $select)) {
                        $a = Accreditation::findOne($value);
                        $this->unlink('accreditations', $a, $delete = TRUE);
                    }
                }
            }
            // No need to save $profile model
            return $this;
        }

    // ************************** Fellowship ***********************************
        
        $oldSelect = arrayHelper::map($this->fellowships, 'id', 'id');
        // handle case of new selection
        if (!$oldSelect && ($select = $this->select) != NULL) {
            foreach ($select as $value) {
                $f = Fellowship::findOne($value);
                $this->link('fellowships', $f);

                // notify new fellowship profile owner of new link
                if ($fProfile = $f->linkedProfile) {
                    $fProfileOwner = User::findOne($fProfile->user_id);
                    ProfileMailController::initSendLink($this, $fProfile, $fProfileOwner, 'AS', 'L');      
                }

            }
        }
        // handle case of all unselected
        if ($oldSelect && !$this->select)  {
            $f = $this->fellowships;
            foreach($f as $model) {
                $model->unlink('profiles', $this, $delete = TRUE);

                // notify old fellowship profile owner of unlink
                if ($fProfile = $f->linkedProfile) {
                    $fProfileOwner = User::findOne($fProfile->user_id);
                    ProfileMailController::initSendLink($this, $fProfile, $fProfileOwner, 'AS', 'UL');      
                }
            }
        }
        // handle all other cases of change in selection
        if ($oldSelect && ($select = $this->select) != NULL) {
               // link any new selections
            foreach($select as $value) {
                if(!in_array($value, $oldSelect)) {
                    $f = Fellowship::findOne($value);
                    $this->link('fellowships', $f);

                    // notify new fellowship profile owner of new link
                    if ($fProfile = $f->linkedProfile) {
                        $fProfileOwner = User::findOne($fProfile->user_id);
                        ProfileMailController::initSendLink($this, $fProfile, $fProfileOwner, 'AS', 'L');      
                    }
                }
            }
            // unlink any selections that were removed
            foreach($oldSelect as $value) {
                if(!in_array($value, $select)) {
                    $f = Fellowship::findOne($value);
                    $this->unlink('fellowships', $f, $delete = TRUE);

                    // notify old fellowship profile owner of unlink
                    if ($fProfile = $f->linkedProfile) {
                        $fProfileOwner = User::findOne($fProfile->user_id);
                        ProfileMailController::initSendLink($this, $fProfile, $fProfileOwner, 'AS', 'UL');      
                    }
                }
            }
        }

        // Give preference to text input if both fellowshipSelect and fellowshipName are populated (better to 
        // collect more data, can delete duplicate entries if need be; and mitigates accidental selection)
        if ($this->name != NULL) {
            if ($this->validate()) {
                $newF = new Fellowship();
                $newF->name = $this->name;
                $newF->acronym = $this->acronym;
                if ($newF->save()) {
                    $this->link('fellowships', $newF);
                }
            } else {
                return false;
            }                                                          
        }

    // ************************* Association ***********************************
        $oldSelectM = arrayHelper::map($this->associations, 'id', 'id');
        // handle case of new selection
        if (!$oldSelectM && ($selectM = $this->selectM) != NULL) {
            foreach ($selectM as $value) {
                $a = Association::findOne($value);
                $this->link('associations', $a);

                // notify new association profile owner of new link
                if ($aProfile = $a->linkedProfile) {
                    $aProfileOwner = User::findOne($aProfile->user_id);
                    ProfileMailController::initSendLink($this, $aProfile, $aProfileOwner, 'AS', 'L');      
                }

            }
        }
        // handle case of all unselected
        if ($oldSelectM && !$this->selectM)  {
            $a = $this->associations;
            foreach($a as $model) {
                $model->unlink('profiles', $this, $delete = TRUE);

                // notify old association profile owner of unlink
                if ($aProfile = $a->linkedProfile) {
                    $aProfileOwner = User::findOne($aProfile->user_id);
                    ProfileMailController::initSendLink($this, $aProfile, $aProfileOwner, 'AS', 'UL');      
                }
            }
        }
        // handle all other cases of change in selection
        if ($oldSelectM && ($selectM = $this->selectM) != NULL) {
            // link any new selections
            foreach($selectM as $value) {
                if(!in_array($value, $oldSelectM)) {
                    $a = Association::findOne($value);
                    $this->link('associations', $a);
                    
                    // notify new association profile owner of new link
                    if ($aProfile =$a->linkedProfile) {
                        $aProfileOwner = User::findOne($aProfile->user_id);
                        ProfileMailController::initSendLink($this, $aProfile, $aProfileOwner, 'AS', 'L');      
                    }
                }
            }
            // unlink any selections that were removed
            foreach($oldSelectM as $value) {
                if(!in_array($value, $selectM)) {
                    $a = Association::findOne($value);
                    $this->unlink('associations', $a, $delete = TRUE);

                    // notify old association profile owner of unlink
                    if ($aProfile = $a->linkedProfile) {
                        $aProfileOwner = User::findOne($aProfile->user_id);
                        ProfileMailController::initSendLink($this, $aProfile, $aProfileOwner, 'AS', 'UL');      
                    }
                }
            }
        }

        // Give preference to text input if both associationSelect and associationName are populated (better 
        // to collect more data, can delete duplicate entries if need be; and mitigates accidental selection)
        if ($this->aName != NULL) {
            if ($this->validate()) {
                $newA = new Association();
                $newA->name = $this->aName;
                $newA->acronym = $this->aAcronym;
                if ($newA->save()) {
                    $this->link('associations', $newA);
                }
            } else {
                return false;
            }                                                  
        }

    // ***************************** Save **************************************
        if ($this->save(false) && $this->setUpdateDate()) {
            return $this;
        }
        return False;
    }

    /**
     * handleFormTA: Tag
     * Assign tag(s) to special ministry profile
     * @return mixed
     */
    public function handleFormTA()
    {

        $oldSelect = arrayHelper::map($this->tags, 'id', 'id');
        // handle case of new selection
        if (empty($oldSelect) && !empty($select = $this->select)) {
            foreach ($select as $value) {
                $t = Tag::findOne($value);
                    $this->link('tags', $t);
            }
        }
        // handle case of all unselected
        if (!empty($oldSelect) && empty($this->select))  {
            $t = $this->tags;
            foreach($t as $model) {
                $model->unlink('profiles', $this, $delete = TRUE);
            }
        }
        // handle all other cases of change in selection
        if ($oldSelect && ($select = $this->select)) {
            // link any new selections
            foreach($select as $value) {
                if(!in_array($value, $oldSelect)) {
                    $t = Tag::findOne($value);
                    $this->link('tags', $t);
                }
            }
            // unlink any selections that were removed
            foreach($oldSelect as $value) {
                if(!in_array($value, $select)) {
                    $t = Tag::findOne($value);
                    $this->unlink('tags', $t, $delete = TRUE);
                }
            }
        }

    // ***************************** Save **************************************

        if ($this->validate() && $this->save() && $this->setUpdateDate()) {            
            return $this;
        }
        return False;
    }




/***************************************************************************************************
 ***************************************************************************************************
 *
 * End of data collection forms 
 *
 ***************************************************************************************************
\**************************************************************************************************/

    







    /**
     * Generate new profile transfer token
     * @param int $userId
     * @return string transfer token
     */
    public function generateProfileTransferToken($userId)
    {
        return $userId . '+' . Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Check new profile transfer token
     * @param string $token
     * @return boolean
     */
    public function checkProfileTransferToken($token)
    {
        if ($token == $this->transfer_token) {
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['tokenExpire.profileTransfer'];
            return $timestamp + $expire >= time();
        }         
        return false;                                      
    }

    /**
     * Set profile status to "Active" 
     * Update created_at, last_update, and renewal_date fields
     * @return mixed
     */
    public function activate()
    {
        if ($this->category == self::CATEGORY_IND) {

            $name = Inflector::slug($this->ind_last_name);
            $urlLoc = ($this->type == self::TYPE_MISSIONARY) ?
                Inflector::slug($this->missionary->field) :
                Inflector::slug($this->ind_city);

        } else {
            $name = Inflector::slug($this->org_name);
            $urlLoc = Inflector::slug($this->org_city);
        }

        // Send link notifications to profile owners
        ProfileMailController::dbSendLink($this->id);
        // Notify admin of activation
        if ($this->status == Self::STATUS_NEW) {
            ProfileMail::sendAdminActiveProfile($this->id);
        }

        if ($this->type == self::TYPE_MISSIONARY) {
            $missionary = $this->missionary;
            $missionary->generateRepositoryKey();
            // Set to active any mailchimp updates that were generated while the profile was inactive
            $missionary->setUpdatesActive();
        }

        // Enter first timeline event as "Joined IBNet"
        $events = $this->history;
        $e = false;
        foreach ($events as $event) {
            if ($event->title = 'Joined IBNet') {
                $e = true;
            }
        }
        if ($e == false) {
            $history = new History;
            $history->profile_id = $this->id;
            $history->date = time();
            $history->title = 'Joined IBNet';
            $history->save();
        }

        $createDate = new Expression('CURDATE()');

        // Set active
        $this->updateAttributes([
            'created_at' => $createDate, 
            'inactivation_date' => NULL,
            'status' => self::STATUS_ACTIVE,
            'url_name' => $name,
            'url_loc' => $urlLoc]); 
        $this->setUpdateDate();
            
        return $this;
    }

    /**
     * Set profile status to "Inactive" 
     * Update last_update and renewal_date fields
     * @return boolean
     */
    public function inactivate()
    {
        // Delete progress
        if ($progress = FormsCompleted::findOne($this->id)) {
            $progress->delete();
        }
        $this->setUpdateDate(); 
        $this->updateAttributes([
            'status' => Profile::STATUS_INACTIVE, 
            'renewal_date' => NULL,
            'inactivation_date' => new Expression('NOW()'),
            'has_been_inactivated' => 1,
            'edit' => self::EDIT_NO,
        ]);

        return true;
    }

    /**
     * Set profile status to "Trash"
     * Set last_update to current date and set renewal_date to NULL
     * For profiles that are Schools, Mission Agencies, or Fellowships/Associations, remove the link
     *     to each respective table to enable other profiles to claim those names
     * Leave all other links in tact in the event there is a need to restore the profile
     * @return
     */
    public function trash()
    {
    // *********************** Remove forms_completed***************************
        if ($fc = FormsCompleted::findOne($this->id)) {
            $fc->delete();
        }

        // Remove Link to Association/Fellowship
        if ($this->type == self::TYPE_ASSOCIATION) {
            if ($association = $this->linkedAssociation) {
                $association->updateAttributes(['profile_id' => NULL]);
            }
        }
        if ($this->type == self::TYPE_FELLOWSHIP)  {
            if ($fellowship = $this->linkedFellowship) {
                $fellowship->updateAttributes(['profile_id' => NULL]);   
            }         
        }

        // Remove Link to Mission Agency
        if ($this->type == self::TYPE_MISSION_AGCY) {         // Remove Mission Agency profile link to mission agency table
            if ($agency = $this->linkedMissionAgcy) {
                $agency->updateAttributes(['profile_id' => NULL]);
            }
        }

        // Remove Link to School
        if ($this->type == self::TYPE_SCHOOL) {               // Remove School profile link to school table
            if ($school = $this->linkedSchool) {
                $school->updateAttributes(['profile_id' => NULL]);
            }
        }

        // Set Status to "trash"
        $date = new Expression('CURDATE()');
        $this->updateAttributes([
            'status' => self::STATUS_TRASH,
            'last_update' => $date,
            'renewal_date' => NULL]);

        return true;
    }

    /**
     * Permanently delete profile along with links
     * @return
     */
    public function annihilate()
    {
        // Remove forms_completed
        if ($fc = FormsCompleted::findOne($this->id)) {
            $fc->delete();
        }

        // Remove Links to Staff 
            // Individuals (delete record)
        if ($staff = Staff::find()->where(['staff_id' => $this->id])->all()) {
            foreach ($staff as $sf) {
                $sf->delete();
            }
            // Organizations (remove ministry_id and retain record)
        } elseif ($staff = Staff::find()->where(['ministry_id' => $this->id])->all()) {
            foreach ($staff as $sf) {
                $sf->updateAttributes(['ministry_id' => NULL]);
            }
        }

        // Delete Service Time 
        if ($service = $this->serviceTime) {
            $service->delete();
        }

        // Delete Social
        if ($social = $this->social) {
            $social->delete();
        }

        // Delete Missions Housing
        if ($housing = $this->missHousing) {
            $housing->delete();
        }

        // Remove Link to Mission Agency
            // Remove church approved mission agencies
        if (($this->type == self::TYPE_CHURCH)
            && ($agencies = ProfileHasMissionAgcy::find()->where(['profile_id' => $this->id])->all())) {
            foreach ($agencies as $agency) {
                $agency->delete();
            }
        }

        // Remove Link to Schools Attended
        if ($schools = $this->schoolsAttended) {
            foreach ($schools as $school) {
                $this->unlink('schoolsAttended', $school);
            }
        }

        // Remove Link to School Levels
        if ($levels = $this->schoolLevels) {
            foreach ($levels as $level) {
                $this->unlink('schoolLevels', $level);
            }
        }

        // Delete Record
        return $this->delete() ? true : false;
    }

    /**
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return $profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function findProfile($id)
    {
        if ($profile = self::find()
            ->where(['id' => $id])
            ->andWhere(['<>', 'status', self::STATUS_TRASH])
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
            ->where(['id' => $id])
            ->andwhere(['status' => Profile::STATUS_ACTIVE])
            ->one()) {
            return $profile;
        }     
        return false;
    }

    /**
     * Finds an active Profile model based on id, location, and name.
     * @param string $id
     * @param string $urlLoc
     * @param string $name
     * @return Profile the loaded model
     */
    public static function findViewProfile($id, $urlLoc, $urlName)
    {
        return self::find()
            ->where(['id' => $id])
            ->andwhere(['url_loc' => $urlLoc])
            ->andwhere(['url_name' => $urlName])
            ->andwhere(['status' => Profile::STATUS_ACTIVE])
            ->one();
    }

    /**
     * Check if profile has expired within the last two years
     * @param string $id
     * @return boolean
     */
    public static function isExpired($id) 
    {
        $profile = self::findProfile($id);
        if ($profile->status != Profile::STATUS_NEW) {
            $cutoffDate = strtotime($profile->inactivation_date . '+2 year');
            if (date("m-d-Y", $cutoffDate) > date("m-d-Y")) {
                return true;
            }
        }
        throw new NotFoundHttpException; 
    }

    /**
     * Return a duplicate profile if exists
     * @return Profile the loaded model
     */
    public function getDuplicate() 
    {
        if ($this->category == self::CATEGORY_IND) {
            return self::find()
                ->where(['ind_first_name' => $this->ind_first_name])
                ->andWhere(['ind_last_name' => $this->ind_last_name])
                ->andWhere(['ind_city' => $this->ind_city])
                ->andWhere(['ind_st_prov_reg' => $this->ind_st_prov_reg])
                ->andWhere(['ind_country' => $this->ind_country])
                ->andWhere(['<>', 'id', $this->id])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->one();
        } else {
            return self::find()
                ->where(['org_name' => $this->org_name])
                ->andWhere(['org_city' => $this->org_city])
                ->andWhere(['org_st_prov_reg' => $this->org_st_prov_reg])
                ->andWhere(['org_country' => $this->org_country])
                ->andWhere(['<>', 'id', $this->id])
                ->andWhere(['status' => self::STATUS_ACTIVE])
                ->one();
        }
    }

    /**
     * Update $show_map to user selection
     * @var string $mapType
     * @return $this
     */
    public function updateMap($mapType)
    {
        if (($this->getOldAttribute('show_map') == $mapType) && empty($this->map)) {
            $this->show_map = NULL;
        } elseif (!empty($this->map)) {
            $this->show_map = $mapType;
        }
        return $this;
    }

    /**
     * User model for profile owner
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * First and last name
     * @return string
     */
    public function getFullName()
    {
        return $this->ind_first_name . ' ' . $this->ind_last_name;
    }

    /**
     * First and last name, include spouse if exists
     * @return string
     */
    public function getCoupleName()
    {
        return $this->spouse_first_name ? 
            $this->ind_first_name . ' & ' . $this->spouse_first_name . ' ' . $this->ind_last_name :
            $this->ind_first_name . ' ' . $this->ind_last_name;
    }

    /**
     * Fisrt and last name, spouse name in parentheses
     * @return string
     */
    public function getMainName()
    {
        return $this->spouse_first_name ? 
            $this->ind_first_name . ' (& ' . $this->spouse_first_name . ') ' . $this->ind_last_name :
            $this->ind_first_name . ' ' . $this->ind_last_name;
    }

    /**
     * CoupleName or mainName, depending on profile type for indvs, or org_name for orgs
     * 
     * @return string
     */
    public function getformatName()
    {
        if ($this->category == self::CATEGORY_IND) {
            return ($this->type == self::TYPE_MISSIONARY) ? $this->coupleName : $this->mainName;
        } else {
            return $this->org_name;
        }
    }

    /**
     * Active profile by id
     * @return \yii\db\ActiveRecord
     */
    public function getActiveProfile($id)
    {
        return static::find()->where(['id' => $id, 'status' => self::STATUS_ACTIVE])->one();
    }
 
    /**
     * Return singular or plural of individual subtypes
     * @return \yii\db\ActiveQuery
     */
    public function getPluralType()
    {
        switch ($this->type) {
            case self::TYPE_MISSIONARY:
                if ($this->sub_type == self::SUBTYPE_MISSIONARY_CP) {
                    return $this->spouse_first_name ? 'Missionaries ' : 'Missionary ';
                } elseif ($this->sub_type == self::SUBTYPE_MISSIONARY_BT) {
                    return $this->spouse_first_name ? 'Medical Missionaries ' : 'Medical Missionary ';
                } else {
                    return $this->spouse_first_name ? 'Bible Translators ' : 'Bible Translator ';
                }
                break;
            case self::TYPE_CHAPLAIN:
                return $this->sub_type == self::SUBTYPE_CHAPLAIN_M ? 'Military Chaplain ' : 'Jail Chaplain ';
                break;
            default: return $this->type;
            break;
        }
    }

    /**
     * Service times for a church profile
     * @return \yii\db\ActiveQuery
     */
    public function getSocial()
    {
        return $this->hasOne(Social::className(), ['profile_id' => 'id']);
    }

    /**
     * Check if at least one social link exists
     * @return model $social || false
     */
    public function getHasSocial()
    {
        if (($social = $this->social) 
            && !(empty($social->sermonaudio) 
            && empty($social->facebook) 
            && empty($social->linkedin)
            && empty($social->twitter) 
            && empty($social->rss)
            && empty($social->youtube) 
            && empty($social->vimeo) 
            && empty($social->pinterest) 
            && empty($social->tumblr) 
            && empty($social->soundcloud) 
            && empty($social->instagram) 
            && empty($social->flickr))
        ) {
            return $social;
        }
        return false;
    }

    /**
     * Tags linked to a special ministry
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->viaTable('profile_has_tag', ['profile_id' => 'id']);
    }

    /**
     * Parent ministry profile of an organization or individual (staff's place of ministry)
     * @return \yii\db\ActiveQuery
     */
    public function getParentMinistry()
    {
        return $this->hasOne(self::className(), ['id' => 'ministry_of']);
    }

    /**
     * Daughter ministry profiles
     * @return \yii\db\ActiveQuery
     */
    public function getMinistries()
    {
        return $this->hasMany(self::className(), ['ministry_of' => 'id'])
            ->andWhere(['!=', 'type', self::TYPE_STAFF])
            ->andWhere(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Profiles for secondary ministries related to individual profile
     * @return \yii\db\ActiveQuery Staff with their related ministry
     */
    public function getOtherMinistries()
    {
        return $this->hasMany(Staff::className(), ['staff_id' => 'id'])
            ->joinWith('ministry')
            ->where(['staff.ministry_other' => 1, 'profile.status' => Profile::STATUS_ACTIVE])
            ->orderBy('id Asc');
    }

    /**
     * Confirmed profiles for secondary ministries related to individual profile
     * @return \yii\db\ActiveQuery Staff with their related ministry
     */
    public function getOtherMinistriesConfirmed()
    {
        return $this->hasMany(Staff::className(), ['staff_id' => 'id'])
            ->joinWith('ministry')
            ->where(['staff.ministry_other' => 1, 'staff.confirmed' => 1, 'profile.status' => Profile::STATUS_ACTIVE])
            ->orderBy('id Asc');
    }

    /**
     * Programs linked to a church profile
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(self::className(), ['id' => 'program_id'])
            ->viaTable('profile_has_program', ['profile_id' => 'id'])
            ->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Churches listing this profile as a program
     * @return \yii\db\ActiveQuery
     */
    public function getProgramChurches()
    {
        return $this->hasMany(self::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_program', ['program_id' => 'id'])
            ->where(['type' => self::TYPE_CHURCH, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Missionaries sent by this church
     * @return \yii\db\ActiveQuery
     */
    public function getSentMissionaries()
    {
        return $this->hasMany(self::className(), ['home_church' => 'id'])
            ->joinWith('missionary')
            ->where(['profile.status' => self::STATUS_ACTIVE])
            ->andWhere('profile.type="' . self::TYPE_MISSIONARY . '" OR profile.type="' . self::TYPE_CHAPLAIN . '" OR profile.type="' . self::TYPE_EVANGELIST . '"');
    }

    /**
     * Service times for a church profile
     * @return \yii\db\ActiveQuery
     */
    public function getServiceTime()
    {
        return $this->hasOne(ServiceTime::className(), ['profile_id' => 'id']);
    }

    /**
     * Home church profile of an individual
     * @return \yii\db\ActiveQuery
     */
    public function getHomeChurch()
    {
        return $this->hasOne(self::className(), ['id' => 'home_church']);
    }

    /**
     * Church members related to a church profile
     * @return \yii\db\ActiveQuery
     */
    public function getChurchMembers()
    {
        return $this->hasMany(User::className(), ['home_church' => 'id'])
            ->where(['user.status' => User::STATUS_ACTIVE])
            ->andWhere(['!=', 'primary_role', User::PRIMARYROLE_PASTOR])
            ->andWhere(['!=', 'primary_role', User::PRIMARYROLE_SENIORPASTOR]);
    }

    /**
     * Fellow church members related to an individual/personal profile
     * @return \yii\db\ActiveQuery Church profile with related member user models
     */
    public function getFellowChurchMembers()
    {
        return $this->hasOne(self::className(), ['id' => 'home_church'])
            ->joinWith(['churchMembers' => function ($q) {
                    $q->onCondition(['!=', 'user.id', $this->user_id]);
                }]);
    }
    
    /**
     * Confirmed Sr Pastor profile related to a church profile
     * @return \yii\db\ActiveQuery
     */
    public function getSrPastorChurchConfirmed()
    {
        return $this->hasOne(self::className(), ['id' => 'staff_id'])
            ->via('staffSrPastorChurchConfirmed')
            ->where(['profile.status' => self::STATUS_ACTIVE]);
    }

    /**
     * Staff model of a confirmed senior pastor related to this church profile
     * @return \yii\db\ActiveQuery
     */
    public function getStaffSrPastorChurchConfirmed()
    {
        return $this->hasOne(Staff::className(), ['ministry_id' => 'id'])
            ->where(['staff.sr_pastor' => 1, 'staff.confirmed' => 1]);
    }


    /**
     * Confirmed Sr Pastor profile related to this individual profile
     * @return \yii\db\ActiveQuery
     */
    public function getSrPastorIndConfirmed()
    {
        return $this->hasOne(self::className(), ['id' => 'staff_id'])
            ->via('staffSrPastorIndConfirmed')
            ->where(['profile.status' => self::STATUS_ACTIVE]);
    }

    /**
     * Staff model of a confirmed senior pastor related to this individual profile
     * @return \yii\db\ActiveQuery
     */
    public function getStaffSrPastorIndConfirmed()
    {
        return $this->hasOne(Staff::className(), ['ministry_id' => 'home_church'])
            ->where(['staff.sr_pastor' => 1, 'staff.confirmed' => 1]);
    }


    /**
     * Mission agencies marked approved by a church
     * @return \yii\db\ActiveQuery
     */
    public function getMissionAgcys()
    {
        return $this->hasMany(MissionAgcy::className(), ['id' => 'mission_agcy_id'])
            ->viaTable('profile_has_mission_agcy', ['profile_id' => 'id']);
    }

    /**
     * Mission agency represented by (linked to) this profile
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedMissionAgcy()
    {
        return $this->hasOne(MissionAgcy::className(), ['profile_id' => 'id']);
    }

    /**
     * Missionary model linked to missionary profile
     * @return \yii\db\ActiveQuery
     */
    public function getMissionary()
    {
        return $this->hasOne(Missionary::className(), ['profile_id' => 'id']);
    }

    /**
     * Missionary housing linked to this church profile
     * @return \yii\db\ActiveQuery
     */
    public function getMissHousing()
    {
        return $this->hasOne(MissHousing::className(), ['profile_id' => 'id']);
    }

    /**
     * Schools attended by this profile owner
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolsAttended()
    {
        return $this->hasMany(School::className(), ['id' => 'school_id'])
            ->viaTable('profile_has_school', ['profile_id' => 'id']);
    }

    /**
     * School with linked alumni profiles
     * @return \yii\db\ActiveQuery
     */
    public function getAlumni()
    {
        return $this->hasOne(School::className(), ['profile_id' => 'id'])
            ->joinWith('profiles')
            ->where(['profile.status' => self::STATUS_ACTIVE]);
    }

    /**
     * School represented by this profile
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedSchool()
    {
        return $this->hasOne(School::className(), ['profile_id' => 'id']);
    }

    /**
     * School levels linked to this school profile
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolLevels()
    {
        return $this->hasMany(SchoolLevel::className(), ['id' => 'school_level_id'])
            ->viaTable('profile_has_school_level', ['profile_id' => 'id']);
    }

    /**
     * Fellowships of which this profile owner is a member
     * @return \yii\db\ActiveQuery Fellowship with linked profile
     */
    public function getFellowships()
    {
        return $this->hasMany(Fellowship::className(), ['id' => 'flwship_id'])
            ->viaTable('profile_has_fellowship', ['profile_id' => 'id'])
            ->joinWith('linkedProfile');
    }

    /**
     * Fellowship represented by this profile
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedFellowship()
    {
        return $this->hasOne(Fellowship::className(), ['profile_id' => 'id']);
    }

    /**
     * Members (orgs & indvs) of the fellowship represented by this profile
     * @return \yii\db\ActiveQuery Fellowship with related member profiles
     */
    public function getFellowshipMembers()
    {
        return $this->hasOne(Fellowship::className(), ['profile_id' => 'id'])
            ->joinWith('profiles');
    }

    /**
     * Associations of which this profile owner/organization is a member
     * @return \yii\db\ActiveQuery Association with linked profile
     */
    public function getAssociations()
    {
        return $this->hasMany(Association::className(), ['id' => 'ass_id'])
            ->viaTable('profile_has_association', ['profile_id' => 'id'])
            ->joinWith('linkedProfile');
    }

    /**
     * Association represented by this profile
     * @return \yii\db\ActiveQuery
     */
    public function getLinkedAssociation()
    {
        return $this->hasOne(Association::className(), ['profile_id' => 'id']);
    }

    /**
     * Member churches of the association represnted by this profile
     * @return \yii\db\ActiveQuery  Association with related member profiles
     */
    public function getAssociationMembers()
    {
        return $this->hasOne(Association::className(), ['profile_id' => 'id'])
            ->joinWith('profiles');
    }

    /**
     * History events related to this profile
     * @return \yii\db\ActiveQuery
     */
    public function getHistory()
    {
        return $this->hasMany(History::className(), ['profile_id' => 'id'])
            ->where(['deleted' => 0])
            ->orderBy('date ASC');
    }

    /**
     * Accreditations linked to this school profile
     * @return \yii\db\ActiveQuery
     */
    public function getAccreditations()
    {
        return $this->hasMany(Accreditation::className(), ['id' => 'accreditation_id'])
            ->viaTable('profile_has_accreditation', ['profile_id' => 'id']);
    }

    /**
     * Staff model of home church
     * @return \yii\db\ActiveQuery
     */
    public function getStaffHC()
    {
        return $this->hasOne(Staff::className(), ['staff_id' => 'id'])
            ->andWhere(['staff.ministry_id' => $this->home_church, 'home_church' => 1]);
    }

    /**
     * staff model of home church confirmed
     * @return \yii\db\ActiveQuery
     */
    public function getStaffHCConfirmed()
    {
        return $this->hasOne(Staff::className(), ['staff_id' => 'id'])
            ->andWhere(['staff.ministry_id' => $this->home_church, 'staff.confirmed' => 1, 'home_church' => 1]);
    }

    /**
     * Staff model of parent ministry
     * @return \yii\db\ActiveQuery
     */
    public function getStaffPM()
    {
        return $this->hasOne(Staff::className(), ['staff_id' => 'id'])
            ->andWhere(['staff.ministry_id' => $this->ministry_of, 'staff_title' => $this->aName]);
    }

    /**
     * Confirmed staff model of parent ministry
     * @return \yii\db\ActiveQuery
     */
    public function getStaffPMConfirmed()
    {
        return $this->hasOne(Staff::className(), ['staff_id' => 'id'])
            ->andWhere(['staff.ministry_id' => $this->ministry_of, 'staff.confirmed' => 1, 'staff_title' => $this->aName]);
    }

    /**
     * Staff models for an organization
     * Exclude church sr pastor as a connection since he is listed on the profile
     * @return \yii\db\ActiveQuery Staff models with their related profile
     */
    public function getOrgStaff()
    {
        return $this->hasMany(Staff::className(), ['ministry_id' => 'id'])
            ->joinWith('profile')
            ->where(['staff.sr_pastor' => NULL, 'staff.home_church' => NULL, 'profile.status' => Profile::STATUS_ACTIVE])
            ->andWhere(['IS NOT', 'staff.staff_title', NULL])
            ->orderBy('staff.staff_id Asc');
    }

    /**
     * All confirmed staff models for an organization
     * @return \yii\db\ActiveQuery Staff models with their related profile
     */
    public function getOrgStaffConfirmed()
    {
        return $this->hasMany(Staff::className(), ['ministry_id' => 'id'])
            ->joinWith('profile')
            ->where(['staff.sr_pastor' => NULL, 'staff.home_church' => NULL, 'staff.confirmed' => 1, 'profile.status' => Profile::STATUS_ACTIVE])
            ->andWhere(['IS NOT', 'staff.staff_title', NULL])
            ->orderBy('staff.staff_id Asc');
    }

    /**
     * Whether this profile is liked by the current user
     * @return \yii\db\ActiveQuery
     */
    public function getILike()
    {
        return $this->hasOne(ProfileHasLike::className(), ['profile_id' => 'id'])->where(['liked_by_id' => Yii::$app->user->identity->id]);
    }

    /**
     * User that have liked this profile
     * @return \yii\db\ActiveQuery
     */
    public function getLikes()
    {
        return $this->hasMany(User::className(), ['id' => 'liked_by_id'])
            ->viaTable('profile_has_like', ['profile_id' => 'id'])
            ->where(['status' => User::STATUS_ACTIVE]);
    }

    /**
     * Remove users from this connection if they already have other connections
     * @param array $users User models
     * @param array $uids User model ids
     * @return array $users
     */
    public function filterUsers($users, $uids=[])
    {
        if (is_array($uids)) {
            foreach ($users as $i => $u) {
                if (in_array($u->id, $uids)) {
                    unset($users[$i]);
                }
            }
        }
        return $users;
    }

    /**
     * Remove users extracted from profile from this connection if they already have other connections
     * @param array $profiles Profile models
     * @param array $uids User model ids
     * @return array $profiles
     */
    public function filterUsersByProfile($profiles, $uids=[])
    {
        if (is_array($uids)) {
            foreach ($profiles as $i => $p) {
                if (in_array($p->user_id, $uids)) {
                    unset($profiles[$i]);
                }
            }
        }
        return $profiles;
    }

    /**
     * Remove staff from this connection if they already have other connections
     * @param array $staff Staff models (with connected profiles)
     * @param array $uids User model ids
     * @return array $users
     */
    public function filterStaff($staff, $uids=[])
    {
        if (is_array($uids)) {
            foreach ($staff as $i => $s) {
                if (in_array($s->profile->user_id, $uids)) {
                    unset($staff[$i]);
                }
            }
        }
        return $staff;
    }

    /**
     * Add user ids to uids to exclude from like connections
     * @param array $users User models
     * @param array $uids User model ids
     * @return array $uids
     */
    public function filterUserIds($users, $uids=[], $callback=false)
    {
        if ($users) {
            $userIds = $callback ? 
                ArrayHelper::getColumn($users, function ($e) { return $e->profile->user_id; }) : 
                ArrayHelper::getColumn($users, 'id');
            return is_array($uids) ? array_unique(array_merge($uids, $userIds)) : $userIds;
        }
        return $uids;
    }

    /**
     * Add user ids extracted from profiles to uids to exclude from like connections
     * @param array $profiles Profile models
     * @param array $uids User model ids
     * @return array $uids
     */
    public function filterUserIdsByProfile($profiles, $uids=[])
    {
        if ($profiles) {
            $userIds = ArrayHelper::getColumn($profiles, 'user_id');
            return is_array($uids) ? array_unique(array_merge($uids, $userIds)) : $userIds;
        }
        return $uids;
    }

    /**
     * Returns profiles or user models (users without profiles) of profile likers
     * @param array $likes User models
     * @param array $uids User model ids
     * @return array $likeProfiles
     */
    public function getLikeProfiles($likes, $uids=[])
    {
        if (is_array($uids)) {
            foreach ($likes as $i=>$like) {
                if (in_array($like->id, $uids)) {
                    unset($likes[$i]);
                }
            }
        }
        foreach($likes as $user) {
            if($profile =  $user->indActiveProfile) {
                $likeProfiles[] = $profile;
            } else {
                $likeProfiles[] = $user;
            }
        }
        return isset($likeProfiles) ? $likeProfiles : NULL;
    }

    /**
     * Validate Physical Addresses
     * Throws an error if any required address fields are missing
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePhysicalAddress($attribute, $params)
    {
        if ($this->category == self::CATEGORY_IND) {
            if ($this->ind_city && 
                $this->ind_st_prov_reg && 
                $this->ind_country) {
                return $this->$attribute;
            } else {
                $message = 'Please enter a complete address.';
                $this->addError('ind_address1', $message);
            }

        } elseif ($this->org_city && 
            $this->org_st_prov_reg && 
            $this->org_country) {
            return $this->$attribute;
        } else {
            $message = 'Please enter a complete address.';
            $this->addError('org_address1', $message);
        }
    }

    /**
     * Validate Mailing Addresses
     * Throws an error if any required address fields are missing
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateMailingAddress($attribute, $params)
    {
        if ($this->category == self::CATEGORY_IND) {
            if ($this->ind_po_city && 
                $this->ind_po_st_prov_reg && 
                $this->ind_po_country) {
                return $this->$attribute;
            } else {
                $message = 'Please enter a complete address.';
                $this->ind_po_address1 ? 
                    $this->addError('ind_po_address1', $message) :
                    $this->addError('ind_po_box', $message);
            }
        }  elseif ($this->org_po_city && 
            $this->org_po_st_prov_reg && 
            $this->org_po_country) {
            return $this->$attribute;
        } else {
            $message = 'Please enter a complete address.';
            $this->org_po_address1 ? 
                $this->addError('org_po_address1', $message) :
                $this->addError('org_po_box', $message);
        }
    }

    /**
     * Validate Mission Agency Name
     * Throws an error if both org_name (freetext) and missionAgency (from DropDownList) are blank.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateMissAgency($attribute, $params)
    {
        if (($this->org_name == NULL) && // NEED TO CREATE CONDITIONAL VALIDATOR TO REQUIRE ORG_NAME OR MISSIONAGENCY
            (($this->missionAgency == NULL) ||
            ($this->missionAgency == ''))) {
            $this->addError($attribute, 
                'You must select a mission agency from the list or enter a new name.');
            return false;
        }
        return $this->$attribute;
    }

    /**
     * Validate Fellowship and Association acronyms
     * Allow only uppercase letters with no spaces.  Convert lowercase to uppercase.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateAcronym($attribute, $params)
    {
        $acronym = preg_replace('/([^A-Za-z])/', '', $this->$attribute);
        if (strlen($acronym) == strlen($this->$attribute)) {
            $acronym = strtoupper($acronym);
            return $this->$attribute = $acronym;
        } else {
            $this->addError($attribute, 'Acronym can only be letters without spaces.');
            return false;
        }
        return $this->$attribute;
    }

    /**
     * Ensure unique fellowship name
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUniqueFellowship($attribute, $params)
    {
        $uniqueFellowship = Fellowship::find()
            ->select('name')
            ->where(['LIKE', 'name', $this->name])
            ->one();
        if ($uniqueFellowship > NULL) {
             $this->addError($attribute, 'This fellowship already exists.  Select it from the list above');
            return false;
        }
        return $this->$attribute;
    }

    /**
     * Ensure unique fellowship acronym
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUniqueFlwshpAcronym($attribute, $params)
    {
        $uniqueAcronym = Fellowship::find()
            ->select('acronym')
            ->where(['LIKE', 'acronym', $this->acronym])
            ->one();
        if ($uniqueAcronym > NULL) {
             $this->addError($attribute, 'This acronym already exists. Select the fellowship from the list above or choose a different acronym.');
            return false;
        }
        return $this->$attribute;
    }

    /**
     * Ensure unique association name
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUniqueAssociation($attribute, $params)
    {
        $uniqueAssociation = Association::find()
            ->select('name')
            ->where(['LIKE', 'name', $this->aName])
            ->one();
        if ($uniqueAssociation > NULL) {
             $this->addError($attribute, 'This association already exists.  Select it from the list above');
            return false;
        }
        return $this->$attribute;
    }

    /**
     * Ensure unique association acronym
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUniqueAssAcronym($attribute, $params)
    {
        $uniqueAcronym = Association::find()
            ->select('acronym')
            ->where(['LIKE', 'acronym', $this->aAcronym])
            ->one();
        if ($uniqueAcronym > NULL) {
             $this->addError($attribute, 'This acronym already exists. Select the association from the list above or choose a different acronym.');
            return false;
        }
        return $this->$attribute;
    }

    /**
     *  Update profile date fields: last_update, renewal_date
     * @return Profile the loaded model
     */
    public function setUpdateDate()
    {  
        if ($this->status != self::STATUS_NEW) {
            if ($this->status != self::STATUS_ACTIVE || $this->requiredFields()) {
                $update = new Expression('CURDATE()');
                $renewal = new Expression('DATE_ADD(CURDATE(), INTERVAL 2 YEARS)');
                $this->updateAttributes(['last_update' => $update, 'renewal_date' => $renewal]);
            } 
        }
        return $this;
    }

    /**
     *  Set date profile was inactivated
     * @return Profile the loaded model
     */
    public function setInactivationDate()
    {  
        if ($this->status == self::STATUS_INACTIVE) {
            $inactive = new Expression('CURDATE()');
            $this->updateAttributes(['inactivation_date' => $inactive]);
        }
        return $this;
    }

    /**
     *  Check if required linked profiles exist
     * @return boolean
     */
    public function requiredFields()
    {  
        if ($this->category == self::CATEGORY_IND) {
            if (!self::findActiveProfile($this->home_church)) {
                return false;
            }
            if ($this->type == self::TYPE_STAFF && !self::findActiveProfile($this->ministry_of)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns true if profile type is a valid (i.e. active) type 
     * Opting to use this for now because it's quicker than doing 
     * an additional db call for every profile view.
     * @param string $type
     * @return boolean
     */
    public function validType()
    {
        return in_array($this->type, [
            self::TYPE_ASSOCIATION,      
            self::TYPE_CAMP,             
            self::TYPE_CHURCH,           
            self::TYPE_EVANGELIST,       
            self::TYPE_FELLOWSHIP,       
            self::TYPE_SPECIAL,          
            self::TYPE_MISSION_AGCY,          
            self::TYPE_MUSIC,   
            self::TYPE_PASTOR,
            self::TYPE_MISSIONARY,
            self::TYPE_CHAPLAIN,
            self::TYPE_STAFF,
            self::TYPE_PRINT,
            self::TYPE_SCHOOL,     
        ]);
    }

    /**
     * Returns true if profile type is a staff profile
     * @param string $id
     * @return boolean
     */
    public function isStaff($type)
    {
        $t = Type::find()->where(['type' => $type])->one();
        return isset($t->staff) ? true : false;
    }

    /**
     * Create a new forms array to track which forms have been completed.
     * @param string $id
     * @return FormsCompleted model || false
     */
    public static function createProgress($id)
    {
        
        $progress = new FormsCompleted();
        $progressArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $progress->form_array = serialize($progressArray);
        $progress->id = $id;

        return $progress->save() ? $progress : false;
    }

    /**
     * Update $progressArray for a given form number to indicate form completion
     * @return true
     */
    public function setProgress($form)
    {
        if ($this->status != self::STATUS_ACTIVE) {
            if (!$progress = FormsCompleted::findOne($this->id)) {
                $progress = $this->createProgress($this->id);
            }
            $progressArray = unserialize($progress->form_array);
            $progressArray[$form] = 1;
            $progress->updateAttributes(['form_array' => serialize($progressArray)]);
        }
        return true;
    }

    /**
     * Get the current progress for this profile
     * @return array Unserialized FormsCompleted array || false
     */
    public function getProgress()
    {       
        return ($progress = FormsCompleted::findOne($this->id)) ?
            ($progressArray = unserialize($progress->form_array)) : false;
    }

    /**
     * Progress percent completed
     * @param array $typeArray
     * @return int
     */
    public function getProgressPercent($typeArray)
    {       
        if (!$progress = FormsCompleted::findOne($this->id)) {
            $progress = $this->createProgress($this->id);
        }
        $progressArray = unserialize($progress->form_array);
        $n = 100 * array_sum($progressArray) / (array_sum($typeArray)+1);

        return Utility::roundUpToAny($n);
    }

    /**
     * Return progress percent if the profile is not active
     * @return int || NULL
     */
    public function getProgressIfInactive()
    {       
        return ($this->status != Profile::STATUS_ACTIVE) ? 
            $this->getProgressPercent(ProfileFormController::$formArray[$this->type]) : NULL;
    }

    /**
     * Return appropriate org input label depending on profile type
     * @return string
     */
    public function getOrgNameLabel()
    {
        return $this->type == self::TYPE_SPECIAL ? 'Ministry or Organization Name' : $this->type . ' Name';
    }

    /**
     * Return appropriate parent ministry input label depending on profile type
     * @return string
     */
    public function getParentMinistryLabel()
    {
        switch ($this->type) {
            case self::TYPE_EVANGELIST:
                return 'Ministering with';
            break;
            case self::TYPE_STAFF:
                return 'On staff at';
            break;
            case self::TYPE_SPECIAL:
                return 'This ministry or organization is a ministry of';
            break;
            default:
                return 'This ' . strtolower($this->type) . ' is a ministry of';
            break;
        }
    }

    /**
     * Return appropriate home church input label depending on profile type
     * @return string
     */
    public function getChurchLabel()
    {
        switch ($this->type) {
            case self::TYPE_MISSIONARY:
                return 'Sending Church:';
            break;
             case self::TYPE_PASTOR:
                return $this->sub_type . ' at';
            break;
            default:
                return 'Home Church:';
            break;
        }
    }

    /**
     * Delete an image file on the server
     * @param string $img
     * @param string $imageLink
     * @return $this
     */
    public function deleteOldImg($img, $imageLink=NULL)
    {
        if ($img == 'image2' 
            && ($this->type == self::TYPE_CHURCH) 
            && ($pastorLink = $this->srPastorChurchConfirmed)) {
            if (isset($pastorLink->image2)) {
                $imageLink = $pastorLink->image2;
            }
        }

        $oldImg = $this->getOldAttribute($img);
        if ($oldImg 
            && (strpos($oldImg, '/uploads/') !== false) // Only delete if image is found in uploads folder
            && ($oldImg != $this->{$img})           // Only delte if image name and path has changed in db.
            && ($oldImg != $imageLink)) {           // Don't delete a linked pastor image
            unlink($oldImg);
        }
        return $this;
    }
}