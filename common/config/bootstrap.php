<?php

use common\components\I18N;
use common\components\UrlManager;
use common\helpers\LanguageHelper;
use common\validators\FileRequiredValidator;
use common\validators\MultipleValidator;

define('CLEAR_CACHE', 'clear-cache');

define('MAX_32_INT', 2147483647);
define('MIN_32_INT', -2147483647);
define('MAX_TEXT', 255);
define('MAX_TEXTAREA', 500);
define('MAX_EDITOR', 3000);
define('MAX_IMAGE_KB', 1024 * 5);
define('MAX_VIDEO_KB', 1024 * 5);
define('MAX_DOC_KB', 1024 * 4);
define('MAX_BIG_DOC_KB', 1024 * 15);
define('MAX_AUDIO_KB', 1024);
define('DOC_VALID_FORMATS', ['pdf', 'doc', 'docx']);
define('IMAGE_VALID_FORMATS', ['png', 'jpg', 'jpeg']);
define('ICON_VALID_FORMATS', ['png', 'svg']);
define('VIDEO_VALID_FORMATS', ['mp4']);
define('AUDIO_VALID_FORMATS', ['mp3']);

Yii::setAlias('root', dirname(dirname(__DIR__)));
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');


Yii::$container->set(I18N::class, function ($container, $params, $config) {
    $config['languages'] = LanguageHelper::getApplicationLanguages();
    return new I18N($config);
});

// New built-in validators.
yii\validators\Validator::$builtInValidators['multiple'] = [
    'class' => MultipleValidator::class,
];

// New built-in validators.
yii\validators\Validator::$builtInValidators['file-required'] = [
    'class' => FileRequiredValidator::class,
];
