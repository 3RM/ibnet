<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    'modules' => [
        'rbac' =>  [
            'class' => 'johnitvn\rbacplus\Module',
            'beforeCreateController' => function($route){
                return false;                                   // Set to true to access rbac module
            },
        ],   
    ],

    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
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
    ],
    
];
