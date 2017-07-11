<?php
namespace backend\controllers;

use common\models\profile\ProfileTracking;
use common\models\Utility;
use Yii;
use yii\bootstrap\Html;
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
        $statsArray = ProfileTracking::find()->all();
        foreach ($statsArray as $stat) {
            $types = unserialize($stat->type_array);
           // Utility::pp($stat->type_array);
        }
        //$total = ;
        return $this->render('stats');
    }

}
