<?php
namespace backend\controllers;

use common\models\Utility;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Accounts controller
 */
class DatabaseController extends Controller
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
     * 
     *
     * @return string
     */
    public function actionDb()
    {
        $db = Yii::$app->db->createCommand('
                SELECT 
                    TABLE_NAME AS "name", 
                    TABLE_ROWS AS "rows", 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "size"
                FROM information_schema.TABLES 
                WHERE table_schema = "dev"
                ORDER BY (data_length + index_length) DESC;
            ')->queryAll();

        $totalTables = count($db);
        $totalSize = array_sum(ArrayHelper::getColumn($db, 'size'));

        return $this->render('db', [
            'db' => $db, 
            'totalTables' => $totalTables, 
            'totalSize' => $totalSize
        ]);
    }

}
