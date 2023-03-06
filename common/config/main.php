<?php

use common\components\Formatter;
use common\components\Request;
use common\helpers\LanguageHelper;
use himiklab\sitemap\Sitemap;
use yii\rbac\DbManager;
use yii\caching\FileCache;
use yii\caching\DummyCache;
use yii\web\User as UserComponent;
use metalguardian\fileProcessor\Module as FpmModule;
use common\models\User;
use common\components\UrlManager;
use common\components\I18N as I18NComponent;
use common\modules\config\application\ConfigModule;
use common\modules\config\application\components\Configurator;


return [
    'timeZone' => 'Europe/Kiev',
//    'sourceLanguage' => 'xx', // don't change this, pls ;-)
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@npm' => '@vendor/npm-asset',
        '@bower' => '@vendor/bower-asset',
    ],
    'bootstrap' => ['config'],
    'modules' => [
        'config' => [
            'class' => ConfigModule::class,
            'specifications' => require 'config-specification.php',
        ],
        'fileProcessor' => [
            'class' => FpmModule::class,
            'imageSections' => require 'fpm-image-sections.php',
        ],
        'sitemap' => [
            'class' => Sitemap::class,
            'cacheProvider' => 'cache',
            'models' => [
                common\models\Page::class,
            ],
            'enableGzip' => true, // default is false
            'cacheExpire' => YII_ENV_PROD ? 86400 : 1, // 1 second for dev env. Default is 24 hours
        ],
    ],
    'components' => [
        'authManager' => DbManager::class,
        'errorHandler' => ['class' => \yii\web\ErrorHandler::class],
        'formatter' => [
            'class' => Formatter::class,
            'defaultTimeZone' => 'Europe/Kiev',
            'timeFormat' => 'H:i:s',
            'dateFormat' => 'Y-m-d'
        ],
        'configurator' => Configurator::class,
        'user' => [
            'class' => UserComponent::class,
            'identityClass' => User::class,
            'enableAutoLogin' => true,
        ],
        'cache' => [
            'class' => DummyCache::class,
        ],
        'cacheDb' => [
            'class' => FileCache::class,
            'cachePath' => '@api/runtime/cacheDb'
        ],
        'cacheImage' => [
            'class' => FileCache::class,
            'cachePath' => '@api/runtime/cache-image'
        ],
        'cacheLang' => [
            'class' => FileCache::class,
            'cachePath' => '@api/runtime/cacheLang'
        ],
        'i18n' => [
            'class' => I18NComponent::class,
            'enableCaching' => true,
            'cachingDuration' => 60 * 60 * 24,
            'forceTranslation' => true,
            'cache' => 'cacheLang',
        ],
        'urlManager' => [
            'class' => UrlManager::class,
        ],
        'request' => [
            'class' => Request::class,
            'acceptableLanguages' => function () {
                return LanguageHelper::getApplicationLanguages();
            },
        ],
    ],
];
