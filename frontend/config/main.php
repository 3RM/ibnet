<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',

    'modules' => [
        'markdown' => [
            'class' => 'kartik\markdown\Module',
            'smartyPants' => false,
            'i18n' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vendor/kartik-v/yii2-markdown/messages',
                'forceTranslation' => true,
            ],
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ], 
    ],

    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        // 'log' => [
        //     'traceLevel' => YII_DEBUG ? 3 : 0,
        //     'targets' => [
        //         [
        //             'class' => 'yii\log\FileTarget',
        //             'levels' => ['error', 'warning'],
        //         ],
        //         [
        //             'class' => 'yii\log\EmailTarget',
        //             'levels' => ['error'],
        //             'categories' => ['yii\db\*'],  //Process all categories initially until application is stable, then process only db category
        //             'message' => [
        //                'from' => ['admin@ibnet.org'],
        //                'to' => ['admin@ibnet.org'],
        //                'subject' => 'ERROR at ibnet.org',
        //             ],
        //         ],
        //     ],
        // ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [                       // https://github.com/ilopX/yii2-basic-htaccess
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => ['' => 'site/index',
                'sitemap.xml' => 'sitemap/sitemap-google-index',
                'sitemap.<p:\d+>.xml.gz' => 'sitemap/sitemap-google-urlset',
                '<a
                ction>'=>'site/<action>',
                'markdown/<controller:\w+>/<action:\w+>' => 'markdown/<controller>/<action>',
                '<action:association|fellowship|camp|chaplain|church|evangelist|mission-agency|missionary|music|special-ministry|pastor|print|school|staff>/<city:[A-Za-z0-9\-]+>/<name:[A-Za-z0-9\-]+>/<id:\d+>' => 'profile/<action>',
                '<controller:\w+>/<action:[A-Za-z\-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:[A-Za-z\-]+>' => '<controller>/<action>',
            ],
        ],
        // 'urlManagerBackend' => [
        //     'class' => 'yii\web\urlManager',
        //     'baseUrl' => '/yourapp/backend/web',
        //     'enablePrettyUrl' => true,
        //     'showScriptName' => true,
        // ],
    ],  
    'params' => $params,
];
