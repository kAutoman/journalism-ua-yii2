<?php

use yii\log\FileTarget;
use yii\rbac\DbManager;
use yii\console\Request;
use yii\console\ErrorHandler;
use vintage\i18n\components\I18N;
use console\controllers\MigrateController;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'console\migrations',
                'common\modules\menu\migrations',
                'common\modules\builder\migrations',
                'common\modules\mailer\migrations',
                'common\modules\faq\migrations',
                'common\modules\seo\migrations'
            ],
            'migrationPath' => [
                '@vendor/yiisoft/yii2/rbac/migrations',
            ]
        ],
    ],
    'components' => [
        'authManager' => ['class' => DbManager::class],
        'errorHandler' => ['class' => ErrorHandler::class],
        'log' => [
            'targets' => [
                ['class' => FileTarget::class, 'levels' => ['error', 'warning']],
            ],
        ],
        'i18n' => [
            'class'=> I18N::class,
            'languages' => ['en'] // hardcoded in console only.
        ],
        'request' => Request::class,
    ],
    'params' => $params,
];
