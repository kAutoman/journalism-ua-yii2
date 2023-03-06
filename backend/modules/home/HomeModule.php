<?php

namespace backend\modules\home;

use yii\base\Module;

/**
 * Class HomeModule
 * @package backend\modules\home
 */
class HomeModule extends Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'backend\modules\home\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        return parent::init();
    }
}
