<?php
namespace backend\controllers;

use backend\models\Stats;
use common\models\profile\ProfileTracking;
use common\models\Utility;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Accounts controller
 */
class StatsController extends Controller
{

    public static $typeCategory = [
           'Pastor' =>           'ind',
           'Evangelist' =>       'ind',
           'Missionary' =>       'ind', 
           'Chaplain' =>         'ind',
           'Staff' =>            'ind', 
           'Church' =>           'org',  
           'Mission Agency' =>   'org',  
           'Fellowship' =>       'org',  
           'Association' =>      'org',  
           'Camp' =>             'org',  
           'School' =>           'org',  
           'Print Ministry' =>   'org', 
           'Music Ministry' =>   'org',  
           'Special Ministry' => 'org',
        ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays Stats.
     *
     * @return string
     */
    public function actionStats()
    {
        // $total = [];
        // $org = [];
        // $ind = [];
        // $church = [];
        // $missionAgcy = [];
        // $flwship = [];
        // $ass = [];
        // $camp = [];
        // $school = [];
        // $print = [];
        // $music = [];
        // $special = [];
        // // $pastor = [];
        // $evangelist = [];
        // $missionary = [];
        // $chaplain = [];
        // $staff = [];
        // $statsArray = ProfileTracking::find()->limit(52)->orderBy('date Asc')->all();
        // foreach ($statsArray as $stat) {
        //     $countOrg = 0;
        //     $countInd = 0;
        //     $types = unserialize($stat->type_array);

        //     // Total
        //     $countTot = ArrayHelper::getcolumn($types, 'count');
        //     array_push($total, array_sum($countTot));

        //     // Orgs and Inds Totals
        //     foreach ($types as $type) {
        //         if (self::$typeCategory[$type['type']] == 'org') {
        //             $countOrg += $type['count'];
        //         } else {
        //             $countInd += $type['count'];
        //         }
        //     }
        //     array_push($org, $countOrg);
        //     array_push($ind, $countInd);

        //     // Profiles by type
        //     $typeCountArray = [
        //         'church' => NULL,
        //         'missionAgcy' => NULL,
        //         'flwship' => NULL,
        //         'ass' => NULL,
        //         'camp' => NULL,
        //         'school' => NULL,
        //         'print' => NULL,
        //         'music' => NULL,
        //         'special' => NULL,
        //         // 'pastor' => NULL,
        //         'evangelist' => NULL,
        //         'missionary' => NULL,
        //         'chaplain' => NULL,
        //         'staff' => NULL,
        //     ];
        //     foreach ($types as $type) {
        //         switch ($type['type']) {
        //             case 'Church':
        //                 array_push($church, $type['count']);
        //                 $typeCountArray['church'] = true;
        //                 break;
        //             case 'Mission Agency':
        //                 array_push($missionAgcy, $type['count']);
        //                 $typeCountArray['missionAgcy'] = true;
        //                 break;
        //             case 'Fellowship':
        //                 array_push($flwship, $type['count']);
        //                 $typeCountArray['flwship'] = true;
        //                 break;
        //             case 'Association':
        //                 array_push($ass, $type['count']);
        //                 $typeCountArray['ass'] = true;
        //                 break;
        //             case 'Camp':
        //                 array_push($camp, $type['count']);
        //                 $typeCountArray['camp'] = true;
        //                 break;
        //             case 'School':
        //                 array_push($school, $type['count']);
        //                 $typeCountArray['school'] = true;
        //                 break;
        //             case 'Print Ministry':
        //                 array_push($print, $type['count']);
        //                 $typeCountArray['print'] = true;
        //                 break;
        //             case 'Music Ministry':
        //                 array_push($music, $type['count']);
        //                 $typeCountArray['music'] = true;
        //                 break;
        //             case 'Special Ministry':
        //                 array_push($special, $type['count']);
        //                 $typeCountArray['special'] = true;
        //                 break;
        //             // case 'Pastor':
        //             //     array_push($pastor, $type['count']);
        //             //     $typeCountArray['pastor'] = true;
        //             //     break;
        //             case 'Evangelist':
        //                 array_push($evangelist, $type['count']);
        //                 $typeCountArray['evangelist'] = true;
        //                 break;
        //             case 'Missionary':
        //                 array_push($missionary, $type['count']);
        //                 $typeCountArray['missionary'] = true;
        //                 break;
        //             case 'Chaplain':
        //                 array_push($chaplain, $type['count']);
        //                 $typeCountArray['chaplain'] = true;
        //                 break;
        //             case 'Staff':
        //                 array_push($staff, $type['count']);
        //                 $typeCountArray['staff'] = true;
        //                 break;
                    
        //             default:
        //                 break;
        //         }
        //     }
        //     foreach ($typeCountArray as $key => $value) {                           // Set any zero count types to 0
        //         if ($value == NULL) {
        //             array_push(${$key}, 0);
        //         }
        //     }
        // }

        // $total = join($total, ',');
        // $org = join($org, ',');
        // $ind = join($ind, ',');

        // $church = join($church, ',');
        // $missionAgcy = join($missionAgcy, ',');
        // $flwship = join($flwship, ',');
        // $ass = join($ass, ',');
        // $camp = join($camp, ',');
        // $school = join($school, ',');
        // $print = join($print, ',');
        // $music = join($music, ',');
        // $special = join($special, ',');
        // // $pastor = join($pastor, ',');
        // $evangelist = join($evangelist, ',');
        // $missionary = join($missionary, ',');
        // $chaplain = join($chaplain, ',');
        // $staff = join($staff, ',');

        // $date = strtotime($statsArray[0]['date']);                                                  // Plot start date
        // $yr = date('Y', $date);
        // $mo = date('m', $date)-1;                                                                   // UTC month is zer-based
        // $dy = date('d', $date);
    
        return $this->render('stats', [
            // 'total' => $total,
            // 'org' => $org,
            // 'ind' => $ind,
            // 'church' => $church,
            // 'missionAgcy' => $missionAgcy,
            // 'flwship' => $flwship,
            // 'ass' => $ass,
            // 'camp' => $camp,
            // 'school' => $school,
            // 'print' => $print,
            // 'music' => $music,
            // 'special' => $special,
            // // 'pastor' => $pastor,
            // 'evangelist' => $evangelist,
            // 'missionary' => $missionary,
            // 'chaplain' => $chaplain,
            // 'staff' => $staff,
            // 'yr' => $yr,
            // 'mo' => $mo,
            // 'dy' => $dy,
        ]);
    }

}
