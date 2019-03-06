<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\profile\Profile;
use common\models\profile\ProfileBrowse; use common\models\Utility;
use frontend\models\GeoCoder;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * FacetController implements the browse facet actions.
 */
class FacetController extends Controller
{

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
     * Action for Ajax request on browse page.
     *
     * @return mixed
     */
    public function actionFacet($constraint, $cat)
    {
        // Retrieve user selections from session
        $session = Yii::$app->session; 
        
        $browseModel = new ProfileBrowse();
        $browseModel->scenario = 'browse';
        $query = $browseModel->query();
        $fqs = $session->get('fqs');
        $spatial = $session->get('spatial'); 

        if ($constraint) {
            $select = explode('+', $constraint);
            $con = $select[0];
            $toggle = $select[1];
        }

        // User cleared spatial search
        if (isset($_POST['clear'])) {
            $spatial = [
                'distance' => 60,
                'location' => NULL,
                'lat'   => NULL,
                'lng'   => NULL
            ];

        // User performed spatial browse
        } elseif ($browseModel->load(Yii::$app->request->post()) && $browseModel->validate()) {
            $spatial = [
                'distance' => $browseModel->distance,
                'location' => $browseModel->location,
                'lat'   => NULL,
                'lng'   => NULL,
            ];

        // Add constraint choices
        } elseif (isset($con) && isset($cat)) {
            // If constraint is in $fqs, remove it
            switch ($cat) {
                case 'type':
                    if (isset($fqs['type']['type']) && $fqs['type']['type'] == $con && $toggle == 'u') { 
                        unset($fqs['type']);
                    // If constraint is not in $fqs, add it
                    } elseif ($toggle == 's') {
                        $fqs['type']['type'] = $con;
                    }
                    break;
                case 'program':
                     if (isset($fqs['type']['program']) && $fqs['type']['program'] == $con && $toggle == 'u') { 
                        unset($fqs['type']['program']);
                    } elseif ($toggle == 's') {                                                                     
                        $fqs['type']['program'] = $con;
                    }
                    break;
                case 'tag':
                     if (isset($fqs['type']['tag']) && $fqs['type']['tag'] == $con && $toggle == 'u') { 
                        unset($fqs['type']['tag']);
                    } elseif ($toggle == 's') {                                                                     
                        $fqs['type']['tag'] = $con;
                    }
                    break;
                case 'country': // Pattern: 'country' => ['country' => '', 'state' => ['state' => '', 'city' => '']]
                    if (isset($fqs['country']['country']) && $fqs['country']['country'] == $con && $toggle == 'u') {
                        unset($fqs['country']);
                    } elseif ($toggle == 's') {
                        $fqs['country'] = ['country' => $con];
                    }
                    break;
                case 'state':
                    if (isset($fqs['country']['state']['state']) && $fqs['country']['state']['state'] == $con && $toggle == 'u') { 
                        unset($fqs['country']['state']);
                    } elseif ($toggle == 's') {
                        $fqs['country']['state'] = ['state' => $con];
                    }
                    break;
                case 'city':
                    if (isset($fqs['country']['state']['city']) && $fqs['country']['state']['city'] == $con && $toggle == 'u') {
                        unset($fqs['country']['state']['city']);
                    } elseif ($toggle == 's') {
                         $fqs['country']['state']['city'] = $con;
                    }
                    break;
                case 'miss_status':
                    if (isset($fqs['type']['miss_status']) && $fqs['type']['miss_status'] == $con && $toggle == 'u') {
                        unset($fqs['type']['miss_status']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['miss_status'] = $con;
                    }
                    break;
                case 'miss_field':
                    if (isset($fqs['type']['miss_field']) && $fqs['type']['miss_field'] == $con && $toggle == 'u') {
                        unset($fqs['type']['miss_field']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['miss_field'] = $con;
                    }
                    break;
                case 'miss_agcy':
                    if (isset($fqs['type']['miss_agcy']) && $fqs['type']['miss_agcy'] == $con && $toggle == 'u') {
                        unset($fqs['type']['miss_agcy']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['miss_agcy'] = $con;
                    }
                    break;
                case 'level':
                    if (isset($fqs['type']['level']) && $fqs['type']['level'] == $con && $toggle == 'u') {
                        unset($fqs['type']['level']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['level'] = $con;
                    }
                    break;
                case 'sub_type':   // Sub type for pastors and missionaries
                    if (isset($fqs['type']['sub_type']) && $fqs['type']['sub_type'] == $con && $toggle == 'u') {
                        unset($fqs['type']['sub_type']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['sub_type'] = $con;
                    }
                    break;
                case 'title':
                    if (isset($fqs['type']['title']) && $fqs['type']['title'] == $con && $toggle == 'u') {
                        unset($fqs['type']['title']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['title'] = $con;
                    }
                    break;
                case 'bible':
                    if (isset($fqs['type']['bible']) && $fqs['type']['bible'] == $con && $toggle == 'u') {
                        unset($fqs['type']['bible']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['bible'] = $con;
                    }
                    break;
                case 'worship_style':
                    if (isset($fqs['type']['worship_style']) && $fqs['type']['worship_style'] == $con && $toggle == 'u') {
                        unset($fqs['type']['worship_style']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['worship_style'] = $con;
                    }
                    break;
                case 'polity':
                    if (isset($fqs['type']['polity']) && $fqs['type']['polity'] == $con && $toggle == 'u') {
                        unset($fqs['type']['polity']);
                    } elseif ($toggle == 's') {
                         $fqs['type']['polity'] = $con;
                    }
                    break;
                default:
                    break;
            } 
            $session->set('fqs', $fqs);
        }

        // Populate browseModel from session or POST
        $browseModel->distance = isset($spatial['distance']) ? $spatial['distance'] : 60;
        $browseModel->location = $spatial['location'];
        $browseModel->lat = $spatial['lat'];
        $browseModel->lng = $spatial['lng'];

    // =================== Set spatial search fq =======================
        // Add geo filterquery in the event of user spatial browse
        if ($browseModel->distance && $browseModel->location) {
            if (empty($browseModel->lat) || empty($browseModel->lng)) {
                $res = GeoCoder::getCoordinates($browseModel->location, Yii::$app->params['apiKey.Google-no-restrictions'], true);
                $spatial['lat'] = $res['lat'];
                $spatial['lng'] = $res['lng'];
            }
            
            // get the dismax component and set a boost query
            $sp = $query->getSpatial();
            $sp->setPoint(doubleval($spatial['lat']) . ',' . doubleval($spatial['lng']));
            $sp->setField('org_loc');
            $sp->setDistance($browseModel->distance);

            // apply 'geofilt' filtering
            $query->createFilterQuery('org_loc')->setQuery('{!geofilt}');

            // add distance to results fields
            $query->addField('_distance_:geodist()');

            // apply sorting by distance
            $query->addSort('geodist()', 'ASC');
      
        }
        $session->set('spatial', $spatial);

    // =================== Set facet fqs =======================
        if (isset($fqs)) {
            foreach ($fqs as $fq) {
                if (is_array($fq)) {
                    foreach ($fq as $fq1) {
                        if (is_array($fq1)) {
                            foreach ($fq1 as $fq2) {
                                $field = explode(':', $fq2);
                                $query->createFilterQuery($field[0])->setQuery($fq2);
                            }
                        } else {
                            $field = explode(':', $fq1);
                            $query->createFilterQuery($field[0])->setQuery($fq1);
                        }
                    }
                } else {
                    $field = explode(':', $fq);
                    $query->createFilterQuery($field[0])->setQuery($fq);
                }
            }
        }

    // ================ Commit query and fetch results ===================
        $dataProvider = $browseModel->dataProvider($query);
        $resultSet = $browseModel->resultSet($query);

    // ================ Map Markers ===================
        $center = NULL;
        $markers[] = NULL;
        if (!empty($_SESSION['spatial']['distance']) && !empty($_SESSION['spatial']['location'])) {
            $center = '{lat: ' . $spatial['lat'] . ', lng: ' . $spatial['lng'] . '}';
            foreach ($resultSet as $i=>$doc) {
                //if ($doc->category == Profile::CATEGORY_IND) {continue;}
                if ($doc->org_loc) { 
                    $latlng = explode(',', $doc->org_loc);
                    $markers[$i][0] = $doc->org_name;
                } else {
                    $latlng = explode(',', $doc->ind_loc);
                    $markers[$i][0] = $doc->ind_name;
                }
                $markers[$i][1] = $latlng[0];
                $markers[$i][2] = $latlng[1];
                if ($i == 10) {break;}
            }
        }

    // =================== Toggle more/less button =======================
        $more = $session->get('more');
        // If session has expired and any elements are missing from $more, reset browse
        if (!isset($more) || (count($more) < 12)) {
            return $this->redirect(['/profile/browse']);
        }  
        if ($constraint == false && !empty($cat)) {
            $more[$cat] == 1 ? $more[$cat] = 2 : $more[$cat] = 1;
            $session->set('more', $more);
        }

        return $this->render('/profile/browse', [
            'fqs' => $fqs,
            'browseModel' => $browseModel,
            'more' => $more,
            'resultSet' => $resultSet,
            'dataProvider' => $dataProvider,
            'center' => $center,
            'markers' => $markers
        ]);
    }
}                     