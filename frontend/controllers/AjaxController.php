<?php
namespace frontend\controllers;

use common\models\profile\Mail;
use common\models\profile\Profile;
use common\models\profile\Social;
use common\models\Utility;
use common\models\User;
use frontend\controllers\ProfileFormController;
use frontend\models\Box3Content;
use Yii;
use yii\base\Security;
use yii\bootstrap\Html;
use yii\web\Controller;
use yii\web\Response;

/**
 * Ajax controller
 */
class AjaxController extends Controller
{

    /**
     * Process "like" link.
     *
     * @return mixed
     */
    public function actionLike($iLike, $likeCount, $pid)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if (!Yii::$app->user->isGuest) {
                $profile = Profile::findOne($pid);
                $user = User::findOne(Yii::$app->user->identity->id);
                if ($iLike) {
                    $profile->unlink('like', $user, $delete = true);
                    $iLike = false;
                    $likeCount--;
                    $likes = $likeCount > 0 ? '<span class="badge">' . $likeCount . '</span>' : NULL;
                    $body = $likes ?
                        $likes . Html::a(Html::icon('heart'), ['ajax/like', 'iLike' => $iLike, 'likeCount' => $likeCount, 'pid' => $profile->id], [
                            'id' => 'like-id', 
                            'data-on-done' => 'likeDone', 
                            'class' => 'ind-icon']) :
                        Html::a(Html::icon('heart-empty'), ['ajax/like', 'iLike' => $iLike, 'likeCount' => $likeCount, 'pid' => $profile->id], [
                            'id' => 'like-id', 
                            'data-on-done' => 'likeDone', 
                            'class' => 'ind-icon']);
                } else {
                    $profile->link('like', $user);
                    $iLike = true;
                    $likeCount++;
                    $body = '<span class="badge">' . $likeCount . '</span>' . Html::a(Html::icon('heart'), ['ajax/like', 'iLike' => $iLike, 'likeCount' => $likeCount, 'pid' => $profile->id], [
                        'id' => 'like-id', 
                        'data-on-done' => 'likeDone', 
                        'class' => 'ind-icon heart']);
                    Mail::sendLike($profile, $user);
                }
            }

            $response = array(
                'body' => $body,
                'success' => true,
            );

            return $response;
        }
    }

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

            $profile->category == Profile::CATEGORY_IND ?
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
