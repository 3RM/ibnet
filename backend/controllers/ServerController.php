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
        $query = CronJob::find()->limit(63)->orderBy(['id_cron_job' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
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
            // 'searchModel' => $searchModel, 
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
        ]);
    }

    /**
     * Yii Cache
     *
     * @return string
     */
    public function actionCache()
    {

        if (isset($_POST['backendClear'])) {
            $sizeBefore = $this->getDirectorySize(Yii::$app->cache->cachePath);
            Yii::$app->cache->flush();
            $sizeAfter =  $this->getDirectorySize(Yii::$app->cache->cachePath);
            
            Yii::$app->session->setFlash('success', 
                'Yii backend cache flushed successfully. Size before: ' . $sizeBefore . 'B. Size after: ' . $sizeAfter . 'B.');
        
        } elseif (isset($_POST['frontendClear'])) {
            $sizeBefore =  $this->getDirectorySize(Yii::$app->cacheFrontend->cachePath);
            Yii::$app->cacheFrontend->flush();
            $sizeAfter =  $this->getDirectorySize(Yii::$app->cacheFrontend->cachePath);
            
            Yii::$app->session->setFlash('success', 
                'Yii frontend cache flushed successfully. Size before: ' . $sizeBefore . 'B. Size after: ' . $sizeAfter . 'B.');
        
        } elseif (isset($_POST['authClear'])) {
            $manager = Yii::$app->authManager;
            $manager->invalidateCache();

            Yii::$app->session->setFlash('success', 'Yii auth manager cache cleared successfully.');
        }

        return $this->render('cache');
    }

    /**
     * Get size of a server directory
     * 
     * @param mixed $dir
     * @return boolean TRUE if it is a valid string. FALSE if it isn't.
     */
    public static function getDirectorySize($dir)
    {
        $dir = rtrim(str_replace('\\', '/', $dir), '/');
    
        if (is_dir($dir) === true) {
            $totalSize = 0;
            $os        = strtoupper(substr(PHP_OS, 0, 3));
            // If on a Unix Host (Linux, Mac OS)
            if ($os !== 'WIN') {
                $io = popen('/usr/bin/du -sb ' . $dir, 'r');
                if ($io !== false) {
                    $totalSize = intval(fgets($io, 80));
                    pclose($io);
                    return $totalSize;
                }
            }
            // If on a Windows Host (WIN32, WINNT, Windows)
            if ($os === 'WIN' && extension_loaded('com_dotnet')) {
                $obj = new \COM('scripting.filesystemobject');
                if (is_object($obj)) {
                    $ref       = $obj->getfolder($dir);
                    $totalSize = $ref->size;
                    $obj       = null;
                    return $totalSize;
                }
            }
            // If System calls did't work, use slower PHP 5
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }
            return $totalSize;
        } else if (is_file($dir) === true) {
            return filesize($dir);
        }
    }

}
