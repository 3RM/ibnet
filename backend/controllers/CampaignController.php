<?php
namespace backend\controllers;

use common\models\Utility;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Accounts controller
 */
class CampaignController extends Controller
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
    public function actionMailchimp()
    {
        if (Yii::$app->request->post()) {
            $user = Yii::$app->user->identity;
            $user->refreshFeatureList('8b62bd54b8');        // New Feature at IBNet
        }

        return $this->render('mailchimp');
    }

}
