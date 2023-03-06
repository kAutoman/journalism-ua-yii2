<?php

namespace common\modules\fileManager;

use yii\base\Module;

/**
 * Class FileManagerModule
 *
 * @package common\modules\fileManager
 */
class FileManagerModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'common\modules\fileManager\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
