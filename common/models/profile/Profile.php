<?php

namespace common\models\profile;

use borales\extensions\phoneInput\PhoneInputBehavior;
use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\profile\Country;
use common\models\profile\FormsCompleted;
use common\models\profile\GoogleGeocoder;
use common\models\profile\ProfileHasLike;
use common\models\profile\State;
use common\models\profile\Type;
use common\models\SendMail;
use common\models\User;
use common\models\Utility;
use frontend\controllers\MailController;
use frontend\controllers\ProfileController;
use sadovojav\cutter\behaviors\CutterBehavior;
use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "profile".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $profile_name
 * @property string $created_at
 * @property string $last_update
 * @property string $last_modified
 * @property string $renewal_date
 * @property string $inactivation_date
 * @property string $status
 * @property string $tagline
 * @property string $description
 * @property integer $ministry_of
 * @property string $image1
 * @property string $image2
 * @property string $flwsp_ass_level
 * @property string $org_name
 * @property string $org_address1
 * @property string $org_address2
 * @property string $org_po_box
 * @property string $org_city
 * @property string $org_st_prov_reg
 * @property string $org_zip
 * @property string $org_country
 * @property string $org_loc
 * @property string $ind_first_name
 * @property string $ind_last_name
 * @property string $spouse_first_name
 * @property string $ind_address1
 * @property string $ind_address2
 * @property string $ind_po_box
 * @property string $ind_city
 * @property string $ind_st_prov_reg
 * @property string $ind_zip
 * @property string $ind_country
 * @property string $ind_loc
 * @property integer $show_map
 * @property string $phone
 * @property string $email
 * @property string $website
 * @property integer $pastor_interim
 * @property integer $pastor_church_planter
 * @property string $miss_field
 * @property string $miss_status
 * @property integer $inappropriate
 * @property string $bible
 * @property string $worship_style
 * @property string $polity
 * @property integer $staff_id
 * @property integer $service_time_id
 * @property integer $social_id
 * @property integer $flwship_id
 * @property integer $ass_id
 * @property integer $miss_housing_id
 */

class Profile extends \yii\db\ActiveRecord
{

    /**
     * @const int $STATUS_* The status of the profile.
     */
    const STATUS_NEW = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20;
    const STATUS_TRASH = 30;

    /**
     * @const int $CATEGORY_* The category (individual or organization) of the profile.
     */
    const CATEGORY_IND = 10;
    const CATEGORY_ORG = 20;

    /**
     * @const int $PRIVATE_EMAIL_* Private email request status
     * Default is 0
     */
    const PRIVATE_EMAIL_NONE = 0;
    const PRIVATE_EMAIL_ACTIVE = 10;
    const PRIVATE_EMAIL_PENDING = 20;

    /**
     * @const int $MAP_* Map choices to determine which map to display on profile
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
        'Association' => '<span class="glyphicons glyphicons-group"></span>',
        'Camp' => '<span class="glyphicons glyphicons-camping"></span>',
        'Chaplain' => '<span class="glyphicons glyphicons-shield"></span>',
        'Church' => '<span class="glyphicons glyphicons-temple-christianity-church type-icon"></span>',
        'Evangelist' => '<span class="glyphicons glyphicons-fire"></span>',
        'Fellowship' => '<span class="glyphicons glyphicons-handshake"></span>',
        'Mission Agency' => '<span class="glyphicons glyphicons-globe-af"></span>',
        'Missionary' => '<span class="glyphicons glyphicons-person-walking"></span>',
        'Music Ministry' => '<span class="glyphicons glyphicons-music"></span>',
        'Pastor' => '<span class="glyphicons glyphicons-book-open"></span>',
        'Print Ministry' => '<span class="glyphicons glyphicons-book"></span>',
        'School' => '<span class="glyphicons glyphicons-education"></span>',
        'Special Ministry' => '<span class="glyphicons glyphicons-global"></span>',
        'Staff' => '<span class="glyphicons glyphicons-briefcase"></span>',
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
     * @var string $formattedNames Names in the format "First (& Spouse) Last" or "First Last"
     */
    public $formattedNames;

