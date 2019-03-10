<?php
/**
 *  Controller class for all ajax requests
 * 
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\LoginForm;
use common\models\missionary\Missionary;
use common\models\missionary\MissionaryUpdate;
use common\models\network\Network;
use common\models\network\NetworkMember;
use common\models\network\NetworkKeyword;
use common\models\network\NetworkPlace;
use common\models\network\Prayer;
use common\models\network\PrayerUpdate;
use common\models\network\PrayerTag;
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
use yii\db\Query;
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
     * Logs in a user from the Nav bar.
     *
     * @return array
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

        // =============== login successful =======================
                if ($user->email != NULL && $model->login()) {
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
                    $body = 'Incorrect username or password.';
                    return ['body' => $body, 'success' => false];
                } 

        // ============== Incorrect username =======================
            } else {
                $body = 'Incorrect username or password.';
                return ['body' => $body, 'success' => false];
            }
        }
    }

    /**
     * Retrieve next new profile for content box 3 on index page.
     *
     * @return array
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
     * Mark a feature video as viewed.
     *
     * @return array
     */
    public function actionViewed($mid=NULL)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($mid) {
            $missionary = Missionary::findOne($mid);
            $missionary->updateAttributes(['viewed_update' => 1]);
        }

        $response = [
            'body' => '',
            'success' => true,
        ];

        return $response;
    }

    /**
     * Search box results for churches, ministries, and programs
     */
    public function actionSearch($type='church', $q=NULL, $exclude=NULL) 
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = ['results' => ['id' => '', 'text' => '']];
    
        $query = new Query;
        $query->select('id, type, status, org_name AS text, org_city, org_st_prov_reg')
            ->from('profile');

        if ($type == 'church') {
            $query->where(['type' => Profile::TYPE_CHURCH]);
        } elseif ($type == 'ministry') {
            $query->where(['category' => Profile::CATEGORY_ORG]);
        } elseif ($type == 'programs') {
            $query->where(['type' => Profile::TYPE_SPECIAL]);
        }
            
        $exclude == NULL ? NULL : $query->andWhere(['<>', 'id', $exclude]);
        $query->andWhere(['status' => Profile::STATUS_ACTIVE]);
        $escaped = '%' . str_replace(['+', '%', '_'], ['++', '+%', '+_'], $q) . '%';
        $query->andWhere("((`org_city` LIKE :term ESCAPE '+') OR (`org_name` LIKE :term ESCAPE '+'))")
            ->addParams([':term' => $escaped])
            ->limit(10);
        $data = $query->createCommand()->queryAll();
        $response['results'] = array_values($data);
            
        return $response;
    }



    // ************************** Profiles *******************************
  
    /**
     * Process "like" link.
     *
     * @return array
     */
    public function actionLike($iLike, $likeCount, $pid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            $profile = Profile::findOne($pid);
            $user = \Yii::$app->user->identity;
            if ($iLike) {
                $profile->unlink('likes', $user, $delete = true);
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
                $profile->link('likes', $user);
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




    // ************************** Profile Forms *******************************

    /**
     * Process request for forwarding email on form4 modal
     *
    * @return array
     */
    public function actionForwarding($id) 
    { 
        Yii::$app->response->format = Response::FORMAT_JSON;
            
        $profile = Profile::findProfile($id);
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

            if (empty($profile->email_pvt)) {

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
                Or '. Html::a('contact us', ['site/contact'], ['target' => '_blank', 'rel' => 'noopener noreferrer']) . ' regarding any questions with 
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



    // ************************** Missionary Updates *******************************

    /**
     * Toggle visible switch in missionary update table
     *
     * @return array
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



    // ************************** Network *******************************

    /**
     * Add a network place
     *
     * @return array
     */
    public function actionNetworkPlace()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $place = New NetworkPlace();
        $place->scenario = 'new';

        if ($place->load(Yii::$app->request->Post())) { 
            list($place->country, $place->state, $place->city) = array_reverse(explode(',', $place->place));
            $place->network_id = $_POST['add'];
            if ($place->validate() &&  $place->save()) {
                return $response = [
                    'pid' => $place->id, 
                    'place' => $place->place, 
                    'success' => true
                ];
            }
        } else {
            $response = ['success' => false];
        }
        return $response;
    }

    /**
     * Delete a network place
     *
     * @return array
     */
    public function actionDeleteNetworkPlace($pid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($place = NetworkPlace::findOne($pid)) {
            $place->delete();
            $response = ['pid' => $pid, 'success' => true];
        } else {
            $response = ['success' => false];
        }
        return $response;
    }

    /**
     * Add a network keyword
     *
     * @return array
     */
    public function actionNetworkKeyword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $keyword = New NetworkKeyword();
        $keyword->scenario = 'new';

        if ($keyword->load(Yii::$app->request->Post())) { 
            $keyword->network_id = $_POST['add'];
            if ($keyword->validate() &&  $keyword->save()) {
                return $response = [
                    'kid' => $keyword->id, 
                    'keyword' => $keyword->keyword, 
                    'success' => true
                ];
            }
        } else {
            $response = ['success' => false];
        }
        return $response;
    }

    /**
     * Delete a network keyword
     *
     * @return array
     */
    public function actionDeleteNetworkKeyword($kid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($keyword = NetworkKeyword::findOne($kid)) {
            $keyword->delete();
            $response = ['kid' => $kid, 'success' => true];
        } else {
            $response = ['success' => false];
        }
        return $response;
    }

    /**
     * Delete tag on network prayer request tag form
     *
     * @return array
     */
    public function actionDeleteTag($tid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($tag = PrayerTag::findOne($tid)) {
            $tag->delete();
            $response = ['tid' => $tid, 'success' => true];
        } else {
            $response = ['success' => false];
        }
        return $response;
    }

    /**
     * Return network prayer request on answer list back to prayer list
     *
     * @return array
     */
    public function actionReturnRequest($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($prayer = Prayer::findOne($id)) {
            $prayer->updateAttributes(['answered' => NULL, 'answer_date' => NULL]);
            $response = ['requestId' => $prayer->id, 'success' => true];
        } else {
            $response = ['success' => false, 'requestId' => $id];
        }
        return $response;
    }

    /**
     * Delete network prayer request on prayer list
     *
     * @return array
     */
    public function actionDeleteRequest($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($prayer = Prayer::findOne($id)) {
            $prayer->updateAttributes(['deleted' => 1]);
            $response = ['requestId' => $prayer->id, 'success' => true];
        } else {
            $response = ['success' => false, 'requestId' => $id];
        }
        return $response;
    }

    /**
     * Delete prayer update on network prayer list
     *
     * @return array
     */
    public function actionDeleteUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($update = PrayerUpdate::findOne($id)) {
            $update->updateAttributes(['deleted' => 1]);
            $response = ['updateId' => $update->id, 'success' => true];
        } else {
            $response = ['success' => false, 'updateId' => $id];
        }
        return $response;
    }

    /**
     * Process email_prayer_alert checkbox
     *
     * @return array
     */
    public function actionPrayerAlert()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $member = NetworkMember::findOne($_POST['mid']);
        $value = $member->email_prayer_alert == 1 ? 0 : 1;
        $member->updateAttributes(['email_prayer_alert' => $value]);
        return ['success' => true];
    }

    /**
     * Process email_prayer_summary checkbox
     *
     * @return array
     */
    public function actionPrayerSummary()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $member = NetworkMember::findOne($_POST['mid']);
        $value = $member->email_prayer_summary == 1 ? 0 : 1;
        $member->updateAttributes(['email_prayer_summary' => $value]);
        return ['success' => true];
    }

    /**
     * Process email_update_alert checkbox
     *
     * @return array
     */
    public function actionUpdateAlert()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $member = NetworkMember::findOne($_POST['mid']);
        $value = $member->email_update_alert == 1 ? 0 : 1;
        $member->updateAttributes(['email_update_alert' => $value]);
        return ['success' => true];
    }
}
