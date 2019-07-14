<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace frontend\controllers;

use common\models\LoginForm;
use common\models\Subscription;
use common\models\blog\WpPosts;
use common\models\profile\Profile;
use common\models\profile\ProfileBrowse;
use common\models\profile\ProfileMail;
use common\models\profile\ProfileSearch;
use common\models\profile\ProfileGuestSearch;
use common\models\PrimaryRole;
use common\models\User;
use frontend\controllers\ProfileController;
use frontend\models\Box3Content;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\RegisterForm;
use Yii;
use yii\base\InvalidParamException;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public $layout;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'settings'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'settings'],
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'page' => [
                'class' => 'yii\web\ViewAction',
            ],
            'timezone' => [
                'class' => 'yii2mod\timezone\TimezoneAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {   
        $searchModel = Yii::$app->user->isGuest ? new ProfileGuestSearch() : new ProfileSearch();

        if ($searchModel->load(Yii::$app->request->Post()) &&
            $searchModel->term != '') {
            $term = $searchModel->term; 
            return $this->redirect(['/profile/search', 'term' => $term]);
        } else {
        
            $term = '';

            // Get new profiles for box 3 and add to session
            $profiles = Profile::find()
                ->select('*')
                ->where(['status' => PROFILE::STATUS_ACTIVE])
                ->andwhere('created_at>DATE_SUB(NOW(), INTERVAL 14 DAY)')
                ->orderBy('created_at DESC')
                ->all();
            $count = count($profiles);
            $i = 0;

            $session = Yii::$app->session;
            $session->open('profiles');
            $session->open('count'); // Total number of profiles
            $session->open('i'); // Profile number to show
            $session->set('profiles', $profiles);
            $session->set('count', $count);
            $session->set('i', $i);

            $content = new Box3Content();
            $box3Content = $content->getBox3Content();

            // Get Blog posts
            $posts = NULL;
            $comments = NULL;
            $posts = WpPosts::getPosts();
            $postIds = ArrayHelper::getColumn($posts, 'post_id');
            $comments = WpPosts::getComments($postIds);
        
            $this->layout = 'bg-gray';
            return $this->render('/site/index', [
                'searchModel' => $searchModel, 
                'term' => $term,
                'box3Content' => $box3Content,
                'posts' => $posts,
                'comments' => $comments,
                'count' => $count
            ]);
        }
    }

    /**
     * Logs in a user.
     * @param $url string Redirect url following login
     * @return mixed
     */
    public function actionLogin($url=NULL)
    {
        if (!Yii::$app->user->isGuest) {
            return empty($url) ?
                $this->goHome() :
                $this->redirect(urldecode($url));
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->user;
            if (isset($user)) {

        // =============== login successful =======================
                if ($user->email != NULL && $model->login()) {

                    // Redirect to Discourse if SSO login
                    if ($sso = Yii::$app->getSession()->get('sso')) {
                        return $this->redirect(['discourse-sso', 
                            'sso' => $sso['sso'], 
                            'sig' => $sso['sig']
                        ]);
                    }

                    return $url == NULL ?
                        $this->goHome() :
                        $this->redirect($url);

        // =============== email not verified =======================   
                } elseif ($user->new_email != NULL && $user->email == NULL) {       
                    $link = HTML::a('Resend Confirmation Link', Yii::$app->urlManager->createAbsoluteUrl([
                        'site/resend-verification-email', 
                        'username' => $user->username]));
                    Yii::$app->session->setFlash('error', 'Your email is not verified.  Find the 
                        verification email we sent and follow the link to complete your 
                        registration.  Be sure to check your spam folder.<br>' . $link);
                    return $this->render('login', ['model' => $model]);

        // =============== Incorrect Password =======================
                } else {
                    Yii::$app->session->setFlash('error', 'Your password or username is incorrect.');
                    return $this->render('login', ['model' => $model]);
                } 

        // ============== Incorrect username =======================
            } else {
                Yii::$app->session->setFlash('error', 'Your password or username is incorrect.');
                return $this->render('login', ['model' => $model]);
            }

        } else {
            return $this->render('login', ['model' => $model]);
        }
    }

    /**
     * Discourse SSO login
     *
     * @return mixed
     */
    public function actionDiscourseSso()
    {
        $request = Yii::$app->getRequest();
        $sso = Yii::$app->discourseSso;
        
        $payload = $request->get('sso');
        $sig = $request->get('sig');
    
        if(!($sso->validate($payload, $sig))){
            throw new ForbiddenHttpException('Bad SSO request');
        }
        
        $nonce = $sso->getNonce($payload);
        
        if (Yii::$app->getUser()->isGuest) {
            Yii::$app->getSession()->set('sso', ['sso' => $payload, 'sig' => $sig]);
            return $this->redirect(['site/login']);
        } else {
            $user = Yii::$app->getuser()->getIdentity();
        }
        
        Yii::$app->getSession()->remove('sso');
        
        // Send the data
        $userparams = [
            "nonce" => $nonce,
            "external_id" => (String)$user->id,
            "email" => $user->email,
            "username" => $user->username,
            "name" => $user->fullName,
            'avatar_url' => isset($user->usr_image) ? Url::to([$user->usr_image], 'http') : NULL,
        ];
        $q = $sso->buildLoginString($userparams);
        
        // Redirect back
        $this->redirect(Yii::getAlias('@discourse') . '/session/sso_login?' . $q);
    }

    public function actionTestVerification()
    {

        return $this->render('test-verification');
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Return from Discourse forum
     *
     * @return redirect
     */
    public function actionForumReturn()
    {
        return $this->redirect(Url::previous());
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['email.admin'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Unsubscribe an email address from all communications
     *
     * @return mixed
     */
    public function actionUnsubscribe($email, $token)
    {
        if (isset($email) && isset($token)) {
            if (!$sub = Subscription::find()->where(['email' => $email])->andWhere(['token' => $token])->one()) {
                throw new NotFoundHttpException;
            }
        } else {
            $sub = new MailPreferences();
        }
        $sub->scenario = 'unsubscribe';

        if ($sub->load(Yii::$app->request->post())) {
            $sub->unsubscribe();
            return $this->redirect(['unsubscribed', 'email' => $sub->email, 'token' => $sub->token]);
        }

        $registered = User::find()->where(['email' => $email])->exists();

        return $this->render('unsubscribe', [
            'sub' => $sub, 
            'registered' => $registered
        ]);
    }

    /**
     * Unsubscribe successful
     *
     * @return mixed
     */
    public function actionUnsubscribed($email, $token)
    {
        if (!$sub = Subscription::find()->where(['email' => $email])->andWhere(['token' => $token])->one()) {
            throw new NotFoundHttpException;
        }

        return $this->render('unsubscribed', [
            'sub' => $sub,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        Url::remember();
        return $this->render('about');
    }

    /**
     * Registers user.
     *
     * @return mixed
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('already-registered');
        }
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->register()) {                

                // Send email verification email
                $user->generateNewEmailToken();
                $link = Yii::$app->urlManager->createAbsoluteUrl(['site/registration-complete', 
                    'token' => $user->new_email_token]);
                $mail = $user->subscription ?? new Subscription();
                $mail->to = $user->new_email;
                $mail->title = 'Complete your registration with IBNet.org';
                $mail->subject = Yii::$app->params['email.systemSubject'];
                $mail->message = 'Follow this link to complete your registration: ' . $link;
                $mail->sendNotification(NULL, TRUE);

                // Notify admin of new user
                $mail = Subscription::getSubscriptionByEmail(Yii::$app->params['email.admin']) ?? new Subscription();
                $mail->to = Yii::$app->params['email.admin'];
                $mail->title = 'New User Registration';
                $mail->subject = Yii::$app->params['email.systemSubject'];
                $mail->message = 'A new user has registered at IBNet: ' . $user->fullName;
                $mail->sendNotification(NULL, TRUE);
                    
                return $this->render('completeRegistration');
            }
        }
        $this->layout = 'bg-gray';
        return $this->render('register', ['model' => $model]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAlreadyRegistered()
    {
        $this->layout = 'bg-gray';
        return $this->render('alreadyRegistered');
    }

    /**
     * Resends user an email with a link to verify their email address
     *
     * @return mixed
     */
    public function actionResendVerificationEmail($username)
    {
        $user = User::findByUsername($username);
        $user->generateNewEmailToken();
        $link = Yii::$app->urlManager->createAbsoluteUrl([
            'site/registration-complete', 
            'token' => $user->new_email_token]);
       
        $mail = Subscription::getSubscriptionByEmail($user->new_email) ?? new Subscription();
        $mail->to = $user->new_email;
        $mail->title = 'Complete your registration with IBNet.org';
        $mail->subject = Yii::$app->params['email.systemSubject'];
        $mail->message = 'Follow this link to complete your registration: ' . $link;
        if ($mail->sendNotification(NULL, TRUE)) {
            Yii::$app->session->setFlash('success', 'A new email was sent with a link to verify your email address.');
        } else {
            Yii::$app->session->setFlash('danger', 'There was an error processing your request. Please contact admin@ibnet.org if the problem persists.');
        }
        return $this->redirect('login');
    }

    /**
     * Landing page for registration complete.
     *
     * @return mixed
     */
    public function actionRegistrationComplete($token=NULL)
    {
        if (!empty($token) && $user = User::findByNewEmailToken($token)) {
            if ($user->isNewEmailTokenValid($token)) {
                // Save user params
                $user->updateAttributes([
                    'new_email_token' => NULL,
                    'email' => $user->new_email,
                    'new_email' => NULL,
                    'timezone' => Yii::$app->timezone->name, // Very rough guess at user timezone using JSTZ
                ]);
                Yii::$app->getUser()->login($user);
                $user->scenario = 'sub';
                return $this->render('registrationComplete', ['user' => $user]);
            }
        } elseif ($user = Yii::$app->user->identity) {
            // Load post data from email preferences
            if ($user->load(Yii::$app->request->Post()) &&
                $user->validate() && 
                $user->save()) {
                return $this->redirect('settings');
            }
        }
        return $this->render('invalidToken');
    }

    /**
     * Displays privacy page.
     *
     * @return mixed
     */
    public function actionPrivacy()
    {
        Url::remember();
        return $this->render('privacy');
    }

    /**
     * Displays terms page.
     *
     * @return mixed
     */
    public function actionTerms()
    {
        Url::remember();
        return $this->render('terms');
    }

    /**
     * Displays beliefs page.
     *
     * @return mixed
     */
    public function actionBeliefs()
    {
        Url::remember();
        return $this->render('beliefs');
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions to reset your password.');
                return $this->redirect('login');
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset your password for the email provided.');
            }
        }
        return $this->render('requestPasswordResetToken', ['model' => $model]);
    }

    /**
     * Resets password
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');
            return $this->redirect('login');
        }
        return $this->render('resetPassword', ['model' => $model]);
    }

    /**
     * New email confirmed
     *
     * @param string $token
     * @return mixed
     */
    public function actionEmailConfirmed($token)
    {
        $confirmed = false;
        if ($token && $user = User::findByNewEmailToken($token)) {
            $user->updateAttributes([
                'new_email_token' => NULL,
                'email' => $user->new_email,
                'new_email' => NULL]);
            $confirmed = true;
        }
            
        return $this->render('emailConfirmed', ['confirmed' => $confirmed]);
    }

    /**
     * User profile and account management area
     * @return mixed
     */
    public function actionSettings()
    {
        // Personal user settings
        $userP = Yii::$app->user->identity;
        $userP->scenario = 'personal';

        // Account user settings
        $userA = Yii::$app->user->identity;
        $userA->scenario = 'account';

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id))[0];
        $joinedGroups = $userP->activeJoinedGroups;

        // Set default role
        if ($userP->primary_role == NULL) {
            $userP->primary_role = 'Church Member';
        }
        $home_church = NULL;
        if (($userP->home_church != NULL)
            && ($hc = Profile::findActiveProfile($userP->home_church))) {
            $home_church = $hc->org_name . ', ' . $hc->org_city . ', ' . $hc->org_st_prov_reg;
        }
        $list = ArrayHelper::map(PrimaryRole::find()->all(), 'role', 'role', 'type');

        // Set subscriptions
        if (!$sub = $userA->subscription) {
            $sub = New Subscription();
            $sub->add($userA->email);
        }
        $userA->subscriptionProfile = $sub->profile;
        $userA->subscriptionLinks = $sub->links;
        $userA->subscriptionComments = $sub->comments;
        $userA->subscriptionFeatures = $sub->features;
        $userA->subscriptionBlog = $sub->blog;

        Url::remember();
        return $this->render('settings', [
            'userP' => $userP,
            'userA' => $userA,
            'list' => $list,
            'home_church' => $home_church,
            'role' => $role,
            'joinedGroups' => $joinedGroups,
        ]);
    }

    /**
     * Personal settings form submit redirects here
     * @return mixed
     */
    public function actionPersonalSettings()
    {
        $user = Yii::$app->user->identity;
        $user->scenario = 'personal';

        if ($user->load(Yii::$app->request->Post())) {
            $user->validate(); 
            if ($user->primary_role == NULL) {
                // Set default role
                $user->primary_role = 'Church Member';
            }

            // Update roles
            $role = array_keys(Yii::$app->authManager->getRolesByUser($user->id))[0];            
            if ($user->home_church && ($role == User::ROLE_USER)) {  
                // Revoke current User role
                $auth = Yii::$app->authManager;
                $item = $auth->getRole(User::ROLE_USER);
                $auth->revoke($item, $user->id);  
                // Home church identified; Upgrade user role to SafeUser         
                $auth = Yii::$app->authManager;
                $userRole = $auth->getRole('SafeUser');
                $auth->assign($userRole, $user->id);
            
            } elseif (!$user->home_church && !$user->hasIndActiveProfile && ($role == User::ROLE_SAFEUSER)) {
                // Revoke current SafeUser role
                $auth = Yii::$app->authManager;
                $item = $auth->getRole(User::ROLE_SAFEUSER);
                $auth->revoke($item, $user->id);
                // Assign User role
                $auth = Yii::$app->authManager;
                $userRole = $auth->getRole(USER::ROLE_USER);
                $auth->assign($userRole, $user->id);
            }
            $user->save();

            // Notify church profile owner of new link
            $oldChurch = $user->getOldAttribute('home_church');
            if ($user->home_church && $user->home_church != $oldChurch) {
                $church = Profile::findActiveProfile($user->home_church);
                $churchProfOwner = User::findOne($church->user_id);
                if ($churchProfOwner && $churchProfOwner->subscription->links == 1) {
                    ProfileMail::sendLink($user, $church, $churchProfOwner, 'PSHC', 'L');
                }
                if ($oldChurch) {
                    $oldChurch = Profile::findActiveProfile($oldChurch);
                    $oldChurchProfOwner = User::findOne($oldChurch->user_id);
                    if ($oldChurchProfOwner && $oldChurchProfOwner->subscription->links == 1) {
                        ProfileMail::sendLink($user, $oldChurch, $oldChurchProfOwner, 'PSHC', 'UL');
                    }
                }
            }
        }
        return $this->redirect('settings');

    }

    /**
     * Account settings form submit redirects here
     * @return mixed
     */
    public function actionAccountSettings()
    {
        // Personal user settings
        $user = Yii::$app->user->identity;
        $user->scenario = 'account';

        if ($user->load(Yii::$app->request->Post()) &&
            $user->handleAccount()) {
            $user->validate(); 
            $user->save();
        }
        return $this->redirect('settings');
    }
}
