<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'controllerNamespace' => 'backend\components\gii\controllers',
        'generators' => [
            'migrations' => [
                'class' => \backend\components\gii\migration\Generator::class,
                'templates' => [
                    'default' => '@backend/components/gii/migration/default',
                ]
            ],
            'static-page-model' => [
                'class' => \backend\components\gii\staticPageModel\Generator::class,
                'templates' => [
                    'default' => '@backend/components/gii/staticPageModel/default',
                ]
            ],
            'builder-model' => backend\components\gii\builder\Generator::class,
            'advanced-module' => [
                'class' => \backend\components\gii\module\Generator::class,
                'templates' => [
                    'default' => '@backend/components/gii/module/default',
                ]
            ],
        ],
    ];
}

return $config;
