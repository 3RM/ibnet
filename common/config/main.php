<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    'modules' => [
        'rbac' =>  [
            'class' => 'dektrium\rbac\RbacWebModule',
        ],
        'comments' => [
            'class' => 'rmrevin\yii\module\Comments\Module',
            'userIdentityClass' => 'common\models\User',
            'useRbac' => true,
            'modelMap' => [
                'Comment' => 'frontend\models\Comment'
            ],
        ],     
    ],

    'components' => [
        'authManager' => [
            'class' => 'dektrium\rbac\components\DbManager',
            'cache' => 'yii\caching\FileCache',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'dateFormat' => 'MM-dd-yyyy',
        ],
        'solr' => [
            'class' => 'sammaye\solr\Client',
            'options' => [
                'endpoint' => [
                    'ibnet' => [
                        'host' => '62.151.181.176',
                        'port' => '8983',
                        'path' => '/solr/ibnet',
                    ],
                ],
            ],
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@vendor/rmrevin/yii2-comments/widgets/views' => '@frontend/views/profile/profilePages/comment',
                ],
            ],
        ],
    ],
    
];
