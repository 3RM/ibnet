<?php
namespace backend\controllers;

use common\models\Utility;
use console\models\CronJob;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Accounts controller
 */
class ServerController extends Controller
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
     * PHP Info
     *
     * @return string
     */
    public function actionPhpinfo()
    {
        return $this->render('phpinfo');
    }

    /**
     * Cron job log
     *
     * @return string
     */
    public function actionCron()
    {
        //$searchModel = new CSearch();

        $query = CronJob::find()->orderBy(['id_cron_job' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $gridColumns = [
            'id_cron_job',
            'controller',
            'action',
            'limit',
            'offset',
            'running',
            'success',
            [
                'attribute' => 'started_at',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return Yii::$app->formatter->asDate($model->started_at, 'php:Y-m-d, g:i a');
                },
            ],
            [
                'attribute' => 'ended_at',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return Yii::$app->formatter->asDate($model->ended_at, 'php:Y-m-d, g:i a');
                },
            ],
            [
                'attribute' => 'last_execution_time',
                'format' => 'raw',
                'value' => function ($model) {                      
                    return isset($model->last_execution_time) ? Yii::$app->formatter->asDecimal($model->last_execution_time, 5) * 1000 . ' ms' : NULL;
                },
            ],
        ];

        return $this->render('cron', [
            'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

}
