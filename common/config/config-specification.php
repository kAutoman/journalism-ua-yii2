<?php

use common\modules\config\domain\services\FieldFactory;

return [
    'app.name' => [
        /** Field type. @see FieldFactory::getTypesMap() for available field types. */
        'type' => FieldFactory::INPUT_TEXT,
        /** Auto translatable, no need for Yii::t() */
        'label' => 'Application name',
        /** Default value, will be fetched if value doesn't exist in storage. */
        'default' => 'My app',
        /** Currently not supported */
        'display' => false,
        /** Currently not supported */
        'autoload' => true,
        /** Auto translatable, no need for Yii::t() */
        'description' => 'Some app name description',
        /** Rules specification, as in common AR model, but without attribute setting. */
        'rules' => [
            ['required'],
            ['string', 'max' => 191],
        ],
    ],
    'app.front.domain' => [
        'type' => FieldFactory::INPUT_TEXT,
        'label' => 'Site domain',
        'default' => 'http://engine.d',
        'display' => true,
        'autoload' => true,
        'rules' => [
            ['required'],
            ['url'],
            ['string', 'max' => 191],
        ],
    ],
    'app.site.copyright' => [
        'type' => FieldFactory::INPUT_TEXT,
        'label' => 'Site copyright',
        'default' => '&copy; ' . date('Y') .' Vintage',
        'display' => true,
        'autoload' => true,
        'description' => 'Site copyright string',
        'rules' => [
            ['required'],
            ['string', 'max' => 191],
        ],
    ],
];
