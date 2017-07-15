<?php
namespace backend\controllers;

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
        $statsArray = ProfileTracking::find()->limit(52)->orderBy('date Asc')->all();
        $total = [];
        foreach ($statsArray as $stat) {
            $statsArray['typeArray'] = unserialize($stat->type_array);
            $types = unserialize($stat->type_array); //Utility::pp($statsArray);
            $countCol = ArrayHelper::getcolumn($types, 'count');
            array_push($total, array_sum($countCol));
        }

        $total = join($total, ',');

        $date = strtotime($statsArray[0]['date']);                                                  // Plot start date
        $yr = date('Y', $date);
        $mo = date('m', $date)-1;                                                                   // UTC month is zer-based
        $dy = date('d', $date);
    
        return $this->render('stats', [
            'total' => $total,
            'yr' => $yr,
            'mo' => $mo,
            'dy' => $dy,
        ]);
    }

}
