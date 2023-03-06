<?php

namespace backend\modules\user;

use yii\base\Module;

/**
 * Class UserModule
 *
 * @package backend\modules\user
 */
class UserModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'backend\modules\user\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
