<?php

use backend\modules\auth\AuthModule;
use backend\modules\faq\FaqModule;
use backend\modules\imagesUpload\ImagesUploadModule;
use backend\modules\language\controllers\TranslationController;
use backend\modules\language\LanguageModule;
use backend\modules\menu\MenuModule;
use backend\modules\page\PageModule;
use backend\modules\seo\SeoModule;
use backend\modules\user\UserModule;
use common\components\Formatter;
use common\modules\builder\BuilderModule;
use common\modules\dynamicForm\DynamicFormModule;
use common\modules\mailer\MailerModule;
use developeruz\db_rbac\Yii2DbRbac;
use kartik\grid\Module as GridViewModule;
use vintage\i18n\Module as TranslateModule;
use yii\log\FileTarget;
use yii\rbac\DbManager;
use yii\web\DbSession;

$params = merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'name' => 'Vintage',
    'language' => 'uk',
    'sourceLanguage' => 'uk',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log', 'fileProcessor'],
    'timezone' => 'Europe/Kiev',
    'modules' => [
        'seo' => SeoModule::class,
        'rbac' => [
            'class' => 'backend\modules\rbac\Module',
            'layout' => 'left-menu',
            'mainLayout' => '@backend/views/layouts/main.php',
            'controllerMap' => [
                'assignment' => [
                    'class' => 'backend\modules\rbac\controllers\AssignmentController',
                    'idField' => 'id',
                    'usernameField' => 'username',
                ],
            ],
        ],
        'user' => UserModule::class,
        'auth' => AuthModule::class,
        'builder' => BuilderModule::class,
        'log' => backend\modules\log\Module::class,
        'language' => LanguageModule::class,
        'gridview' => GridViewModule::class,
        'page' => PageModule::class,
        'faq' => FaqModule::class,
        'menu' => MenuModule::class,
        'imagesUpload' => ImagesUploadModule::class,
        'dynamic-form' => DynamicFormModule::class,
        'mailer' => MailerModule::class,
        'news' => backend\modules\news\Module::class,
        'layout' => backend\modules\layout\LayoutModule::class,
        'expert' => backend\modules\expert\ExpertModule::class,
        'jury' => backend\modules\jury\JuryModule::class,
        'member' => backend\modules\member\MemberModule::class,
        'home' => backend\modules\home\HomeModule::class,
        'winner' => backend\modules\winner\WinnerModule::class,
        'request' => backend\modules\request\RequestModule::class,
        'article' => backend\modules\article\ArticleModule::class,
        'permit' => [
            'class' => Yii2DbRbac::class,
            'params' => ['userClass' => common\models\User::class],
        ],
        'i18n' => [
            'class' => TranslateModule::class,
            'controllerMap' => ['default' => TranslationController::class],
        ],
    ],
    'components' => [
        'session' => [
            'class' => DbSession::class,
            'sessionTable' => '{{%session}}',
            'writeCallback' => function () {
                return [
                    'user_id' => Yii::$app->user->id ?? null,
                ];
            }
        ],
        'user' => ['loginUrl' => ['/login']],
        'authManager' => [
            'class' => DbManager::class,
            'cache' => '\yii\caching\FileCache',
        ],
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => ['https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js']
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => '/error/error'
        ],
        'formatter' => [
            'class' => Formatter::class,
            'datetimeFormat' => 'php:Y-m-d H:i',
            'dateFormat' => 'php:Y-m-d',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                ['class' => FileTarget::class, 'levels' => ['error', 'warning']],
            ],
        ],
    ],
    'as access' => [
        'class' => 'backend\modules\rbac\components\AccessControl',
        'allowActions' => [
            'auth/login/login',
            'auth/login/logout',
        ]
    ],
    'params' => $params,
];