    /**
     * @var string $missHousing User selection of whether church has missions housing
     */
    public $missHousing;

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
            'lo-org' => ['org_address1', 'org_address2', 'org_city', 'org_st_prov_reg', 'org_zip', 'org_country', 'map', 'org_po_address1', 'org_po_address2', 'org_po_box', 'org_po_city', 'org_po_st_prov_reg', 'org_po_zip', 'org_po_country', 'url_loc'],
    // lo-ind: Location Individual
            'lo-ind' => ['ind_address1', 'ind_address2', 'ind_city', 'ind_st_prov_reg', 'ind_zip', 'ind_country', 'map', 'ind_po_address1', 'ind_po_address2', 'ind_po_box', 'ind_po_city', 'ind_po_st_prov_reg', 'ind_po_zip', 'ind_po_country', 'url_loc'],
    // co: Contact
            'co' => ['phone', 'email', 'email_pvt', 'website'],
    // co: Contact - Forwarding Email
            'co-fe' => ['phone', 'email', 'email_pvt', 'website'],
    // sf: Staff - Church
            'sf-church' => ['ind_first_name', 'ind_last_name', 'spouse_first_name', 'pastor_interim', 'cp_pastor'],
    // sf-org: Staff Organization
            'sf-org' => ['staff'],
    // hc-required: Home Church
            'hc-required' => ['select', 'map'],
    // hc: Parent Home Church
            'hc' => ['select', 'map'],
    // pm-required: Parent Ministry for Staff
            'pm-required' => ['select', 'selectM', 'titleM', 'map'],
    // pm-ind: Other Ministries of individuals
            'pm-ind' => ['select', 'selectM', 'titleM', 'map'],
    // pm-org: Parent Ministry
            'pm-org' => ['select', 'map'],
    // pg: Programs
            'pg' => ['select'],
    // sa: Schools Attended
            'sa' => ['select'],
    // sl: School Levels
            'sl' => ['select'],
    // ma-church: Mission Agencies - Church
            'ma-church' => ['select', 'missHousing', 'packet'],
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
                return $profile->type == 'Missionary';
            }, 'whenClient' => "function (attribute, value) {
                return $('#profile-type').val() == 'Missionary';
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
            [['org_po_box'], 'string', 'max' => 6, 'on' => 'lo-org'],
            [['org_st_prov_reg', 'org_po_st_prov_reg'], 'string', 'max' => 50, 'on' => 'lo-org'],
            [['org_zip', 'org_po_zip'], 'string', 'max' => 20, 'on' => 'lo-org'],
            [['org_address1', 'org_address2', 'org_city', 'org_st_prov_reg', 'org_zip', 'org_po_address1', 'org_po_address2', 'org_po_city', 'org_po_st_prov_reg', 'org_po_zip'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'lo-org'],
            [['org_address1', 'org_address2', 'org_city', 'org_st_prov_reg', 'org_zip', 'org_po_address1', 'org_po_address2', 'org_po_city', 'org_po_st_prov_reg', 'org_po_zip'], 'trim', 'skipOnEmpty' => true, 'on' => 'lo-org'],
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
            [['ind_po_box'], 'string', 'max' => 6, 'on' => 'lo-ind'],
            [['ind_st_prov_reg', 'ind_po_st_prov_reg'], 'string', 'max' => 50, 'on' => 'lo-ind'],
            [['ind_zip', 'ind_po_zip'], 'string', 'max' => 20, 'on' => 'lo-ind'],
            [['ind_address1', 'ind_address2', 'ind_city', 'ind_st_prov_reg', 'ind_zip', 'ind_po_address1', 'ind_po_address2', 'ind_po_city', 'ind_po_st_prov_reg', 'ind_po_zip'], 'filter', 'filter' => 'strip_tags', 'skipOnEmpty' => true, 'on' => 'lo-ind'],
            [['ind_address1', 'ind_address2', 'ind_city', 'ind_st_prov_reg', 'ind_zip', 'ind_po_address1', 'ind_po_address2', 'ind_po_city', 'ind_po_st_prov_reg', 'ind_po_zip'], 'trim', 'skipOnEmpty' => true, 'on' => 'lo-ind'],
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
            [['email_pvt'], 'required', 'on' => 'co-fe'],
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

    // hc-required: Home Church ('select', 'map')
            ['select', 'required', 'on' => 'hc-required'],
            ['map', 'safe', 'on' => 'hc-required'],

    // hc: Home Church ('select', 'map')
            [['select', 'map'], 'safe', 'on' => 'hc'],

    // pm-required: Parent Ministry for Staff ('select', 'selectM', 'titleM', 'map')
            ['select', 'required', 'on' => 'pm-required'],
            ['titleM', 'string', 'max' => 60, 'on' => 'pm-required'],
            ['map', 'safe', 'on' => 'pm-required'],

    // pm-ind: Other Ministries for Individuals ('select', 'selectM', 'titleM', 'map')
            ['titleM', 'string', 'max' => 60, 'on' => 'pm-ind'],
            [['select', 'selectM', 'map'], 'safe', 'on' => 'pm-ind'],

    // pm-org: Parent Ministry ('select', 'map')
            ['select', 'safe', 'on' => 'pm-org'],
            [['map'], 'safe', 'on' => 'pm-org'],

    // pg: Programs ('select')
            ['select', 'safe', 'on' => 'pg'],

    // sa: Schools Attended ('select')
            ['select', 'safe', 'on' => 'sa'],

    // sl: School Levels ('select')
            ['select', 'safe', 'on' => 'sl'],

    // ma-church: Mission Agencies - Church ('select', 'missHousing', 'packet')
            ['select', 'default', 'value' => NULL, 'on' => 'ma-church'],
            ['missHousing', 'default', 'value' => 'N', 'on' => 'ma-church'],
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
    // Determine profile type specific labels
        $parent_ministry = $this->getMinistryLabel($this->type);
        $home_church = $this->getChurchLabel($this->type, $this->sub_type);
        $this->type == 'Special Ministry' ?
            $orgNameLabel = 'Ministry or Organization Name' :
            $orgNameLabel = $this->type . ' Name';
        switch($this->scenario){                                                                    // Apply labels depending on scenario
    
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
                'org_name' => $orgNameLabel,
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
                'name' => $this->org_name == NULL ? 'Or enter a new name here' : 'Fellowship Name',
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

    // hc-required: Home Church ('select')
            case 'hc-required':
            return [
                'select' => $home_church,
                'map' => 'Show a Google map of this ministry on my profile',
            ];
            break;

    // hc: Home Church ('select', 'map')
            case 'hc':
            return [
                'select' => $home_church,
                'map' => 'Show a Google map of this ministry on my profile',
            ];
            break;

    // pm-required: Parent Ministry for Staff ('select', 'selectM', 'titleM', 'map')
            case 'pm-required':
            return [
                'select' => $parent_ministry,
                'selectM' =>  'Ministry',
                'titleM' => 'Title',
                'map' => 'Show a Google map of this ministry on my profile',
            ];
            break;

    // pm-ind: Other Ministries for Individuals ('select', 'selectM', 'titleM', 'map')
            case 'pm-ind':
            return [
                'select' => $parent_ministry,
                'selectM' =>  'Ministry',
                'titleM' => 'Title',
                'map' => 'Show a Google map of this ministry on my profile',
            ];
            break;

    // pm: Parent Ministry ('select', 'map')
            case 'pm-org':
            return [
                'select' => $parent_ministry,
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

    // ma-church: Mission Agencies - Church ('select', 'missHousing', 'packet')
            case 'ma-church':
            return ['missHousing' => 'Does the church have mission housing or motorhome/trailer parking?'];
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
            return ['select' => 'Acreditation or Association'];
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

    public function profileCreate()
    {
        $this->status = self::STATUS_NEW;
        $this->user_id = Yii::$app->user->identity->id;

        if ($this->type == 'Pastor') {                                                              // Set Subtype
            $this->sub_type = $this->ptype;
        } elseif ($this->type == 'Missionary') {
            $this->sub_type = $this->mtype;
        } elseif ($this->type == 'Chaplain') {
            $this->sub_type = $this->ctype;
        } else {
            $this->sub_type = $this->type;
        }

        $type = Type::findOne(['type' => $this->type]);
        $type->group == 'Individuals' ? 
            $this->category = self::CATEGORY_IND : 
            $this->category = self::CATEGORY_ORG;

        if ($this->validate() && $this->getIsNewRecord() && $this->save()) { 
            return $this;
        }
        return false;
    }




/***************************************************************************************************
 ***************************************************************************************************
 *
 * The following functions process the incoming data from the profile data collection forms 
 * 
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
        if ($this->type == 'Mission Agency') {
            if (!empty($this->select)) {
                $mission = MissionAgcy::findOne($this->select);
                if ($mission && ($this->getOldAttribute('org_name') != $mission->mission)) {
                    $this->org_name = $mission->mission;                                                          
                    if ($oldA = MissionAgcy::find()->where(['profile_id' => $this->id])->one()) {    // Unlink old mission agcency
                        $oldA->unlink('linkedProfile', $this);
                    }
                    $mission->link('linkedProfile', $this);                                          // link mission agency in mission agency table
                }
            } elseif (!empty($this->name) && !MissionAgcy::find()                                    // Check for duplicate
                ->where(['mission' => $this->name])->exists()) {
                $this->org_name = $this->name;
                $mission = new MissionAgcy();                                                        // Add to mission agency table
                $mission->mission = $this->name;
                $mission->mission_acronym = $this->acronym;
                $mission->profile_id = $this->id;
                $mission->validate();
                $mission->save();
            }
        }

    // *************************** Fellowship **********************************
        if ($this->type == 'Fellowship') {  
            if (!empty($this->select)) {
                $fellowship = ProfileController::findFellowship($this->select);
                if ($fellowship && ($this->getOldAttribute('org_name') != $fellowship->fellowship)) {
                    $this->org_name = $fellowship->fellowship;                                                      
                    if ($oldA = Fellowship::find()->where(['profile_id' => $this->id])->one()) {    // Unlink old fellowship
                        $oldA->unlink('profile', $this);
                    }
                    $fellowship->link('linkedProfile', $this);                                      // link fellowship in fellowship table
                }
            } elseif (!empty($this->name) && !Fellowship::find()                                    // Check for duplicate
                ->where(['fellowship' => $this->name])->exists()) {
                $this->org_name = $this->name;
                $fellowship = new Fellowship();                                                     // Add to fellowship table
                $fellowship->fellowship = $this->name;
                $fellowship->fellowship_acronym = $this->acronym;
                $fellowship->profile_id = $this->id;
                $fellowship->validate();
                $fellowship->save();
            }
        }

        // ************************** Association ******************************
        if ($this->type == 'Association') {
            if (!empty($this->select)) {
                $association = Association::findOne($this->select);
                if ($association && ($this->getOldAttribute('org_name') != $association->association)) {
                    $this->org_name = $association->association;                                                          
                    if ($oldA = Association::find()->where(['profile_id' => $this->id])->one()) {   // Unlink old association
                        $oldA->unlink('profile', $this);
                    }
                    $association->link('linkedProfile', $this);                                     // link association in association table
                }
            } elseif (!empty($this->name) && !Association::find()                                   // Check for duplicate
                ->where(['association' => $this->name])->exists()) {
                $this->org_name = $this->name;
                $association = new Association();                                                   // Add to association table
                $association->association = $this->name;
                $association->association_acronym = $this->acronym;
                $association->profile_id = $this->id;
                $association->validate();
                $association->save();
            }
        }

    // ***************************** School ************************************
        if ($this->type == 'School') {
            if (!empty($this->select)) {
                $name = explode('(', $this->select, 2);
                $school = School::find()->where(['school' => $name[0]])->one();
                if ($school && ($this->getOldAttribute('org_name') != $name[0])) {
                    $this->org_name = $school->school;
                    $this->org_city = $school->city;
                    $this->org_st_prov_reg = $school->st_prov_reg;
                    $this->org_country = $school->country;
                    
                    if ($oldSchool = School::find()->where(['profile_id' => $this->id])->one()) {   // Unlink old school
                        $oldSchool->unlink('linkedProfile', $this);
                    }
                    $school->link('linkedProfile', $this);                                          // link school in school table                                       
                }
            } elseif (!empty($this->name)) {
                $this->org_name = $this->name;
            }
        }

        $this->category == self::CATEGORY_IND ?                                                     // Update url name
            $this->url_name = $this->urlName($this->ind_last_name) :
            $this->url_name = $this->urlName($this->org_name);

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

                if (empty($this->ind_city)) {                                                       // if physical address is empty, populate city, state, country, and zip from mailing address
                    $this->ind_city = $this->ind_po_city;
                    $this->ind_st_prov_reg = $this->ind_po_st_prov_reg;
                    $this->ind_zip = $this->ind_po_zip;
                    $this->ind_country = $this->ind_po_country;
                }
            
                $this->ind_address1 ? $address = $this->ind_address1 . ',+' : $address = '';        // Assemble international address string for geocoding (123+Main+St,+Mullingar,+Westmeath,+Ireland)
                $address .= $this->ind_city . ',+';
                $address .= $this->ind_st_prov_reg . ',+';
                $address .= $this->ind_country;
                $address = preg_replace('/\s+/', '+', $address);                                    // Replace all spaces with "+"

                $geocoder = new GoogleGeocoder();
                try{
                    $result = $geocoder->getLatLngOfAddress($address);
                } catch(Exception $e){
                    //Something went wrong!
                    echo '$e->getMessage()'; die;
                }
                $this->ind_loc = $result['lat'] . ',' . $result['lng'];

                if ($this->ind_country == 'United States') {                                        // Convert US states to abbreviations
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

                if (empty($this->org_city)) {                                                       // if physical address is empty, populate city, state, country, and zip from mailing address
                    $this->org_city = $this->org_po_city;
                    $this->org_st_prov_reg = $this->org_po_st_prov_reg;
                    $this->org_zip = $this->org_po_zip;
                    $this->org_country = $this->org_po_country;
                }
            
                $this->org_address1 ? $address = $this->org_address1 . ',+' : $address = '';        // Assemble international address string for geocoding (123+Main+St,+Mullingar,+Westmeath,+Ireland)
                $address .= $this->org_city . ',+';
                $address .= $this->org_st_prov_reg . ',+';
                $address .= $this->org_country;
                $address = preg_replace('/\s+/', '+', $address);                                    // Replace all spaces with "+"

                $geocoder = new GoogleGeocoder();
                $result = $geocoder->getLatLngOfAddress($address);
                $this->org_loc = $result['lat'] . ',' . $result['lng'];
            
                if ($this->org_country == 'United States') {                                        // Convert US states to abbreviations
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
             
            $oldMap = $this->getOldAttribute('show_map');
            if ($oldMap == self::MAP_PRIMARY && empty($this->map)) {
                $this->show_map = NULL;
            } elseif (!empty($this->map)) {
                $this->show_map = self::MAP_PRIMARY;
            }

            if ($this->type != 'Missionary') {
                $this->category == self::CATEGORY_IND ?                                              // Update Url location
                $this->url_loc = $this->urlName($this->ind_city) :
                $this->url_loc = $this->urlName($this->org_city);
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
            if ($this->social_id != $social->id) {
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
    public function handleFormSFSA($profile)
    {
        $ids = explode('+', $_POST['senior']);
        $pastor = $this->findModel($ids[0]);
        $staff = Staff::findOne($ids[1]);
        if ($pastor && $staff) {
            $this->updateAttributes([
                'ind_first_name' => $pastor->ind_first_name,
                'ind_last_name' => $pastor->ind_last_name,
                'spouse_first_name' => $pastor->spouse_first_name,
            ]);
            $staff->updateAttributes(['sr_pastor' => 1, 'confirmed' => 1]);

            $pastorProfileOwner = User::findOne($pastor->user_id);
            MailController::initSendLink($this, $pastor, $pastorProfileOwner, 'SFSA', 'L');         // Notify staff profile owner of unconfirmed status

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
        if ($staff = Staff::find()
            ->where(['staff_id' => $_POST['clear']])
            ->andWhere(['ministry_id' => $this->id])
            ->andWhere(['sr_pastor' => 1])
            ->one()) {
            $staff->updateAttributes([
                'confirmed' => NULL, 
                'sr_pastor' => NULL]);
        }
        $this->updateAttributes([                                                                   // remove all reference to pastor for the church profile
            'ind_first_name' => NULL,                                       
            'spouse_first_name' => NULL,
            'ind_last_name' => NULL,
            'cp_pastor' => NULL,
            'pastor_interim' => NULL,
        ]);
        
        $pastor = $this->findModel($staff->staff_id);
        $pastorProfileOwner = User::findOne($pastor->user_id);
        MailController::initSendLink($this, $pastor, $pastorProfileOwner, 'SFSA', 'UL');            // Notify staff profile owner of unconfirmed status

        return $this;
    }

    /**
     * handleFormHC: Home Church
     * 
     * @return mixed
     */
    public function handleFormHC()
    {
        if ($this->select != NULL) {
            $this->home_church = $this->select;
            if (!$staff = Staff::find()
                ->where(['staff_id' => $this->id])
                ->andWhere(['ministry_id' => $this->select])
                ->andWhere(['home_church' => 1])
                ->one()) {
                $staff = new Staff();
                $staff->save();
            }

            if ($this->select != $staff->ministry_id) {                                             // Did user select a new ministry?
                $profile = ProfileController::findProfile($this->id);
                $churchProfile = ProfileController::findProfile($this->select);
                $churchProfileOwner = User::findOne($churchProfile->user_id);
                MailController::initSendLink($profile, $churchProfile, $churchProfileOwner, 'HC', 'L');    // Notify church profile owner of new linked profile
            }

            $staff->updateAttributes([
                'staff_id' => $this->id, 
                'staff_type' => $this->type,
                'staff_title' => $this->sub_type,
                'ministry_id' => $this->select,
                'home_church' => 1]);

            if ($this->type == 'Pastor') {                                                          // Only add pastors to staff table on home church form; other staff will be added on staff form
                $staff->updateAttributes(['church_pastor' => 1]);
            }
        }

        $oldMap = $this->getOldAttribute('show_map');
        if ($oldMap == self::MAP_CHURCH && empty($this->map)) {
            $this->show_map = NULL;
        } elseif (!empty($this->map)) {
            $this->show_map = self::MAP_CHURCH;
        }

        if ($this->save() && $this->setUpdateDate()) {
            return $this;
        }
        return false;
    }

    /**
     * handleFormHCR: Home Church Remove
     * 
     * @return mixed
     */
    public function handleFormHCR()
    {
        if ($staff = Staff::find()
            ->where(['staff_id' => $this->id])
            ->andWhere(['ministry_id' => $this->home_church])
            ->andWhere(['staff_title' => $this->sub_type])
            ->andWhere(['home_church' => 1])
            ->one()) {

            $profile = ProfileController::findProfile($staff->staff_id);
            $churchProfile = ProfileController::findProfile($staff->ministry_id);
            $churchProfileOwner = User::findOne($churchProfile->user_id);
            MailController::initSendLink($profile, $churchProfile, $churchProfileOwner, 'HC', 'UL');// Notify church profile owner of unlinked profile

            $staff->delete();
        }
        $this->updateAttributes(['home_church' => NULL]);
        return $this;
    }

    /**
     * handleFormPM: Parent Ministry
     * 
     * @return mixed
     */
    public function handleFormPM()
    {
        if ($this->select != NULL) {
            if ($this->category == self::CATEGORY_IND) {                                            // If individual, Update staff table regardless of new or existing connection
                $this->type == 'Staff' ? $title = $this->title : $title = $this->sub_type;
                if (!$staff = Staff::find()                                                         // Add to staff table if not already there
                    ->where(['staff_id' => $this->id])
                    ->andWhere(['ministry_id' => $this->ministry_of])
                    ->andWhere(['staff_title' => $title])
                    ->one()) {
                    $staff = new Staff();
                    $staff->save();
                }
                $staff->updateAttributes([
                    'staff_id' => $this->id, 
                    'staff_type' => $this->type,
                    'staff_title' => $title,
                    'ministry_id' => $this->select,
                    'ministry_of' => 1]);
            }
            if ($this->ministry_of != $this->select) {

                $ministryProfile = ProfileController::findProfile($this->select);
                $ministryProfileOwner = User::findOne($ministryProfile->user_id);
                MailController::initSendLink($this, $ministryProfile, $ministryProfileOwner, 'PM', 'L');     // Notify ministry profile owner of new link
                
                $this->ministry_of = $this->select;                                                 // Update profile ministry_of
            }
        }
        
        $oldMap = $this->getOldAttribute('show_map');
        if ($oldMap == self::MAP_MINISTRY && empty($this->map)) {
            $this->show_map = NULL;
        } elseif (!empty($this->map)) {
            $this->show_map = self::MAP_MINISTRY;
        }

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
                ->andWhere(['staff_type' => $this->type])                                           // Allow for multiple staff roles at same church
                ->andWhere(['staff_title' => $this->titleM])                                            // 
                ->andWhere(['ministry_id' => $this->selectM])
                ->andWhere(['ministry_other' => 1])
                ->one()) {
                $staff = new Staff();
                $staff->save();
            }

            if ($staff->ministry_id != $this->selectM) {                                            // Send mail to notify ministry profile owner of new link
                $ministryProfile = ProfileController::findProfile($this->selectM);
                $ministryProfileOwner = User::findOne($ministryProfile->user_id);
                MailController::initSendLink($this, $ministryProfile, $ministryProfileOwner, 'PM', 'L');   
            }
                
            $staff->updateAttributes([
                'staff_id' => $this->id, 
                'staff_type' => $this->type,
                'staff_title' => $this->titleM,
                'ministry_id' => $this->selectM,
                'ministry_other' => 1]);

        }
        return true;
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
            MailController::initSendLink($this, $pgProfile, $pgProfileOwner, 'PG', 'UL');           // Notify program profile owner of unlinked church

            $this->unlink('program', $pgProfile, $delete = true);
        
        } elseif ($this->select != NULL) {
            $pgProfile = $this->findOne($this->select);
            $linked = false;
            if ($pgs = $this->program) {
                foreach($pgs as $pg) {                                                              // Check to see if program is already linked to profile
                    if ($pg->id == $pgProfile->id) {
                        $linked = true;
                    }
                }
            }
            if (!$linked && $this->setUpdateDate()) {                                               // Link program to file
                $this->link('program', $pgProfile);
            }

            $pgProfileOwner = User::findOne($pgProfile->user_id);
            MailController::initSendLink($this, $pgProfile, $pgProfileOwner, 'PG', 'L');            // Notify program profile owner of unlinked church

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
            $oldSelect = arrayHelper::map($this->school, 'id', 'id');
            if (empty($oldSelect) && ($select = $this->select) != NULL) {                           // handle case of new selection
                foreach ($select as $value) {
                    $sc = School::findOne($value);
                    $this->link('school', $sc);                                                     // Link new schools

                    $scProfile = $sc->linkedProfile;
                    if ($scProfile && ($scProfileOwner = $scProfile->user)) {
                        MailController::initSendLink($this, $scProfile, $scProfileOwner, 'SA', 'L');   // notify school profile owner of link
                    }
                }
            }
            if (!empty($oldSelect) && empty($this->select))  {                                      // handle case of all unselected
                $schoolArray = $this->school;
                foreach($schoolArray as $sc) {
                    
                    $scProfile = $sc->linkedProfile;
                    if ($scProfile && ($scProfileOwner = $scProfile->user)) {
                        MailController::initSendLink($this, $scProfile, $scProfileOwner, 'SA', 'UL');  // notify school profile owner of unlink
                    }

                    $sc->unlink('profile', $this, $delete = true);                                  // unlink all schools

                }
            }
            if (!empty($oldSelect) && ($select = $this->select) != NULL) {                          // handle all other cases of change in selection
                foreach($select as $value) {                                                        // link any new selections
                    if(!in_array($value, $oldSelect)) {
                        $sc = School::findOne($value);
                        $this->link('school', $sc);

                        $scProfile = $sc->linkedProfile;
                        if ($scProfile && ($scProfileOwner = $scProfile->user)) {
                            MailController::initSendLink($this, $scProfile, $scProfileOwner, 'SA', 'L');   // notify school profile owner of link
                        }

                    }
                }
                foreach($oldSelect as $value) {                                                     // unlink any selections that were removed
                    if(!in_array($value, $select)) {
                        $sc = School::findOne($value);

                        $scProfile = $sc->linkedProfile;
                        if ($scProfile && ($scProfileOwner = $scProfile->user)) {
                            MailController::initSendLink($this, $scProfile, $scProfileOwner, 'SA', 'UL');  // Notify school profile owner of unlink
                        }

                        $this->unlink('school', $sc, $delete = true);
                
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
            $oldSelect = arrayHelper::map($this->schoolLevel, 'id', 'id');
            if (empty($oldSelect) && ($select = $this->select) != NULL) {                           // handle case of new selection
                foreach ($select as $value) {
                    $s = SchoolLevel::findOne($value);
                    $this->link('schoolLevel', $s);
                }
            }
            if (!empty($oldSelect) && empty($this->select))  {                                      // handle case of all unselected
                $s = $this->schoolLevel;
                foreach($s as $model) {
                    $model->unlink('profile', $this, $delete = TRUE);
                }
            }
            if (!empty($oldSelect) && ($select = $this->select) != NULL) {                          // handle all other cases of change in selection
                foreach($select as $value) {                                                        // link any new selections
                    if(!in_array($value, $oldSelect)) {
                        $s = SchoolLevel::findOne($value);
                        $this->link('schoolLevel', $s);
                    }
                }
                foreach($oldSelect as $value) {                                                     // unlink any selections that were removed
                    if(!in_array($value, $select)) {
                        $s = SchoolLevel::findOne($value);
                        $this->unlink('schoolLevel', $s, $delete = TRUE);
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
        if ($uploadPacket = UploadedFile::getInstance($this, 'packet')) {                           // Create subfolders on server and store uploaded pdf
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
        $oldSelect = arrayHelper::map($this->missionAgcy, 'id', 'id');
        if (empty($oldSelect) && !empty($select = $this->select)) {                                 // handle case of new selection
            foreach ($select as $value) {
                $a = MissionAgcy::findOne($value);
                    $this->link('missionAgcy', $a);
            }
        }
        if (!empty($oldSelect) && empty($this->select))  {                                          // handle case of all unselected
            $a = $this->missionAgcy;
            foreach($a as $model) {
                $model->unlink('profile', $this, $delete = TRUE);
            }
        }
        if (!empty($oldSelect) && !empty($select = $this->select)) {                                // handle all other cases of change in selection
            foreach($select as $value) {                                                            // link any new selections
                if(!in_array($value, $oldSelect)) {
                    $a = MissionAgcy::findOne($value);
                    $this->link('missionAgcy', $a);
                }
            }
            foreach($oldSelect as $value) {                                                         // unlink any selections that were removed
                if(!in_array($value, $select)) {
                    $a = MissionAgcy::findOne($value);
                    $this->unlink('missionAgcy', $a, $delete = TRUE);
                }
            }
        }

    // *********************** Missions Housing ********************************
        if ($this->miss_housing_id && $this->missHousing == 'N') {                                  // Handle case of deleting mission housing
            
            if ($v = MissHousing::find()
                ->with('missHousingVisibility')
                ->where(['id' => $this->miss_housing_id])
                ->one()) {  
                if ($v->missHousingVisibility) {
                    $v->unlink('missHousingVisibility', $v->missHousingVisibility[0], $delete = TRUE); 
                }
                $this->unlink('missHousing', $v);
                $v->delete();
            }                       
        }

    // ***************************** Save **************************************

        if ($this->validate() && $this->save() && $this->setUpdateDate()) {                         // Save Profile instance            
            return $this;
        }
        return False;
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

            $oldSelect = arrayHelper::map($this->accreditation, 'id', 'id');
            if (empty($oldSelect) && ($select = $this->select) != NULL) {                           // handle case of new selection
                foreach ($select as $value) {
                    $a = Accreditation::findOne($value);
                        $this->link('accreditation', $a);
                }
            }
            if (!empty($oldSelect) && empty($this->select))  {                                      // handle case of all unselected
                $a = $this->accreditation;
                foreach($a as $model) {
                    $model->unlink('profile', $this, $delete = TRUE);
                }
            }
            if (!empty($oldSelect) && ($select = $this->select) != NULL) {                          // handle all other cases of change in selection
                foreach($select as $value) {                                                        // link any new selections
                    if(!in_array($value, $oldSelect)) {
                        $a = Accreditation::findOne($value);
                        $this->link('accreditation', $a);
                    }
                }
                foreach($oldSelect as $value) {                                                     // unlink any selections that were removed
                    if(!in_array($value, $select)) {
                        $a = Accreditation::findOne($value);
                        $this->unlink('accreditation', $a, $delete = TRUE);
                    }
                }
            }
            return $this;                                                                           // No need to save $profile model
        }

    // ************************** Fellowship ***********************************
        
        $oldSelect = arrayHelper::map($this->fellowship, 'id', 'id');
        if (empty($oldSelect) && ($select = $this->select) != NULL) {                               // handle case of new selection
            foreach ($select as $value) {
                $f = Fellowship::findOne($value);
                $this->link('fellowship', $f);

                if ($fProfile = $f->linkedProfile) {                                                // notify new fellowship profile owner of new link
                    $fProfileOwner = User::findOne($fProfile->user_id);
                    MailController::initSendLink($this, $fProfile, $fProfileOwner, 'AS', 'L');      
                }

            }
        }
        if (!empty($oldSelect) && empty($this->select))  {                                          // handle case of all unselected
            $f = $this->fellowship;
            foreach($f as $model) {
                $model->unlink('profile', $this, $delete = TRUE);

                if ($fProfile = $f->linkedProfile) {                                                // notify old fellowship profile owner of unlink
                    $fProfileOwner = User::findOne($fProfile->user_id);
                    MailController::initSendLink($this, $fProfile, $fProfileOwner, 'AS', 'UL');      
                }
            }
        }
        if (!empty($oldSelect) && ($select = $this->select) != NULL) {                              // handle all other cases of change in selection
            foreach($select as $value) {                                                            // link any new selections
                if(!in_array($value, $oldSelect)) {
                    $f = Fellowship::findOne($value);
                    $this->link('fellowship', $f);

                    if ($fProfile = $f->linkedProfile) {                                            // notify new fellowship profile owner of new link
                        $fProfileOwner = User::findOne($fProfile->user_id);
                        MailController::initSendLink($this, $fProfile, $fProfileOwner, 'AS', 'L');      
                    }
                }
            }
            foreach($oldSelect as $value) {                                                         // unlink any selections that were removed
                if(!in_array($value, $select)) {
                    $f = Fellowship::findOne($value);
                    $this->unlink('fellowship', $f, $delete = TRUE);

                    if ($fProfile = $f->linkedProfile) {                                            // notify old fellowship profile owner of unlink
                        $fProfileOwner = User::findOne($fProfile->user_id);
                        MailController::initSendLink($this, $fProfile, $fProfileOwner, 'AS', 'UL');      
                    }
                }
            }
        }

        if ($this->name != NULL) {                                                                  // Give preference to text input if both fellowshipSelect and fellowshipName are populated (better to collect more data, can delete duplicate entries if need be; and mitigates accidental selection)
            if ($this->validate()) {
                $newF = new Fellowship();
                $newF->fellowship = $this->name;
                $newF->fellowship_acronym = $this->acronym;
                if ($newF->save()) {
                    $this->link('fellowship', $newF);
                }
            } else {
                return false;                                                                       // Validation failed
            }                                                          
        }

    // ************************* Association ***********************************
        $oldSelectM = arrayHelper::map($this->association, 'id', 'id');
        if (empty($oldSelectM) && ($selectM = $this->selectM) != NULL) {                            // handle case of new selection
            foreach ($selectM as $value) {
                $a = Association::findOne($value);
                $this->link('association', $a);

                if ($aProfile = $a->linkedProfile) {                                                // notify new association profile owner of new link
                    $aProfileOwner = User::findOne($aProfile->user_id);
                    MailController::initSendLink($this, $aProfile, $aProfileOwner, 'AS', 'L');      
                }

            }
        }
        if (!empty($oldSelectM) && empty($this->selectM))  {                                        // handle case of all unselected
            $a = $this->association;
            foreach($a as $model) {
                $model->unlink('profile', $this, $delete = TRUE);

                if ($aProfile = $a->linkedProfile) {                                                // notify old association profile owner of unlink
                    $aProfileOwner = User::findOne($aProfile->user_id);
                    MailController::initSendLink($this, $aProfile, $aProfileOwner, 'AS', 'UL');      
                }
            }
        }
        if (!empty($oldSelectM) && ($selectM = $this->selectM) != NULL) {                           // handle all other cases of change in selection
            foreach($selectM as $value) {                                                           // link any new selections
                if(!in_array($value, $oldSelectM)) {
                    $a = Association::findOne($value);
                    $this->link('association', $a);
 
                    if ($aProfile =$a->linkedProfile) {                                            // notify new association profile owner of new link
                        $aProfileOwner = User::findOne($aProfile->user_id);
                        MailController::initSendLink($this, $aProfile, $aProfileOwner, 'AS', 'L');      
                    }
                }
            }
            foreach($oldSelectM as $value) {                                                        // unlink any selections that were removed
                if(!in_array($value, $selectM)) {
                    $a = Association::findOne($value);
                    $this->unlink('association', $a, $delete = TRUE);

                    if ($aProfile = $a->linkedProfile) {                                            // notify old association profile owner of unlink
                        $aProfileOwner = User::findOne($aProfile->user_id);
                        MailController::initSendLink($this, $aProfile, $aProfileOwner, 'AS', 'UL');      
                    }
                }
            }
        }

        if ($this->aName != NULL) {                                                                 // Give preference to text input if both associationSelect and associationName are populated (better to collect more data, can delete duplicate entries if need be; and mitigates accidental selection)
            if ($this->validate()) {
                $newA = new Association();
                $newA->association = $this->aName;
                $newA->association_acronym = $this->aAcronym;
                if ($newA->save()) {
                    $this->link('association', $newA);
                }
            } else {
                return false;                                                                       // Validation failed
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

        $oldSelect = arrayHelper::map($this->tag, 'id', 'id');
        if (empty($oldSelect) && !empty($select = $this->select)) {                                 // handle case of new selection
            foreach ($select as $value) {
                $t = Tag::findOne($value);
                    $this->link('tag', $t);
            }
        }
        if (!empty($oldSelect) && empty($this->select))  {                                          // handle case of all unselected
            $t = $this->tag;
            foreach($t as $model) {
                $model->unlink('profile', $this, $delete = TRUE);
            }
        }
        if (!empty($oldSelect) && !empty($select = $this->select)) {                                // handle all other cases of change in selection
            foreach($select as $value) {                                                            // link any new selections
                if(!in_array($value, $oldSelect)) {
                    $t = Tag::findOne($value);
                    $this->link('tag', $t);
                }
            }
            foreach($oldSelect as $value) {                                                         // unlink any selections that were removed
                if(!in_array($value, $select)) {
                    $t = Tag::findOne($value);
                    $this->unlink('tag', $t, $delete = TRUE);
                }
            }
        }

    // ***************************** Save **************************************

        if ($this->validate() && $this->save() && $this->setUpdateDate()) {                         // Save Profile instance            
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
     * Generates new profile transfer token
     */
    public function generateProfileTransferToken($userId)
    {
        return $userId . '+' . Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new profile transfer token
     */
    public function checkProfileTransferToken($token)
    {
        if ($token == $this->transfer_token) {                                                      // Is the token same as db token?
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['profileTransferTokenExpire'];                              // Is the token expired?
            return $timestamp + $expire >= time();
        }         
        return false;                                      
    }

    /**
     * Set profile status to "Active" 
     * Update created_at, last_update, and renewal_date fields
     * @return
     */
    public function activate()
    {
        $createDate = new Expression('CURDATE()');

        if ($this->category == self::CATEGORY_IND) {
            $name = $this->urlName($this->ind_last_name);
            $urlLoc = ($this->type == 'Missionary') ?
                $this->urlName($this->missionary->field) :
                $this->urlName($this->ind_city);
        } else {
            $name = $this->urlName($this->org_name);
            $urlLoc = $this->urlName($this->org_city);
        }

        MailController::dbSendLink($this->id);                                                      // send link notifications to profile owners
        
        if ($this->category == self::CATEGORY_IND) {                                                // Update number of active individual profiles
            $user = Yii::$app->user->identity;
            $indProfiles = $user->ind_act_profiles + 1;
            $user->updateAttributes(['ind_act_profiles' => $indProfiles]);
        }

        $events = $this->history;                                                                   // Enter first timeline event as "Joined IBNet"
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

        $this->updateAttributes([
            'created_at' => $createDate, 
            'status' => self::STATUS_ACTIVE,
            'url_name' => $name,
            'url_loc' => $urlLoc]); 
        $this->setUpdateDate();
            
        return $this;
    }

    /**
     * Set profile status to "Inactive" 
     * Update last_update and renewal_date fields
     * @return
     */
    public function inactivate()
    {
        if ($progress = FormsCompleted::findOne($this->id)) {                                       // Delete progress
            $progress->delete();
        }
        if ($this->setUpdateDate() && 
            $this->updateAttributes(['status' => Profile::STATUS_INACTIVE, 'renewal_date' => NULL, 'edit' => self::EDIT_NO])) {

            if ($this->category = self::CATEGORY_IND) {                                             // Update number of active individual profiles
                $user = Yii::$app->user->identity;
                $indProfiles = $user->ind_act_profiles - 1;
                $user->updateAttributes(['ind_act_profiles' => $indProfiles]);
            }
            return true;
        }
        return false;
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
        //
        //

    // ************** Remove Link to Association/Fellowship ********************
        if ($this->type == 'Association') {
            $association = Association::findOne($this->ass_id);
            if (isset($association)) {
                $association->updateAttributes(['profile_id' => NULL]);
            }
        }
        if ($this->type == 'Fellowship')  {
            $fellowship = ProfileController::findFellowship($this->flwship_id);
            if (isset($fellowship)) {
                $fellowship->updateAttributes(['profile_id' => NULL]);   
            }         
        }

    // ******************* Remove Link to Mission Agency ***********************
        if ($this->type == 'Mission Agency') {                                                      // Remove Mission Agency profile link to mission agency table
            $agency = MissionAgcy::find()
                ->select('*')
                ->where(['profile_id' => $this->id])
                ->one();
            if (isset($agency)) {
                $agency->updateAttributes(['profile_id' => NULL]);
            }
        }

    // ********************* Remove Link to School *****************************
        if ($this->type == 'School') {                                                              // Remove School profile link to school table
            $school = School::find()
                ->select('*')
                ->where(['profile_id' => $this->id])
                ->one();
            if (isset($school)) {
                $school->updateAttributes(['profile_id' => NULL]);
            }
        }

    // ********************* Set Status to "trash" *****************************
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
    // *********************** Remove forms_completed***************************
        //
        //

    // *********************** Remove Link to Staff ****************************
        if ($this->staff_id != NULL) {
            $staff = Staff::findOne($this->staff_id);
            if (isset($staff)) {
                $staff->delete();
            }
        }

    // ******************* Remove Link to Service Time *************************
        if ($this->service_time_id != NULL) {
            $service = ServiceTime::findOne($this->service_time_id);
            if (isset($service)) {
                $service->delete();
            }
        }

    // ********************** Remove Link to Social ****************************
        if ($this->social_id != NULL) {
            $social = Social::findOne($this->social_id);
            if (isset($social)) {
                $social->delete();
            }
        }

    // ***************** Remove Link to Missions Housing ***********************
        if ($this->miss_housing_id != NULL) {
            $housing = MissHousing::findOne($this->miss_housing_id);
            if (isset($housing)) {
                $visibility = MissHousingVisibility::findOne($housing->visibility_id);              // Remove link to Mission Housing Visibility
                if (isset($visibility)) {
                    $visibility->delete();                                                          // Remove link to Missions Housing
                }
                $housing->delete();
            }
        }

    // ******************* Remove Link to Mission Agency ***********************
        if ($this->type == 'Church') {                                                              // Remove church approved mission agencies
            $agencies = ProfileHasMissionAgcy::find()
                ->select('*')
                ->where(['profile_id' => $this->id])
                ->all();
            if (isset($agencies)) {
                foreach ($agencies as $agency) {
                    $agency->delete();
                }
            }
        }

    // ********************* Remove Link to School *****************************
        if ($this->category == self::CATEGORY_IND) {                                                // Remove links to schools attended
            $schools = ProfileHasSchool::find()
                ->select('*')
                ->where(['profile_id' => $this->id])
                ->all();
            if (isset($schools)) {
                foreach ($schools as $school) {
                    $school->delete();
                }
            }
        }

    // ***************** Remove Link to School Levels **************************
        if ($this->type == 'School') {
            $levels = ProfileHasSchoolLevel::find()
                ->select('*')
                ->where(['profile_id' => $this->id])
                ->all();
            if (isset($levels)) {
                foreach ($levels as $level) {
                    $level->delete();
                }
            }
        }

    // ************************* Delete Record *********************************
        if ($this->delete()) {
            return true;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Return an array of profiles owned by the current user
     * @return array (of objects)
     */
    public function getProfileArray()
    {
        $id = Yii::$app->user->identity->id;
        return Profile::find()     
            ->select('id, type, category, profile_name, created_at, renewal_date, status')
            ->where(['user_id' => $id])
            ->andwhere('status != ' . self::STATUS_TRASH)
            ->orderBy('id ASC')
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceTime()
    {
        return $this->hasOne(ServiceTime::className(), ['id' => 'service_time_id']);
    }
 
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSocial()
    {
        return $this->hasOne(Social::className(), ['id' => 'social_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->viaTable('profile_has_tag', ['profile_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMinistryOf()
    {
        return $this->hasOne(Profile::className(), ['id' => 'ministry_of']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMinistry()
    {
        return $array = $this->find()->where(['ministry_of' => $this->id])->andWhere(['status' => self::STATUS_ACTIVE])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasMany(Profile::className(), ['id' => 'program_id'])
            ->viaTable('profile_has_program', ['profile_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChurches()
    {
        return $this->hasMany(Profile::className(), ['id' => 'profile_id'])
            ->viaTable('profile_has_program', ['program_id' => 'id'])
            ->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHomeChurch()
    {
        return $this->hasOne(Profile::className(), ['id' => 'home_church']);
    }

    /**
     * Links a profile to a list of approved mission agencies
     * @return \yii\db\ActiveQuery
     */
    public function getMissionAgcy()
    {
        return $this->hasMany(MissionAgcy::className(), ['id' => 'mission_agcy_id'])
            ->viaTable('profile_has_mission_agcy', ['profile_id' => 'id']);
    }

    /**
     * Links a profile to a list of approved mission agencies
     * @return \yii\db\ActiveQuery
     */
    public function getMissionary()
    {
        return $this->hasOne(Missionary::className(), ['id' => 'missionary_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMissHousing()
    {
        return $this->hasOne(MissHousing::className(), ['id' => 'miss_housing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchool()
    {
        return $this->hasMany(School::className(), ['id' => 'school_id'])
            ->viaTable('profile_has_school', ['profile_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolLevel()
    {
        return $this->hasMany(SchoolLevel::className(), ['id' => 'school_level_id'])
            ->viaTable('profile_has_school_level', ['profile_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFellowship()
    {
        return $this->hasMany(Fellowship::className(), ['id' => 'flwship_id'])
            ->viaTable('profile_has_fellowship', ['profile_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssociation()
    {
        return $this->hasMany(Association::className(), ['id' => 'ass_id'])
            ->viaTable('profile_has_association', ['profile_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistory()
    {
        return $this->hasMany(History::className(), ['profile_id' => 'id'])->where(['deleted' => 0])->orderBy('date ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccreditation()
    {
        return $this->hasMany(Accreditation::className(), ['id' => 'accreditation_id'])
            ->viaTable('profile_has_accreditation', ['profile_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLike()
    {
        return $this->hasMany(User::className(), ['id' => 'liked_by_id'])
            ->viaTable('profile_has_like', ['profile_id' => 'id'])
            ->where(['status' => User::STATUS_ACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getILike()
    {
        return $this->hasOne(ProfileHasLike::className(), ['profile_id' => 'id'])->where(['liked_by_id' => Yii::$app->user->identity->id]);
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

        }  elseif ($this->org_city && 
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
        if (($this->org_name == NULL) &&                                                            // NEED TO CREATE CONDITIONAL VALIDATOR TO REQUIRE ORG_NAME OR MISSIONAGENCY
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
            ->select('fellowship')
            ->where(['LIKE', 'fellowship', $this->name])
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
            ->select('fellowship_acronym')
            ->where(['LIKE', 'fellowship_acronym', $this->acronym])
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
            ->select('association')
            ->where(['LIKE', 'association', $this->aName])
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
            ->select('association_acronym')
            ->where(['LIKE', 'association_acronym', $this->aAcronym])
            ->one();
        if ($uniqueAcronym > NULL) {
             $this->addError($attribute, 'This acronym already exists. Select the association from the list above or choose a different acronym.');
            return false;
        }
        return $this->$attribute;
    }

    /**
     *  Update profile date fields: last_update, renewal_date
     * @return object
     */
    public function setUpdateDate()
    {  
        if ($this->status != self::STATUS_NEW) {
            if ($this->status != self::STATUS_ACTIVE || $this->requiredFields()) {
                $update = new Expression('CURDATE()');
                $renewal = new Expression('DATE_ADD(CURDATE(), INTERVAL 1 YEAR)');
                $this->updateAttributes(['last_update' => $update, 'renewal_date' => $renewal]);
            } 
        }
        return $this;
    }

    /**
     *  Update profile date fields: last_update, renewal_date
     * @return object
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
            if (!ProfileController::findActiveProfile($this->home_church)) {
                return false;
            }
            if ($this->type == 'Staff' && !ProfileController::findActiveProfile($this->ministry_of)) {
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
        $typeArray = [
            'Association',      
            'Camp',             
            'Church',           
            'Evangelist',       
            'Fellowship',       
            'Special Ministry',          
            'Mission Agency',          
            'Music Ministry',   
            'Pastor',
            'Missionary',
            'Chaplain',
            'Staff',
            'Print Ministry',
            'School',     
        ];

        return in_array($this->type, $typeArray);
    }

    /**
     * Returns true if profile type is a staff profile
     * @param string $id
     * @return boolean
     */
    public function isStaff($type)
    {
        $t = Type::find()->select('*')->where(['type' => $type])->one();
        return isset($t->staff) ? true : false;
    }

    /**
     * Create a forms array to track which forms have been completed.
     * @param string $id
     * @return Profile the loaded model
     */
    public static function createProgress($id)
    {
        
        $progress = new FormsCompleted();
        $progressArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];                    // Begin array to track which forms have been completed
        $progress->form_array = serialize($progressArray);
        $progress->id = $id;

        return $progress->save() ? $progress : false;
    }

    /**
     * Update $progressArray for a given form number to indicate form completion
     * 
     * @return string
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
     * set $formsArray for a given form number to indicate form completion
     * 
     * @return string
     */
    public function getProgress()
    {       
        return ($progress = FormsCompleted::findOne($this->id)) ?
            ($progressArray = unserialize($progress->form_array)) : false;
    }

    /**
     * set $formsArray for a given form number to indicate form completion
     * 
     * @return string
     */
    public function getProgressPercent($typeArray)
    {       
        if (!$progress = FormsCompleted::findOne($this->id)) {
            $progress = $this->createProgress($this->id);
        }
        $progressArray = unserialize($progress->form_array);
        $n = 100 * array_sum($progressArray) / (array_sum($typeArray)+1);                           // Add +1 to $typeArray to account for activation form

        return Utility::roundUpToAny($n);
    }

    /**
     * Return ind_names in format "First (& Spouse) Last" if spouse
     * or "First Last" if no spouse
     * 
     * @return string
     */
    public function getformattedNames()
    {
        if ($this->spouse_first_name != NULL) {
            $this->formattedNames = $this->ind_first_name . ' (& ' . $this->spouse_first_name . ') ' . $this->ind_last_name;
        } else {
            $this->formattedNames = $this->ind_first_name . ' ' . $this->ind_last_name;
        }
        return $this;
    }

    /**
     * Finds a senior pastor for a given church profile.
     * @param string $id
     * @return Profile the loaded model
     */
    public function findSrPastor()
    {
        if ($pastor = Staff::find()
            ->select('staff_id')
            ->where(['ministry_id' => $this->id])
            ->andwhere(['sr_pastor' => 1])
            ->one()) {
            return ProfileController::findActiveProfile($pastor->staff_id);
        }     
        return NULL;
    }

    /**
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function findModel($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Return appropriate parent ministry input label depending on profile type
     * @param string $id
     * @return Profile the loaded model
     */
    public function getMinistryLabel($type)
    {
        switch ($type) {
            case 'Evangelist':
                return $label = 'Ministering with';
            break;
            case 'Staff':
                return $label = 'On staff at';
            break;
            case 'Special Ministry':
                return $label = 'This ministry or organization is a ministry of';
            break;
            default:
                return $label = 'This ' . strtolower($this->type) . ' is a ministry of';
            break;
        }
    }

    /**
     * Return appropriate home church input label depending on profile type
     * @param string $id
     * @return Profile the loaded model
     */
    public function getChurchLabel($type, $sub_type)
    {
        switch ($type) {
            case 'Missionary':
                return $label = 'Sending Church:';
            break;
             case 'Pastor':
                return $label = $this->sub_type . ' at';
            break;
            default:
                return $label = 'Home Church:';
            break;
        }
    }

    /**
     * Return a url-friendly name
     * @param string $name
     * @return string
     */
    public function urlName($name)
    {
        return preg_replace("/[^a-zA-Z0-9-]/", "", str_replace(' ', '-', strtolower(trim($name))));
    }

    /**
     * Delete an image file on the server
     * @param string $name
     * @return string
     */
    public function deleteOldImg($img, $imageLink=NULL)
    {
        if ($img == 'image2' && 
            ($this->type == 'Church') && 
            ($pastorLink = $this->findSrPastor())) {
            if (isset($pastorLink->image2)) {
                $imageLink = $pastorLink->image2;
            }
        }

        $oldImg = $this->getOldAttribute($img);
        if ($oldImg && 
            (strpos($oldImg, '/uploads/') !== false) &&                                             // Only delete if image is found in uploads folder
            ($oldImg != $this->{$img}) &&                                                           // Only delte if image name and path has changed in db.
            ($oldImg != $imageLink)) {                                                              // Don't delete a linked pastor image
            unlink($oldImg);
        }
        return $this;
    }

}