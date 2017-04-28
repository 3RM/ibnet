<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    'modules' => [
        'rbac' =>  [
            'class' => 'johnitvn\rbacplus\Module',
            'beforeCreateController' => function($route){
                return true;                                   // Set to true to access rbac module (i.e. rule/permission/role/assignment routes)
            },
            'userModelExtraDataColumls'=>[
                [
                    'attribute'=>'First Name',
                    'value'=>function($model) {
                        return $model->first_name;
                    }
                ],
                [
                    'attribute'=>'Last Name',
                    'value'=>function($model) {
                        return $model->last_name;
                    }
                ],
                [
                    'attribute'=>'email',
                    'value'=>function($model) {
                        return $model->email;
                    }
                ],
            ]
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
