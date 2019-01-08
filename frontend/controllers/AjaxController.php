<?php
namespace frontend\controllers;

use common\models\LoginForm;
use common\models\missionary\MissionaryUpdate;
use common\models\profile\ProfileMail;
use common\models\profile\Profile;
use common\models\profile\ProfileSearch;
use common\models\profile\Social;
use common\models\Utility;
use common\models\User;
use frontend\controllers\ProfileFormController;
use frontend\models\Box3Content;
use Yii;
use yii\base\Security;
use yii\bootstrap\Html;
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

    /**
     * Logs in a user from the Nav bar.
     *
     * @return mixed
     */
    public function actionNavLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if (Yii::$app->request->post()) {
            $model->loginId = $_POST['username'];
            $model->password = $_POST['password'];
            $user = $model->user;
            if (isset($user)) {

        //  ================= banned user =========================
                if ($user->status == User::STATUS_BANNED) {
                    $body = 'Your account has been banned';
                    return ['body' => $body, 'success' => false];

        // =============== login successful =======================
                } elseif ($user->email != NULL && $model->login()) {
                    return ['body' => '', 'success' => true];

        // =============== email not verified =======================   
                } elseif ($user->new_email != NULL && $user->email == NULL) {       
                    $link = HTML::a('Resend Confirmation Link', Yii::$app->urlManager->createAbsoluteUrl([
                        'site/resend-verification-email', 
                        'username' => $user->username]));
                    $body = 'Unverified email: '. $link;
                    return ['body' => $body, 'success' => false];

        // =============== Incorrect Password =======================
                } else {
                    $body = 'Incorrect username/email or password.';
                    return ['body' => $body, 'success' => false];
                } 

        // ============== Incorrect username =======================
            } else {
                $body = 'Incorrect username/email or password.';
                return ['body' => $body, 'success' => false];
            }
        }
    }
  
    /**
     * Process "like" link.
     *
     * @return mixed
     */
    public function actionLike($iLike, $likeCount, $pid)
    {
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
                ProfileMail::sendLike($profile, $user);
            }
        }

        $response = [
            'body' => $body,
            'success' => true,
        ];

        return $response;
    }

    /**
     * Retrieve next new profile for content box 3 on index page.
     *
     * @return mixed
     */
    public function actionNext()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $content = new Box3Content();
        $box3Content = $content->getBox3Content();

        $response = [
            'body' => $box3Content,
            'success' => true,
        ];

        return $response;
    }

    /**
     * Process request for forwarding email on form4 modal
     */
    public function actionForwarding($id) 
    { 
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
            $profile->email = Inflector::slug($profile->ind_last_name) . $profile->id . '@ibnet.org' :
            $profile->email = Inflector::slug($profile->org_name) . $profile->id . '@ibnet.org';

        $profile->email_pvt_status = Profile::PRIVATE_EMAIL_PENDING;
        $profile->save();
            
        if ($profile->email_pvt && 
            $profile->email_pvt_status === Profile::PRIVATE_EMAIL_PENDING &&
            ProfileMail::sendForwardingEmailRqst($id, $profile->email, $profile->email_pvt)) {
    
            Yii::$app->session->setFlash('success', 
                'Your new email is pending and should be visible on your profile within 48 hours.  
                You may proceed with creating or updating your profile.');
        }

        return [
            'body' => '',
            'success' => true,
        ];

    }

    /**
     * Toggle visible switch in missionary update table
     *
     * @return mixed
     */
    public function actionUpdateVisible($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $body = '';
        if ($update = MissionaryUpdate::findOne($id)) {
            if ($update->visible) {
                $update->updateAttributes(['visible' => 0]);
            } else {
                $update->updateAttributes(['visible' => 1]);
            }
            $body = $update->visible ? 
                Html::a(Html::icon('eye-open'), ['ajax/update-visible', 'id' => $update->id], [
                    'id' => 'visible-' . $update->id, 
                    'data-on-done' => 'visibleDone', 
                    'class' => 'update-visible'
                ]) : 
                Html::a(Html::icon('eye-close'), ['ajax/update-visible', 'id' => $update->id], [
                    'id' => 'visible-' . $update->id, 
                    'data-on-done' => 'visibleDone', 
                ]);
            $response = [
                'body' => $body,
                'updateId' => $update->id,
                'success' => true,
            ];
        } else {
            $response = ['success' => false];
        }

        return $response;
    }
}
