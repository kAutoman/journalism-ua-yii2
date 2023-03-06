<?php

use yii\web\JsonParser;
use yii\web\JsonResponseFormatter;
use api\modules\request\RequestModule;
use api\modules\seo\SeoModule;
use api\modules\article\ArticleModule;
use common\components\Formatter;
use api\modules\faq\FaqModule;
use api\modules\page\PageModule;
use api\modules\globalData\GlobalDataModule;

$params = merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
//    'language' => 'en',
    'controllerNamespace' => 'api\controllers',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules' => [
        'global-data' => GlobalDataModule::class,
        'page' => PageModule::class,
        'faq' => FaqModule::class,
        'seo' => SeoModule::class,
        'request' => RequestModule::class,
        'article' => ArticleModule::class,
    ],
    'components' => [
        'request' => [
            'parsers' => ['application/json' => JsonParser::class]
        ],
        'formatter' => [
            'class' => Formatter::class
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false
        ],
        'response' => [
            'formatters' => [
                'json' => [
                    'class' => JsonResponseFormatter::class,
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
            'format' => 'json'
        ],
    ],
    'params' => $params,
];
