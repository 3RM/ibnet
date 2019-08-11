<?php
namespace frontend\models;

use frontend\controllers\ProfileController;
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
                 ->where(['status' => PROFILE::STATUS_ACTIVE, 'has_been_inactivated' => NULL])
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
            if ($profile->type == Profile::TYPE_PASTOR) {
                $linkedProfile = $profile->homeChurch;
            }

            $missionary = NULL;
            if ($profile->type == Profile::TYPE_MISSIONARY) {
                $missionary = $profile->missionary;
            }

            $staffMinistry = NULL;
            if ($profile->type == Profile::TYPE_STAFF) {
                $staffMinistry = $profile->parentMinistry;
            }
            
            $desc = preg_replace("/[^a-zA-Z0-9\s\.\,\?\!\"\-]/", "", $profile->description);

            $content =     Html::img('@img.site/new.png');

            switch ($profile->type) {

                case Profile::TYPE_ASSOCIATION:
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</h4>';
                    break;
                
                case Profile::TYPE_CAMP :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';             
                    break;

                case Profile::TYPE_CHAPLAIN :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->mainName, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
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
                    break;

                case Profile::TYPE_CHURCH :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     ' &#8226 ';
                    $content .=     'Pastor ' . $profile->mainName;
                    $content .= '</p>';            
                    break;

                case Profile::TYPE_EVANGELIST :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->mainName, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
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
                    break;

                case Profile::TYPE_FELLOWSHIP :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    break;

                case Profile::TYPE_SPECIAL :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    break;

                case Profile::TYPE_MISSION_AGCY :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    break;

                case Profile::TYPE_MISSIONARY :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->coupleName, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    if ($missionary) {
                        $content .= '<p>';
                        $content .=     $profile->spouse_first_name == NULL ? 
                                            'Missionary to ' . $missionary->field . ', Status: ' . $missionary->status :
                                            'Missionaries to ' . $missionary->field . ', Status: ' . $missionary->status;
                        $content .= '</p>';
                    }
                    break;

                case Profile::TYPE_MUSIC :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
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
                    break;

                case Profile::TYPE_PASTOR :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->mainName, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
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
                    break;

                case Profile::TYPE_PRINT :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'url_loc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
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
                    break;

                case Profile::TYPE_SCHOOL :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->org_name, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    $content .= '<p>';
                    $content .=     $profile->org_city;
                    $content .=     ', ';
                    $content .=     empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=     $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .= '</p>';
                    break;

                case Profile::TYPE_STAFF :
                    $content .= '<h3>';
                    $content .=     Html::a($profile->mainName, ['profile/' . ProfileController::$profilePageArray[$profile->type],
                                        'urlLoc' => $profile->url_loc, 
                                        'urlName' => $profile->url_name, 
                                        'id' => $profile->id]);
                    $content .= '</h3>';
                    if ($staffMinistry) {
                        $content .= '<p>';
                        $content .=     $profile->title;
                        $content .=     ' at ';
                        $content .=     $staffMinistry->org_name;
                        $content .=     ', ';
                        $content .=     $staffMinistry->org_city;
                        $content .=     ', ';
                        $content .=     empty($staffMinistry->org_st_prov_reg) ? NULL : $staffMinistry->org_st_prov_reg;
                        $content .=     $staffMinistry->org_country == 'United States' ? '' : ', ' . $staffMinistry->org_country;
                        $content .= '</p>';
                    }
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
