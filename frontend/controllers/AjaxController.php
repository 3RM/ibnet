<?php
namespace frontend\controllers;

use common\models\profile\Mail;
use common\models\profile\Profile;
use common\models\profile\Social;
use common\models\Utility;
use frontend\controllers\ProfileFormController;
use frontend\models\Box3Content;
use Yii;
use yii\base\Security;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;

/**
 * Ajax controller
 */
class AjaxController extends Controller
{

    /**
     * Retrieve next new profile for content box 3 on index page.
     *
     * @return mixed
     */
    public function actionNext()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $content = new Box3Content();
            $box3Content = $content->getBox3Content();

            $response = array(
                'body' => $box3Content,
                'success' => true,
            );
            return $response;
        }
    }

    /**
     * Process request for forwarding email on form4 modal
     */
    public function actionForwarding($id) 
    { 
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            // $fmNum = ProfileFormController::$form['co'];
            $profile = ProfileFormController::findProfile($id);
            $profile->scenario = 'co-fe';

            if (!$social = $profile->social) {
                $social = new Social();
            } 

            if(!($social->load(Yii::$app->request->Post()) &&
                $social->validate() &&
                $social->save())) {
                
                return [
                    'body' => 'Oops!  It looks like you need to fix an error with your social media entries first.',
                    'success' => true,
                ];
            }

            if (!($profile->load(Yii::$app->request->Post()) && 
                $profile->validate())) {

                if ($profile->email_pvt == NULL) {

                    return [
                        'body' => 'A private email is required.',
                        'success' => false,
                    ];
                } else {

                    return [
                        'body' => 'Oops!  It looks like you need to fix an error with your contact information first.',
                        'success' => true,
                    ];
                }
            }

            if ($profile->email_pvt_status == Profile::PRIVATE_EMAIL_PENDING) {

                Yii::$app->session->setFlash('success', 
                    'Your new ibnet.org address has status <em>pending</em>.  Please allow 48 hours for it to become active.  
                    Or '. Html::a('contact us', ['site/contact'], ['target' => '_blank']) . ' regarding any questions with 
                    this form or your new email.');
                
                return [
                    'body' => '',
                    'success' => true,
                ];
            }

            $profile->isIndividual($profile->type) ?
                $profile->email = Profile::urlName($profile->ind_last_name) . $profile->id . '@ibnet.org' :
                $profile->email = Profile::urlName($profile->org_name) . $profile->id . '@ibnet.org';

            $profile->email_pvt_status = Profile::PRIVATE_EMAIL_PENDING;
            $profile->save();
            
            if ($profile->email_pvt && 
                $profile->email_pvt_status === Profile::PRIVATE_EMAIL_PENDING &&
                Mail::sendForwardingEmailRqst($id, $profile->email, $profile->email_pvt)) {
    
                Yii::$app->session->setFlash('success', 
                    'Your new email is pending and should be visible on your profile within 48 hours.  
                    You may proceed with creating or updating your profile.');
            }

            return [
                    'body' => '',
                    'success' => true,
                ];

        }
    }
}
