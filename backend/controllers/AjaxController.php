<?php
/**
 *  Controller class for all ajax requests
 * 
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace backend\controllers;

use Yii;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\Response;

/**
 * Ajax controller
 */
class AjaxController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\AjaxFilter',
            ],
        ];
    }




    // ************************** Site *******************************

    /**
     * View User (activates user detail modal in callback
     * @param integer $id User id
     * @return array
     */
    // public function actionViewUser($uid)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     return [
    //         'uid' => $uid,
    //         'success' => true,
    //     ];
    // }
    



    // ************************** Profiles *******************************
  
    /**
     * View User (activates user detail modal in callback
     * @param integer $id User id
     * @return array
     */
    // public function actionViewProfile($pid)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     return [
    //         'uid' => $pid,
    //         'success' => true,
    //     ];
    // }

}