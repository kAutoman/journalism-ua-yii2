<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=project_db',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 60 * 60 * 24,
            'enableQueryCache' => true,
            'queryCacheDuration' => 5,
            'schemaCache' => 'cacheDb',
            'schemaMap' => [
                'mysqli' => '\common\components\Schema', // MySQL
                'mysql' => '\common\components\Schema', // MySQL
            ]
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
