<?php

$config = [
    'components' => [
        'request' => [
            'enableCookieValidation' => false,
            'cookieValidationKey' => '',
        ],
    ],
];
//if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    //$config['bootstrap'][] = 'debug';
    //$config['modules']['debug'] = [
    //    'class' => 'yii\debug\Module',
    //    'allowedIPs' => ['*'],
    //];
//}
return $config;
