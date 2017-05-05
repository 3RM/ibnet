<?php
namespace frontend\models;

use common\models\profile\Missionary;
use common\models\profile\Profile;
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

            switch ($profile->type) {

                case 'Association':
                    $content = Html::a(Html::img('@web/images/association-new.jpg', ['class' => 'img-thumbnail']), ['profile/association',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/association',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;
                
                case 'Camp' :
                    $content = Html::a(Html::img('@web/images/camp-new.jpg', ['class' => 'img-thumbnail']), ['profile/camp',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/camp',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';               
                    break;

                case 'Chaplain' :
                    $content = Html::a(Html::img('@web/images/chaplain-new.jpg', ['class' => 'img-thumbnail']), ['profile/chaplain',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->getFormattedNames()->formattedNames, ['profile/chaplin',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->type;
                    $content .=         ', ';
                    $content .=         $profile->ind_city;
                    $content .=         ', ';
                    $content .=         empty($profile->ind_st_prov_reg) ? NULL : $profile->ind_st_prov_reg;
                    $content .=         $profile->ind_country == 'United States' ? '' : ', ' . $profile->ind_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'Church' :

                    $content = Html::a(Html::img('@web/images/church-new.jpg', ['class' => 'img-thumbnail']), ['profile/church',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/church',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>Pastor ' . $profile->getFormattedNames()->formattedNames . '</p>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';               
                    break;

                case 'Evangelist' :
                    $content = Html::a(Html::img('@web/images/evangelist-new.jpg', ['class' => 'img-thumbnail']), ['profile/evangelist',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->getFormattedNames()->formattedNames, ['profile/evangelist',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->ind_city;
                    $content .=         ', ';
                    $content .=         $profile->ind_st_prov_reg;
                    $content .=         $profile->ind_country == 'United States' ? '' : ', ' . $profile->ind_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'Fellowship' :
                    $content = Html::a(Html::img('@web/images/fellowship-new.jpg', ['class' => 'img-thumbnail']), ['profile/fellowship',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/fellowship',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'Special Ministry' :
                    $content = Html::a(Html::img('@web/images/special-new.jpg', ['class' => 'img-thumbnail']), ['profile/special-ministry',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/special-ministry',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                $content .= '</div>';
                break;

                case 'Mission Agency' :
                    $content = Html::a(Html::img('@web/images/mission-new.jpg', ['class' => 'img-thumbnail']), ['profile/mission-agency',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/mission-agency',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'Missionary' :
                    $names = isset($profile->spouse_first_name) ?
                        ($profile->ind_first_name . ' & ' . $profile->spouse_first_name . ' ' . $profile->ind_last_name) :
                        ($profile->ind_first_name . ' ' . $profile->ind_last_name);
                    $content = Html::a(Html::img('@web/images/missionary-new.jpg', ['class' => 'img-thumbnail']), ['profile/missionary',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($names, ['profile/missionary',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    if ($missionary) {
                        $content .=     '<h4>';
                        $content .=         $profile->spouse_first_name == NULL ? 
                                                'Missionary to ' . $missionary->field . ', Status: ' . $missionary->status :
                                                'Missionaries to ' . $missionary->field . ', Status: ' . $missionary->status;
                        $content .=     '</h4>';
                    }
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'Music Ministry' :
                    $content = Html::a(Html::img('@web/images/music-new.jpg', ['class' => 'img-thumbnail']), ['profile/music',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/music',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'Pastor' :
                    $content = Html::a(Html::img('@web/images/pastor-new.jpg', ['class' => 'img-thumbnail']), ['profile/pastor',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->getFormattedNames()->formattedNames, ['profile/pastor',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    if ($linkedProfile) {
                        $content .=     '<h4>';
                        $content .=         $linkedProfile->org_name . ', ' . $linkedProfile->org_city;
                        $content .=         empty($linkedProfile->org_st_prov_reg) ? NULL : ', ' . $linkedProfile->org_st_prov_reg;
                        $content .=         $linkedProfile->org_country == 'United States' ? '' : ', ' . $linkedProfile->org_country;
                        $content .=     '</h4>';
                    }
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'Print Ministry' :
                    $content = Html::a(Html::img('@web/images/print-new.jpg', ['class' => 'img-thumbnail']), ['profile/print',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/print',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'School' :
                    $content = Html::a(Html::img('@web/images/school-new.jpg', ['class' => 'img-thumbnail']), ['profile/print',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->org_name, ['profile/print',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    $content .=     '<h4>';
                    $content .=         $profile->org_city;
                    $content .=         ', ';
                    $content .=         empty($profile->org_st_prov_reg) ? NULL : $profile->org_st_prov_reg;
                    $content .=         $profile->org_country == 'United States' ? '' : ', ' . $profile->org_country;
                    $content .=     '</h4>';
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                case 'Staff' :
                    $content = Html::a(Html::img('@web/images/staff-new.jpg', ['class' => 'img-thumbnail']), ['profile/staff',
                        'city' => $profile->url_city, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id]);
                    $content .= '<div class="caption">';
                    $content .=     '<h3>';
                    $content .=         Html::a($profile->getFormattedNames()->formattedNames, ['profile/staff',
                                            'city' => $profile->url_city, 
                                            'name' => $profile->url_name, 
                                            'id' => $profile->id]);
                    $content .=     '</h3>';
                    if ($staffMinistry) {
                        $content .=     '<h4>';
                        $content .=         $profile->title;
                        $content .=         ' at ';
                        $content .=         $staffMinistry->org_name;
                        $content .=         ', ';
                        $content .=         $profile->ind_city;
                        $content .=         ', ';
                        $content .=         empty($profile->ind_st_prov_reg) ? NULL : $profile->ind_st_prov_reg;
                        $content .=         $profile->ind_country == 'United States' ? '' : ', ' . $profile->ind_country;
                        $content .=     '</h4>';
                    }
                    $content .=     '<p>' . substr($desc, 0, 150) . '... </p>';
                    $content .= '</div>';
                    break;

                default:
                    break;
            }
       
        } else {

            $i = 0;
            $content = Html::img('@web/images/join.jpg', ['class' => 'img-thumbnail']);
            $content .= '<div class="caption">';
            $content .=     '<h3>Need a profile?  Create one!</h3>';
            $content .=     '<p>Creating a profile for you or your ministry is easy and free.  Simply register to start creating your profile now.</p>';
            $content .= '</div>';
            $content .= '<p class="center">' . Html::a('Get Started &#187', ['site/register'], ['class' => 'btn btn-home', 'role' => 'button', 'style' => 'background-color: green']) . '</p>';
        
        }

        $session->set('i', $i);

        return $content;
    }

}
