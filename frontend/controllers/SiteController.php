<?php
namespace frontend\controllers;

use common\models\AccountSettings;
use common\models\LoginForm;
use common\models\profile\Profile;
use common\models\profile\ProfileBrowse;
use common\models\profile\ProfileSearch;
use common\models\User;
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
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use xj\sitemap\models\Url;
use xj\sitemap\models\BaiduUrl;
use xj\sitemap\actions\SitemapUrlsetAction;
use xj\sitemap\actions\SitemapIndexAction;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
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
             //Google Sitemap By ActiveRecord
            'sitemap-google-index' => [
                'class' => SitemapIndexAction::className(),
                'route' => ['sitemap-google-urlset'],
                'dataProvider' => new ActiveDataProvider([
                    'query' => Profile::find()->where(['status' => Profile::STATUS_ACTIVE])->all(),
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 1, //per page 1 record
                    ]]),
            ],
            'sitemap-google-urlset' => [
                'class' => SitemapUrlsetAction::className(),
                'gzip' => YII_DEBUG ? false : true,
                'dataProvider' => new ActiveDataProvider([
                    'query' => Profile::find()->where(['status' => Profile::STATUS_ACTIVE]),
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 1,
                    ]]),
                'remap' => function ($model) {
                        /* @var $model Profile */
                        $url = Url::create([
                            'loc' => \yii\helpers\Url::to(['profile/view-profile', 'id' => $model->id], true),
                            'lastmod' => date(DATE_W3C, $model->last_modified),
                            'changefreq' => Url::CHANGEFREQ_MONTHLY,
                            'priority' => '0.5',
                        ]);                        
                        return $url;
                },
            ],

            //Baidu Mobile Sitemap By ActiveRecord
            'sitemap-baidumobile-index' => [
                'class' => SitemapIndexAction::className(),
                'route' => ['sitemap-baidumobile-urlset'],
                'dataProvider' => new ActiveDataProvider([
                    'query' => Profile::find()->where(['status' => Profile::STATUS_ACTIVE]),
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 1, //per page 1 record
                    ]]),
            ],
            'sitemap-baidumobile-urlset' => [
                'class' => SitemapUrlsetAction::className(),
                'urlClass' => BaiduUrl::className(), //for Baidu
                'gzip' => YII_DEBUG ? false : true,
                'dataProvider' => new ActiveDataProvider([
                    'query' => Profile::find()->where(['status' => Profile::STATUS_ACTIVE]),
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 1,
                    ]]),
                'remap' => function ($model) {
                    /* @var $model Profile */
                    //return Array will auto using $urlClass::create()
                    return [
                        'loc' => \yii\helpers\Url::to(['user/view', 'username' => $model->username], true),
                        'lastmod' => date(DATE_W3C, $model->updated_at),
                        'changefreq' => Url::CHANGEFREQ_MONTHLY,
                        'priority' => '0.5',
                        'baiduType' => BaiduUrl::BAIDU_TYPE_MOBILE, // BaiduUrl::BAIDU_TYPE_ADAP | BaiduUrl::BAIDU_TYPE_HTMLADAP
                    ];
                },
            ],

            //FOR DIRECT DATA
            'sitemap-direct-index' => [
                'class' => SitemapIndexAction::className(),
                'route' => ['sitemap-direct'],
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => [
                        1, 1, 1, 1 //only need number// p=1 | p=2 | p=3 | p=4
                    ],
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 1,
                    ]
                ]),
            ],
            'sitemap-direct-urlset' => [
                'class' => SitemapUrlsetAction::className(),
                'gzip' => YII_DEBUG ? false : true,
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => [
                        [
                            'loc' => 'http://url-a',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_ALWAYS,
                        ],
                        [
                            'loc' => 'http://url-b',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_DAILY,
                        ],
                        [
                            'loc' => 'http://error-model',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_HOURLY,
                            'priority' => 'errorPriority',
                        ],
                        [
                            'loc' => 'http://url-c',
                            'lastmod' => date(DATE_W3C),
                            'changefreq' => Url::CHANGEFREQ_HOURLY,
                        ],
                    ],
                    'pagination' => [
                        'pageParam' => 'p',
                        'pageSize' => 4,
                    ]
                ]),
                'remap' => function ($model) {
                    /* @var $model array */
                    return Url::create()->setAttributes($model);
                },
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
        $searchModel = new ProfileSearch();

        if ($searchModel->load(Yii::$app->request->Post()) &&
            $searchModel->term != '') {                                                         // Render index if user enters a blank search string
        
                $term = $searchModel->term;     // Consider adding ~ to key words for fuzzy search 
                return $this->redirect(['/profile/search', 'term' => $term]);
        } else {
        
            $term = '';

            $profiles = Profile::find()                                                             // Get new profiles for box 3 and add to session
                ->select('*')
                ->where(['status' => PROFILE::STATUS_ACTIVE])
                ->andwhere('created_at>DATE_SUB(NOW(), INTERVAL 14 DAY)')
                ->orderBy('created_at DESC')
                ->all();
            $count = count($profiles);
            $i = 0;

            $session = Yii::$app->session;
            $session->open('profiles');
            $session->open('count');                                                                 // Total number of profiles
            $session->open('i');                                                                     // Profile number to show
            $session->set('profiles', $profiles);
            $session->set('count', $count);
            $session->set('i', $i);

            $content = new Box3Content();
            $box3Content = $content->getBox3Content();
            
            return $this->render('/site/index', [
                'searchModel' => $searchModel, 
                'term' => $term,
                'box3Content' => $box3Content,
                'count' => $count
            ]);
        }
    }

    /**
     * Logs in a user.
     * $url passes through a redirect url following login
     *
     * @return mixed
     */
    public function actionLogin($url=NULL)
    {
        if (!Yii::$app->user->isGuest) {
            return empty($url) ?
                $this->goHome() :
                $this->redirect($url);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->user;
            if (isset($user)) {
                if ($user->email != NULL && $model->login()) {
                    return $url == NULL ?
                        $this->goHome() :
                        $this->redirect($url);
                } elseif ($user->new_email != NULL && $user->email == NULL) {       // email not verified
                    $link = HTML::a('Resend Confirmation Link', Yii::$app->urlManager->createAbsoluteUrl([
                        'site/resend-verification-email', 
                        'username' => $user->username]));
                    Yii::$app->session->setFlash('error', 'Your email is not verified.  Find the 
                        verification email we sent and follow the link to complete your 
                        registration. Be sure to check your spam folder.<br>' . $link);
                    return $this->render('login', ['model' => $model]);

                } else {        // incorrect password
                    Yii::$app->session->setFlash('error', 'Your password or username/email is incorrect.');
                    return $this->render('login', ['model' => $model]);
                } 

            } else {        // Incorrect username  or email
                Yii::$app->session->setFlash('error', 'Your password or username/email is incorrect.');
                return $this->render('login', ['model' => $model]);
            }
        } else {
            return $this->render('login', ['model' => $model]);
        }
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
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
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
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays blog coming soon page.
     *
     * @return mixed
     */
    public function actionBlog()
    {
        return $this->render('blog');
    }

    /**
     * Displays Grand Opening page.
     *
     * @return mixed
     */
    public function actionGrandOpening()
    {
        return $this->render('grandOpening');
    }

    /**
     * Displays How-To-Videos page.
     *
     * @return mixed
     */
    public function actionHowTo()
    {
        return $this->render('how-to');
    }

    /**
     * Registers user.
     *
     * @return mixed
     */
    public function actionRegister()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->register()) {                
                $this->actionSendVerificationEmail($user->username);
                return $this->render('completeRegistration');
            }
        }
        return $this->render('register', ['model' => $model]);
    }

    /**
     * Sends user an email with a link to verify their email address.
     *
     * @return boolean
     */
    public function actionSendVerificationEmail($username)
    {
        $user = User::findByUsername($username);
        $user->generateNewEmailToken();
        $link = Yii::$app->urlManager->createAbsoluteUrl([
            'site/registration-complete', 
            'token' => $user->new_email_token]);
        if (Yii::$app->mailer
            ->compose(
                ['html' => 'notification-html'],
                [
                    'title' => 'Complete your registration with IBNet.org', 
                    'message' => 'Follow this link to complete your registration: ' . $link,
                ])
            ->setFrom(Yii::$app->params['no-replyEmail'])
            ->setTo($user->new_email)
            ->setSubject(Yii::$app->params['emailSubject'])
            ->send()) {

            return $this->render('about');
        }
        return false;
    }

    /**
     * Resends user an email with a link to verify their email address
     *
     * @return mixed
     */
    public function actionResendVerificationEmail($username)
    {
        if ($this->actionSendVerificationEmail($username)) {
            Yii::$app->session->setFlash('success', 'A new email was sent with a link to verify your 
                email address.');

            return $this->redirect('login');
        }
        // throw an error
    }

    /**
     * Landing page for registration complete.
     *
     * @return mixed
     */
    public function actionRegistrationComplete($token)
    {
        if ($token && $user = User::findByNewEmailToken($token)) {
            $user->updateAttributes([
                'new_email_token' => NULL,
                'email' => $user->new_email,
                'new_email' => NULL]);
            Yii::$app->getUser()->login($user);

            return $this->render('registrationComplete');
        }
    }

    /**
     * Displays privacy page.
     *
     * @return mixed
     */
    public function actionPrivacy()
    {
        return $this->render('privacy');
    }

    /**
     * Displays terms page.
     *
     * @return mixed
     */
    public function actionTerms()
    {
        return $this->render('terms');
    }

    /**
     * Displays beliefs page.
     *
     * @return mixed
     */
    public function actionBeliefs()
    {
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

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
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
     * New email confirmed.
     *
     * @param string $token
     * @return mixed
     */
    public function actionEmailConfirmed($token)
    {
        if ($token && $user = User::findByNewEmailToken($token)) {
            $user->updateAttributes([
                'new_email_token' => NULL,
                'email' => $user->new_email,
                'new_email' => NULL]);
            
            return $this->render('emailConfirmed');

        } else {
            // Throw a bad request error
        }
    }

    /**
     * User profile and account management area
     * @return mixed
     */
    public function actionMySettings()
    {
        $user = Yii::$app->user->identity;
        $account = new AccountSettings;
        $account->scenario = 'update';
        $account->email = $user->email;
        $account->currentUsername = $user->username;   
        $account->emailMaintenance = 1;
        $account->emailPrefProfile = $user->emailPrefProfile;
        $account->emailPrefLinks = $user->emailPrefLinks;
        $account->emailPrefFeatures = $user->emailPrefFeatures;
        $account->toggle = 'none';
        if ($account->load(Yii::$app->request->Post()) && $account->updateAccount($user)) {
            $account->newEmail = '';

            return $this->render('mySettings', ['account' => $account, 'toggle' => $account->toggle]); 

        } else {

            return $this->render('mySettings', ['account' => $account, 'toggle' => $account->toggle]);
        }
    }
}
