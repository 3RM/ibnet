<?php
namespace frontend\models;

use common\models\missionary\Missionary;
use common\models\profile\Profile;
use common\models\Utility;
use yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\db\Expression;

/**
 * Register form
 */
class Box3Content extends Model
{
   
   /**
     * Fetch latest added profile to display in box 3 on index.php
     * @return mixed
     */
    public function getBox3Content()
    {
        $session = Yii::$app->session;  

        $profiles = $session->get('profiles');
        $count = $session->get('count');
        $i = $session->get('i');

        if (!($profiles && $count && $i)) {
            $profiles = Profile::find()                                                             // Get new profiles for box 3 and add to session
                ->select('*')
                ->where(['status' => PROFILE::STATUS_ACTIVE])
                ->andwhere('created_at>DATE_SUB(NOW(), INTERVAL 14 DAY)')
                ->orderBy('created_at DESC')
                ->all();
            $count = count($profiles);
            $i = 0;

            $session->open('profiles');
            $session->open('count');
            $session->open('i');   
            $session->set('profiles', $profiles);
            $session->set('count', $count);
            $session->set('i', $i);
        }

        if ($count > 0) {    

            if ($i == ($count-1)) {
                $profile = $profiles[$i];
                $i = 0;
            } else {
                $profile = $profiles[$i];
                $i++;
            }

            $linkedProfile = NULL;
            if ($profile->type == 'Pastor') {
                $linkedProfile = Profile::findOne($profile->home_church);
            }

            $missionary = NULL;
            if ($profile->type == 'Missionary') {
                $missionary = Missionary::findOne($profile->missionary_id);
            }

            $staffMinistry = NULL;
            if ($profile->type == 'Staff') {
                $staffMinistry = Profile::findOne($profile->ministry_of);
            }
            
            $desc = preg_replace("/[^a-zA-Z0-9\s\.\,\?\!\"\-]/", "", $profile->description);

            $content =     Html::img('@images/content/new.png');

            switch ($profile->type) {

                case 'Association':
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/association',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</h4>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;
                
                case 'Camp' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/camp',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';              
                    break;

                case 'Chaplain' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->getFormattedNames()->formattedNames, ['profile/chaplin',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->type;
                    $content .=     ' &#8226 ';
                    $content .=     $profile->ind_city;
                    $content .=     ', ';
                    $content .=     empty($profile->ind_st_prov_reg) ? NULL : $profile->ind_st_prov_reg;
                    $content .=     $profile->ind_country == 'United States' ? '' : ', ' . $profile->ind_country;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Church' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/church',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     ' &#8226 ';
                    $content .=     'Pastor ' . $profile->getFormattedNames()->formattedNames;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';              
                    break;

                case 'Evangelist' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->getFormattedNames()->formattedNames, ['profile/evangelist',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->type;
                    $content .=     ' &#8226 ';
                    $content .=     $profile->ind_city;
                    $content .=     ', ';
                    $content .=     $profile->ind_st_prov_reg;
                    $content .=     $profile->ind_country == 'United States' ? '' : ', ' . $profile->ind_country;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Fellowship' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/fellowship',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Special Ministry' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/special-ministry',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Mission Agency' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/mission-agency',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Missionary' :
                    $names = isset($profile->spouse_first_name) ?
                        ($profile->ind_first_name . ' & ' . $profile->spouse_first_name . ' ' . $profile->ind_last_name) :
                        ($profile->ind_first_name . ' ' . $profile->ind_last_name);
                    $content .= '<h3>';
                    $content .=     Html::a($names, ['profile/missionary',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    if ($missionary) {
                        $content .= '<p>';
                        $content .=     $profile->spouse_first_name == NULL ? 
                                            'Missionary to ' . $missionary->field . ', Status: ' . $missionary->status :
                                            'Missionaries to ' . $missionary->field . ', Status: ' . $missionary->status;
                        $content .= '</p>';
                    }
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Music Ministry' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/music',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->type;
                    $content .=     ' &#8226 ';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Pastor' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->getFormattedNames()->formattedNames, ['profile/pastor',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    if ($linkedProfile) {
                        $content .= '<p>';
                        $content .=     $profile->sub_type;
                        $content .=     ' &#8226 ';
                        $content .=     $linkedProfile->org_name . ', ' . $linkedProfile->org_city;
                        $content .=     empty($linkedProfile->org_st_prov_reg) ? NULL : ', ' . $linkedProfile->org_st_prov_reg;
                        $content .=     $linkedProfile->org_country == 'United States' ? '' : ', ' . $linkedProfile->org_country;
                        $content .= '</p>';
                    }
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Print Ministry' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/print',
                                        'url_loc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->type;
                    $content .=     ' &#8226 ';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                   $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'School' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/school',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                case 'Staff' :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->getFormattedNames()->formattedNames, ['profile/staff',
                                        'urlLoc' => $profile->url_loc, 
                                        'name' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    if ($staffMinistry) {
                        $content .= '<p>';
                        $content .=     $profile->title;
                        $content .=     ' at ';
                        $content .=     $staffMinistry->org_name;
                        $content .=     ', ';
                        $content .=     $profile->ind_city;
                        $content .=     ', ';
                        $content .=     empty($profile->ind_st_prov_reg) ? NULL : $profile->ind_st_prov_reg;
                        $content .=     $profile->ind_country == 'United States' ? '' : ', ' . $profile->ind_country;
                        $content .= '</p>';
                    }
                    $content .= '<p>' . Utility::trimText($desc, 50) . '</p>';
                    break;

                default:
                    break;
            }
       
        } else {

            $i = 0;
            $content  = '<h3>Create a Profile for your Church or Ministry</h3>';
            $content .= '<p>Take advantage of all the benefits of IBNet. Simply register to start creating your profile now.  It\'s is easy and free.</p>';       
        }

        $session->set('i', $i);

        return $content;
    }

}
