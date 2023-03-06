<?php

namespace backend\modules\auth;

use Yii;
use yii\base\Module;

/**
 * Class AuthModule
 *
 * @package backend\modules\auth
 */
class AuthModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'backend\modules\auth\controllers';

    /**
     * @var string
     */
    public $layout = '/login';

    public function init()
    {
        return parent::init();
    }
}
